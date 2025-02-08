<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .label {
            border: 1px solid black;
            padding: 20px;
            margin: 10px auto;
            width: 500px;
            page-break-inside: avoid;
        }
        .label-header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        .label-details {
            margin-top: 20px;
        }
        .label-details div {
            margin-bottom: 5px;
        }
        .barcode {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="label">
        <div class="label-header">PRIORITY MAIL</div>
        <div class="label-details">
            <div><strong>To:</strong> {{ $label['To_name'] }}</div>
            <div>{{ $label['To_Addre'] }}</div>
            <div>{{ $label['To_City'] }}, {{ $label['To_State'] }} {{ $label['To_Zipcod'] }}</div>
            <div><strong>Weight:</strong> {{ $label['Weight'] }} lb</div>
        </div>
        <div class="barcode">
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($label['Serials'], 'C128') }}" alt="Barcode">
            <div>{{ $label['Serials'] }}</div>
        </div>
    </div>
</body>
</html>
