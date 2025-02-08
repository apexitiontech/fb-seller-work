<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use ZipArchive;
use App\Models\BarcodeUpload;
use App\Models\ManageSerial;

use League\Csv\Reader;
// use Milon\Barcode\Facades\DNS1D;
use DNS1D; // For barcodes
use DNS2D; // For QR codes
// use Illuminate\Support\Facades\Storage;

class BarcodeController extends Controller
{
    /**
     * Show the barcode upload form.
     */
    public function showUploadForm()
    {
        return view('barcodes.upload');
    }

    /**
     * Process the uploaded CSV and generate barcodes.
     */
    public function processBarcodes(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        // Read the CSV file
        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));

        // Extract the header and rows
        $header = array_map('trim', $csvData[0]);
        $rows = array_slice($csvData, 1);

        // Validate CSV structure
        if (!in_array('To_Zipcode', $header)) {
            return back()->withErrors(['csv_file' => 'The CSV file is missing required columns: To_Zipcode.']);
        }

        // Fetch available serials from the database
        $serials = ManageSerial::where('is_link', 0)->take(count($rows))->get();

        // Validate if enough serials are available
        if ($serials->count() < count($rows)) {
            return back()->withErrors(['csv_file' => 'Not enough serial numbers available in the database.']);
        }

        // Map the data to an associative array and attach serials
        $barcodes = [];
        foreach ($rows as $index => $row) {
            $barcodeData = array_combine($header, $row);
            $barcodeData['Serials'] = $serials[$index]->serial_number; // Attach serial from database
            $barcodes[] = $barcodeData;

            // Mark the serial as used
            $serials[$index]->update(['is_link' => 1]);
        }

        // Create a unique folder for storing barcodes
        $hash = Str::random(16);
        $folderPath = storage_path("app/public/barcodes/{$hash}");
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }
    //   dd($barcodes);
        // Generate PDFs for each barcode
        foreach ($barcodes as $index => $barcodeData) {
            $fnc1 = "\x1D"; // ASCII Group Separator for FNC1
            $barcodeText = $fnc1 . "420" . str_pad($barcodeData['To_Zipcode'], 5, '0', STR_PAD_LEFT) . $barcodeData['Serials'];
             $barcode = DNS1D::getBarcodeHTML($barcodeText, 'C128'); // Barcode for the 'Reference' field
			$qrCode = DNS2D::getBarcodeHTML($barcodeText, 'QRCODE'); // QR code for the 'Reference' field
            // Create the PDF
            $pdf = Pdf::loadView('barcodes.pdf', compact('barcodeData', 'barcodeText'));
            $pdf->save("{$folderPath}/barcode_{$index}.pdf");
        }

        // Create a ZIP file of the PDFs
        $zip = new ZipArchive();
        $zipPath = "{$folderPath}.zip";
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = glob($folderPath . '/*.pdf');
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Clean up individual PDFs
        array_map('unlink', glob("{$folderPath}/*"));
        rmdir($folderPath);

        // Save upload details to the database
        BarcodeUpload::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $zipPath,
            'total_rows' => count($barcodes),
            'hash' => $hash,
            'uploaded_by' => auth()->user()->id,
        ]);

        return back()->with('success', 'Barcodes processed and ZIP file created successfully.');
    }

    /**
     * Get the list of uploaded barcodes.
     */
    public function getUploadedBarcodes()
    {
        $uploads = BarcodeUpload::orderBy('created_at', 'desc')->get();
        return view('barcodes.list', compact('uploads'));
    }

    /**
     * Generate a sample barcode PDF.
     */
    public function generateBarcodePDF(Request $request)
    {
        $barcodeData = [
            'From_Name' => 'John Doe',
            'To_Name' => 'Jane Smith',
            'To_Address' => '123 Example St',
            'To_City' => 'Sample City',
            'To_State' => 'CA',
            'To_Zipcode' => '90210',
        ];

        $fnc1 = "\x1D"; // ASCII Group Separator for FNC1
        $barcodeText = $fnc1 . "420" . str_pad($barcodeData['To_Zipcode'], 5, '0', STR_PAD_LEFT) . "123456789";

        $pdf = Pdf::loadView('barcodes.pdf', compact('barcodeData', 'barcodeText'));
        return $pdf->download('barcode.pdf');
    }
}
