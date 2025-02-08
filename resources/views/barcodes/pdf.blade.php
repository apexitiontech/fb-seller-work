<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barcode PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .label {
            border: 1px solid #000;
            padding: 20px;
            width: 500px;
            page-break-inside: avoid;
        }
        .barcode {
            text-align: center;
            margin-top: 20px;
        }
        .address {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="label">
        <h3>Priority Mail</h3>
        <p><strong>From:</strong> {{ $barcodeData['From_Name'] ?? 'Unknown Sender' }}</p>
        <p><strong>To:</strong> {{ $barcodeData['To_name'] ?? 'Unknown Recipient' }}</p>
        <p>{{ $barcodeData['To_Address1'] ?? 'Address Not Provided' }}</p>
        <p>{{ $barcodeData['To_City'] ?? '' }}, {{ $barcodeData['To_State'] ?? '' }} {{ $barcodeData['To_Zipcode'] ?? '' }}</p>

        <div class="barcode">
            <h4>Barcode</h4>
            <div>{{ $barcode }}</div>
        </div>
    </div>
</body>
</html>
