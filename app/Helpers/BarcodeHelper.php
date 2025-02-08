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

class BarcodeHelper
{
    public static function generateGS1Barcode($serialNumber, $zipCode, $uploadPath, $rowData)
    {
        $fullUploadPath = public_path($uploadPath);
   
        // Create directories if they don't exist
        $directories = ['barcode', 'qrcode', 'pdf'];
        foreach ($directories as $dir) {
            $path = $fullUploadPath . DIRECTORY_SEPARATOR . $dir;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        }

        $zipCode = str_pad($zipCode, 5, '0', STR_PAD_LEFT);

        // Generate barcode as per GS1-128 format with fnc1 format
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

        $generator = new BarcodeGeneratorPNG();
        $barcodeSVG = $generator->getBarcode($barcodeData, $generator::TYPE_CODE_128, 2, 100);

        // Save SVG file
        $randomNameGS128 = time() . '-' . uniqid() . '.png';
        $barcodePathGS128 = $fullUploadPath . DIRECTORY_SEPARATOR . 'barcode' . DIRECTORY_SEPARATOR . $randomNameGS128;
        file_put_contents($barcodePathGS128, $barcodeSVG);

        // Generate DataMatrix QR Code
        $dataMatrix = new TCPDF2DBarcode($barcodeDataMatrix, 'DATAMATRIX');
        $barcodeSVGGS1DataMatrix = $dataMatrix->getBarcodePngData(8, 8);

        // Save DataMatrix as png
        $randomNameGS1DataMatrix = time() . '-' . uniqid() . '.png';
        $barcodePathGS1DataMatrix = $fullUploadPath . DIRECTORY_SEPARATOR . 'qrcode' . DIRECTORY_SEPARATOR . $randomNameGS1DataMatrix;
        file_put_contents($barcodePathGS1DataMatrix, $barcodeSVGGS1DataMatrix);

        // Generate PDF
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
            Log::info('error in pdf generation');
            throw new \Exception('Failed to generate PDF.');
        }
        $randomNamePDF = time() . '-' . uniqid() . '.pdf';
        $pdfPath = $fullUploadPath . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $randomNamePDF;
        $pdf->save($pdfPath);
      

        return [
            'barcode_path_gs128' => "{$uploadPath}barcode/{$randomNameGS128}",
            'barcode_path_gs1_datamatrix' => "{$uploadPath}qrcode/{$randomNameGS1DataMatrix}",
            'pdf_path' => "{$uploadPath}pdf/{$randomNamePDF}"
        ];
    }

}
