<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BarcodeHelper
{
    public static function generateAndStoreBarcodes($serialNumber, $uploadPath)
    {
        $barcodePathGS128 = '';

        // Ensure the upload directory exists
        if (!file_exists(public_path($uploadPath))) {
            mkdir(public_path($uploadPath), 0755, true);
        }

        // Generate GS1-128 barcode using TEC-IT API with the required specifications
        $responseGS128 = Http::get('https://barcode.tec-it.com/barcode.ashx', [
            'data' => $serialNumber,
            'code' => 'GS1-128',
            'translate-esc' => true,
            'multiplebarcodes' => false,
            'showhrt' => false, // Hide human-readable text
            'dpi' => 200,      // Set resolution to 600 dpi
            'modulewidth' => 0.013, // Module width in inches
            'unit' => 'inch',  // Use inches as the measurement unit
            'width' => 2.975,  // Width in inches
            'height' => 0.633, // Height in inches
        ]);

        if ($responseGS128->successful()) {
            $barcodeImageGS128 = $responseGS128->body();
            $randomNameGS128 = time() . '-' . uniqid() . '.png';
            $barcodePathGS128 = public_path($uploadPath . 'barcode/' . $randomNameGS128);

            // Ensure the 'barcode' directory exists
            if (!file_exists(public_path($uploadPath . 'barcode'))) {
                mkdir(public_path($uploadPath . 'barcode'), 0755, true);
            }

            // Save the file directly to the specified path
            file_put_contents($barcodePathGS128, $barcodeImageGS128);
        }

        return [
            'barcode_path_gs128' => "{$uploadPath}barcode/{$randomNameGS128}",
        ];
    }
}
