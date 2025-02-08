<?php

namespace App\Helpers;

use TCPDF2DBarcode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorPNG;
use setasign\Fpdi\Tcpdf\Fpdi;

class BarcodeHelper
{
    private static $vendorTemplates = [
        'Ground Easypost' => 'labels.ground-easypost',
        'Priority Mail Rollo' => 'labels.priority-mail-pollo',
        'Ground Rollo' => 'labels.ground-rollo',
        'Easypost' => 'labels.shipping-label',
    ];

    public static function generateGS1Barcode($serialNumber, $zipCode, $vendor, $uploadPath, $rowData)
    {
        try {
            $fullUploadPath = self::prepareDirectories($uploadPath);
            $storageBasePath = self::prepareStorageDirectories();

            $zipCode = str_pad($zipCode, 5, '0', STR_PAD_LEFT);

            list($barcodePathGS128, $barcodePathGS1DataMatrix) = self::generateBarcodes($serialNumber, $zipCode, $storageBasePath);

            $pdfData = self::preparePdfData($rowData, $barcodePathGS128, $barcodePathGS1DataMatrix, $serialNumber, $zipCode);

            $pdf = self::generatePdf($vendor, $pdfData);

            $pdfFileName = self::savePdf($pdf, $fullUploadPath, $serialNumber, $rowData);

            self::mergePDFs($fullUploadPath . DIRECTORY_SEPARATOR . 'pdf', $fullUploadPath . DIRECTORY_SEPARATOR . 'merged.pdf');

            return [
                'pdf_path' => "{$uploadPath}pdf/{$pdfFileName}",
                'merged_pdf_path' => "{$uploadPath}merged.pdf"
            ];
        } catch (\Exception $e) {
            Log::error('Error in generateGS1Barcode: ' . $e->getMessage());
            throw $e;
        }
    }

    private static function prepareDirectories($uploadPath)
    {
        $fullUploadPath = public_path($uploadPath);
        if (!file_exists($fullUploadPath . DIRECTORY_SEPARATOR . 'pdf')) {
            mkdir($fullUploadPath . DIRECTORY_SEPARATOR . 'pdf', 0755, true);
        }
        return $fullUploadPath;
    }

    private static function prepareStorageDirectories()
    {
        $storageBasePath = storage_path('app/public/qrcode-and-bardcode');
        $directories = ['barcode', 'qrcode'];
        foreach ($directories as $dir) {
            $dirPath = $storageBasePath . DIRECTORY_SEPARATOR . $dir;
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0755, true);
            }
        }
        return $storageBasePath;
    }

    private static function generateBarcodes($serialNumber, $zipCode, $storageBasePath)
    {
        $firstPart = "420" . $zipCode;
        $fnc1 = "\xF1";
        $fnc1_2d = "\x1D";
        $secondPart = $serialNumber;
        $barcodeData = $fnc1 . $firstPart . $fnc1 . $secondPart;
        $barcodeDataMatrix = $fnc1_2d . $firstPart . $fnc1_2d . $secondPart;

        $generator = new BarcodeGeneratorPNG();
        $barcodeSVG = $generator->getBarcode($barcodeData, $generator::TYPE_CODE_128, 2, 100);

        $randomNameGS128 = time() . '-' . uniqid() . '.png';
        $barcodePathGS128 = $storageBasePath . DIRECTORY_SEPARATOR . 'barcode' . DIRECTORY_SEPARATOR . $randomNameGS128;
        file_put_contents($barcodePathGS128, $barcodeSVG);

        $dataMatrix = new TCPDF2DBarcode($barcodeDataMatrix, 'DATAMATRIX');
        $barcodeSVGGS1DataMatrix = $dataMatrix->getBarcodePngData(8, 8);

        $randomNameGS1DataMatrix = time() . '-' . uniqid() . '.png';
        $barcodePathGS1DataMatrix = $storageBasePath . DIRECTORY_SEPARATOR . 'qrcode' . DIRECTORY_SEPARATOR . $randomNameGS1DataMatrix;
        file_put_contents($barcodePathGS1DataMatrix, $barcodeSVGGS1DataMatrix);

        return [$barcodePathGS128, $barcodePathGS1DataMatrix];
    }

    private static function preparePdfData($rowData, $barcodePathGS128, $barcodePathGS1DataMatrix, $serialNumber, $zipCode)
    {
        $barcodeBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($barcodePathGS128));
        $qrcodeBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($barcodePathGS1DataMatrix));
        $easypost_logo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/img/logo.png')));

        return [
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
            'tracking_number' => strtoupper("420" . $zipCode . $serialNumber),
            'barcode_number' => implode(' ', str_split($serialNumber, 4)),
            'barcode_url' => $barcodeBase64,
            'easypost_logo' => $easypost_logo,
            'qr_code' => $qrcodeBase64,
            'reference_number' => strtoupper($rowData['reference'] ?? "0"),
            'cost_code' => strtoupper("C{$rowData['reference']}" ?? "0"),
            'to_state' => strtoupper($rowData['to-state'] ?? "0"),
        ];
    }

    private static function generatePdf($vendor, $pdfData)
    {
        $vendorName = trim($vendor->name);
        if (!isset(self::$vendorTemplates[$vendorName])) {
            throw new \Exception("Vendor template not found for: {$vendorName}");
        }

        $pdf = PDF::loadView(self::$vendorTemplates[$vendorName], $pdfData);
        if (!$pdf) {
            throw new \Exception('Failed to generate PDF.');
        }

        return $pdf;
    }

    private static function savePdf($pdf, $fullUploadPath, $serialNumber, $rowData)
    {
        $sanitizedToName = preg_replace('/[^A-Za-z0-9]/', '', $rowData['to-name'] ?? '');
        $pdfFileName = $serialNumber . '_' . $sanitizedToName . '.pdf';
        $pdfFullPath = $fullUploadPath . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $pdfFileName;
        $pdf->save($pdfFullPath);

        return $pdfFileName;
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