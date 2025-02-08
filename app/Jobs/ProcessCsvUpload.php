<?php

namespace App\Jobs;

use Throwable;
use App\Models\User;
use App\Models\CsvUpload;
use App\Models\LabelDetails;
use App\Models\ManageSerial;
use App\Helpers\BarcodeHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessCsvUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $uploadId;
    protected $csv_uploaded;
    protected $vendor_id;


    public function __construct($filePath, $csv_uploaded, $vendor_id)
    {
        $this->filePath = $filePath;
        $this->uploadId = $csv_uploaded->id;
        $this->vendor_id = $vendor_id;
        $this->csv_uploaded = $csv_uploaded;
    }


    public function handle()
    {
        $csvUpload = CsvUpload::find($this->uploadId);
        $csvUpload->update(['status' => 'processing', 'message' => 'Processing started...']);
    
        $user = $csvUpload->user;
        $perRowAmount = $user->per_row_amount ?? 0;
    
        if ($perRowAmount <= 0) {
            $csvUpload->update([
                'status' => 'failed',
                'message' => 'Invalid per-row amount.',
                'error_message' => 'The per-row deduction amount is zero or invalid.',
            ]);
            return;
        }
    
        $baseDir = public_path("uploads/{$this->csv_uploaded->hash}/");
        $pdfDir = $baseDir . 'pdf/';
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0755, true);
        }
    
        try {
            $file = fopen($this->filePath, 'r');
            $header = fgetcsv($file);
            
            if (!in_array('serial_number', $header)) {
                $header[] = 'serial_number';
            }
    
            $availableSerialNumbers = ManageSerial::where('is_link', 0)
                ->where('vendor_id', $this->vendor_id)
                ->count();
            
            $processedRows = 0;
            $newCsvPath = $baseDir . basename($this->filePath);
            $newCsv = fopen($newCsvPath, 'w');
            
            fputcsv($newCsv, $header);
    
            while (($row = fgetcsv($file)) !== false) {
                try {
                    if ($processedRows >= $availableSerialNumbers) {
                        break;
                    }
    
                    if ($user->wallet_amount < $perRowAmount) {
                        $csvUpload->update([
                            'status' => 'failed',
                            'message' => 'Insufficient funds',
                            'error_message' => 'Insufficient funds for wallet deduction.',
                        ]);
                        break;
                    }
    
                    $paddedRow = array_pad($row, count($header), '');
                    
                    $data = [];
                    foreach ($header as $index => $columnName) {
                        $data[$columnName] = $paddedRow[$index] ?? '';
                    }
    
                    $zipcode = !empty($data['to-zip']) ? explode("-", $data['to-zip'])[0] : '';
    
                    $serial_number = null;
                    DB::transaction(function () use (&$serial_number) {
                        $serial_number = ManageSerial::where('is_link', 0)
                            ->where('vendor_id', $this->vendor_id)
                            ->lockForUpdate()
                            ->first();
                        
                        if ($serial_number) {
                            $serial_number->update(['is_link' => 1]);
                        }
                    });
    
                    if (!empty($zipcode) && !empty($serial_number)) {
                        $barcode_number = implode(' ', [
                            substr($serial_number->serial_number, 0, 4),
                            substr($serial_number->serial_number, 4, 4),
                            substr($serial_number->serial_number, 8, 4),
                            substr($serial_number->serial_number, 12, 4),
                            substr($serial_number->serial_number, 16, 4),
                            substr($serial_number->serial_number, 20, 2)
                        ]);
    
                        $barcodes = BarcodeHelper::generateGS1Barcode(
                            $serial_number->serial_number, 
                            $zipcode, 
                            "uploads/{$this->csv_uploaded->hash}/", 
                            $data
                        );
    
                        $data = array_merge($data, $barcodes);
                        $data['serial_number'] = $barcode_number;
                        
                        $outputRow = [];
                        foreach ($header as $columnName) {
                            $outputRow[] = $data[$columnName] ?? '';
                        }
                        
                        fputcsv($newCsv, $outputRow);
                        
                        $processedRows++;
    
                        $user->wallet_amount -= $perRowAmount;
                        $user->save();
    
                        LabelDetails::create($data);
    
                        $csvUpload->increment('processed_rows');
                    }
                } catch (\Exception $e) {
                    Log::error("Row processing failed: " . $e->getMessage());
                    continue; 
                }
            }
    
        } catch (\Exception $e) {
            $csvUpload->update([
                'status' => 'failed',
                'message' => 'CSV Processing failed',
                'error_message' => $e->getMessage(),
            ]);
            Log::error("CSV Processing failed: " . $e->getMessage());
        } finally {
            if (isset($file) && is_resource($file)) {
                fclose($file);
            }
            if (isset($newCsv) && is_resource($newCsv)) {
                fclose($newCsv);
            }
        }
    
        $this->createZipFile($this->csv_uploaded->hash);
    
        if ($csvUpload->status !== 'failed') {
            $csvUpload->update([
                'status' => 'completed',
                'message' => 'Ready to download',
            ]);
        }
    }

    protected function createZipFile($csvUploadedPath)
    {
        $zip = new \ZipArchive();
        $zipFileName = "uploads/{$csvUploadedPath}/{$csvUploadedPath}.zip";
        $baseDir = public_path("uploads/{$csvUploadedPath}/");

        // Ensure the directory for the ZIP file exists
        if (!is_dir($baseDir)) {
            Log::error("Directory does not exist: $baseDir");
            return;
        }

        // Create the ZIP file
        if ($zip->open(public_path($zipFileName), \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            // Helper function to add files recursively
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($baseDir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($baseDir));

                // Add the file to the ZIP archive
                $zip->addFile($filePath, $relativePath);
            }

            $zip->close();
        } else {
            Log::error("Failed to create ZIP file at: $zipFileName");
        }
    }





    public function failed(Throwable $exception)
    {
        Log::error('CSV Processing failed: ' . $exception->getMessage());

        $csvUpload = CsvUpload::find($this->uploadId);
        if ($csvUpload) {
            $csvUpload->update([
                'status' => 'failed',
                'message' => 'Processing failed',
                'error_message' => $exception->getMessage(),
            ]);
        }
    }
}
