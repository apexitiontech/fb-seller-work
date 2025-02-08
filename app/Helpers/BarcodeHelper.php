<?php

namespace App\Helpers;

use TCPDF;
use TCPDF2DBarcode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use setasign\Fpdi\Tcpdf\Fpdi;

class BarcodeHelper
{
    public static function generateGS1Barcode($serialNumber, $zipCode, $uploadPath, $rowData)
    {
        try {
            // Original upload path for PDFs
            $fullUploadPath = public_path($uploadPath);
            if (!file_exists($fullUploadPath . DIRECTORY_SEPARATOR . 'pdf')) {
                mkdir($fullUploadPath . DIRECTORY_SEPARATOR . 'pdf', 0755, true);
            }

            // Storage path for barcodes and QR codes
            $storageBasePath = storage_path('app/public/qrcode-and-bardcode');
            $directories = ['barcode', 'qrcode'];
            foreach ($directories as $dir) {
                $dirPath = $storageBasePath . DIRECTORY_SEPARATOR . $dir;
                if (!file_exists($dirPath)) {
                    mkdir($dirPath, 0755, true);
                }
            }

            $zipCode = str_pad($zipCode, 5, '0', STR_PAD_LEFT);
            
            // Generate barcode data
            $firstPart = "420" . $zipCode;
            $fnc1 = "\xF1";
            $fnc1_2d = "\x1D";
            $secondPart = $serialNumber;
            $fullNumber = $firstPart . $secondPart;
            $barcodeData = $fnc1 . $firstPart . $fnc1 . $secondPart;
            $barcodeDataMatrix = $fnc1_2d . $firstPart . $fnc1_2d . $secondPart;
            
            $barcode_number = implode(' ', [
                substr($serialNumber, 0, 4),
                substr($serialNumber, 4, 4),
                substr($serialNumber, 8, 4),
                substr($serialNumber, 12, 4),
                substr($serialNumber, 16, 4),
                substr($serialNumber, 20, 2)
            ]);

            // Generate and save barcode
            $generator = new BarcodeGeneratorPNG();
            $barcodeSVG = $generator->getBarcode($barcodeData, $generator::TYPE_CODE_128, 2, 100);
            
            $randomNameGS128 = time() . '-' . uniqid() . '.png';
            $barcodePathGS128 = $storageBasePath . DIRECTORY_SEPARATOR . 'barcode' . DIRECTORY_SEPARATOR . $randomNameGS128;
            file_put_contents($barcodePathGS128, $barcodeSVG);

            // Generate and save QR code
            $dataMatrix = new TCPDF2DBarcode($barcodeDataMatrix, 'DATAMATRIX');
            $barcodeSVGGS1DataMatrix = $dataMatrix->getBarcodePngData(8, 8);
            
            $randomNameGS1DataMatrix = time() . '-' . uniqid() . '.png';
            $barcodePathGS1DataMatrix = $storageBasePath . DIRECTORY_SEPARATOR . 'qrcode' . DIRECTORY_SEPARATOR . $randomNameGS1DataMatrix;
            file_put_contents($barcodePathGS1DataMatrix, $barcodeSVGGS1DataMatrix);

            // Generate PDF data
            $barcodeBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($barcodePathGS128));
            $qrcodeBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($barcodePathGS1DataMatrix));
            $easypost_logo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/img/logo.png')));

            $pdfData = [
                'from_address' => strtoupper($rowData['from-address1'] ?? ''),
                'to_address1' => strtoupper($rowData['to-address1'] ?? ''),
                'to_address2' => strtoupper($rowData['to-address2'] ?? ''),
                'from_name' => strtoupper($rowData['from-name'] ?? 'Custom'),
                'from_street' => strtoupper($rowData['from-address1'] ?? ''),
                'from_full_address' => strtoupper(($rowData['from-city'] ?? '') . ' ' . ($rowData['from-state'] ?? '') . ' ' . ($rowData['from-zip'] ?? '')),
                'to_name' => strtoupper($rowData['to-name'] ?? ''),
                'to_street' => strtoupper($rowData['to-address'] ?? ''),
                'to_full_address' => strtoupper(($rowData['to-city'] ?? '') . ' ' . ($rowData['to-state'] ?? '') . ' ' . ($rowData['to-zip'] ?? '')),
                'ship_date' => date('m/d/Y'),
                'weight' => strtoupper("{$rowData['weight']} lb" ?? '1 lb'),
                'dimensions' => strtoupper($rowData['dimensions'] ?? '15x4x8'),
                'tracking_number' => strtoupper($fullNumber),
                'barcode_number' => $barcode_number,
                'barcode_url' => $barcodeBase64,
                'easypost_logo' => $easypost_logo,
                'qr_code' => $qrcodeBase64,
                'reference_number' => strtoupper($rowData['reference'] ?? "0"),
                'cost_code' => strtoupper("C{$rowData['reference']}" ?? "0"),
                'to_state' => strtoupper($rowData['to-state'] ?? "0"),
            ];

            $pdf = PDF::loadView('labels/shipping-label', $pdfData);
            if (!$pdf) {
                throw new \Exception('Failed to generate PDF.');
            }

            $sanitizedToName = preg_replace('/[^A-Za-z0-9]/', '', $rowData['to-name'] ?? '');
            $pdfFileName = $serialNumber . '_' . $sanitizedToName . '.pdf';
            $pdfFullPath = $fullUploadPath . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $pdfFileName;
            $pdf->save($pdfFullPath);

            self::mergePDFs(
                $fullUploadPath . DIRECTORY_SEPARATOR . 'pdf', 
                $fullUploadPath . DIRECTORY_SEPARATOR . 'merged.pdf'
            );

            return [
                'pdf_path' => "{$uploadPath}pdf/{$pdfFileName}",
                'merged_pdf_path' => "{$uploadPath}merged.pdf"
            ];

        } catch (\Exception $e) {
            Log::error('Error in generateGS1Barcode: ' . $e->getMessage());
            throw $e;
        }
    }

    private static function mergePDFs($sourceDirectory, $outputFilePath)
    {
        try {
            $pdf = new Fpdi();
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
    
            $width = 4 * 72; 
            $height = 6 * 72;  
    
            $files = glob($sourceDirectory . DIRECTORY_SEPARATOR . '*.pdf');
            if (empty($files)) {
                Log::warning('No PDF files found to merge in: ' . $sourceDirectory);
                return;
            }
    
            foreach ($files as $file) {
                try {
                    $pageCount = $pdf->setSourceFile($file);
                    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        $templateId = $pdf->importPage($pageNo);
                        
                        $pdf->AddPage('P', array($width, $height));
                        
                        $pdf->useTemplate($templateId, 0, 0, $width, $height);
                    }
                } catch (\Exception $e) {
                    Log::error('Error processing PDF file ' . $file . ': ' . $e->getMessage());
                    continue;
                }
            }
    
            $pdf->Output($outputFilePath, 'F');
        } catch (\Exception $e) {
            Log::error('Error in mergePDFs: ' . $e->getMessage());
            throw $e;
        }
    }
}