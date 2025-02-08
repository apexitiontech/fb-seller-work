<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Ground Easy Post</title>
    <style>
        @page {
            size: 4in 6in;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 4in;
            height: 6in;
            font-weight: bold;
        }

        .label-container {
            border: 1px solid #000;
            height: 5.9in;
            position: relative;
            box-sizing: border-box;
            padding: 0;
            margin: 0;
            overflow: hidden;
            page-break-inside: avoid;
            border-bottom: unset;
        }

        .header {
            width: 100%;
            height: 70px;
            border-collapse: collapse;
        }

        .priority-mark {
            font-size: 85px;
            font-weight: bolder;
            border: 1px solid #000;
            padding: 0;
            width: 90px;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
        }

        .priority-text-container {
            text-align: center;
            font-size: 12px;
            line-height: 1.2;
            font-weight: bold;
            padding: 2px 1px;
            width: 40%;
            border: 1px solid #000;
        }

        .priority-text-container1 {
            text-align: center;
            font-size: 8pt !important;
            line-height: 1.2;
            padding: 2px 1px;
            margin-left: 163px;
            width: 38%;
            border: 1px solid #000;
        }


        .main-title {
            text-align: center;
            font-weight: bold;
            padding: 6px;
            border-bottom: 1px solid #000;
            font-size: 16px;
        }

        .content {
            padding: 15px 3px 0 7px;
            position: relative;
        }

        .from-address {
            font-size: 12px;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .shipping-details {
            position: absolute;
            right: 12px;
            top: 13px;
            text-align: right;
            font-size: 9pt;
            line-height: 1.4;
        }

     

        .cost-code {
            border: 1px solid #000;
            font-size: 10pt;
            padding: 2px 5px;
            display: inline-block;
            margin-top: 18px;
            margin-left: 250px;


        }

        .ship-to-container {
            display: flex;
            align-items: center;
            font-size: 12px;
        }

        .ship-to {
            font-weight: medium;
            margin-right: 10px;
            margin-bottom: 5px;
        }

        .ship-to-title {
            display: inline-block;
            font-size: 10pt;
        }

        .ship-to-name,
        .ship-to-address {
            display: inline-block;
        }

        .ship-to-name {
            display: inline-block;
            font-size: 9pt;
        }
        
        .ship-to-address {
            position: absolute;
            top: 78px;
            font-size: 9pt;
            left: 60px;
        }

        .qr-small {
            margin-right: 5px;
        }

        .qr-small {
            display: inline-block;
            vertical-align: top;
            margin-right: 10px;
        }

        .tracking-section {
            position: absolute;
            bottom: 60px;
            text-align: center;
            padding: 3px 45px;
            border-top: 4px solid #000;
            border-bottom: 4px solid #000;

        }

        .tracking-title {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 10px;
            display: inline-block;
        }

        .barcode {
            width: 100%;
            height: 80px;
            margin-bottom: 5px;
        }

        .tracking-number {
            font-size: 12pt;
            font-weight: bold;
        }

        .footer {
            position: absolute;
            top: 515px !important;
            z-index: 999999999999999999999999;
            width: 100%;
            box-sizing: border-box;
        }

     

        .qr-code {
            width: 35px;
            height: 35px;
            margin-top: 20px;
            display: inline-block;
            margin-left: 280px;
        }
        .reference-number {
            display: inline-block;
            vertical-align:center ;
            vertical-align:middle ;
            margin-top: 0px;
            font-weight: bold;
            margin-left: 15px;
            font-size: 9pt;
        }
        .font-big {
            margin-top: 0px;
            font-size: 14pt;
            font-weight: 400
        }
    </style>
</head>

<body>
    <div class="label-container">
        <table class="header">
            <tr>
                <td class="priority-mark">G</td>
                <td class="priority-text-container">
                    <div class="priority-text-container1">
                        U.S. POSTAGE PAID<br>
                        ROLLO<br>
                        ePostage
                    </div>
                </td>
            </tr>
        </table>

        <div class="main-title"> GROUND ADVANTAGE <sup>TM</sup></div>


        <div class="content">
            <div class="from-address">
                <div class="div">
                    {{ $from_name }}
                </div>
                <div class="div">
                    {{ $from_street }}
                </div>
                <div class="div">
                    {{ $from_full_address }}
                </div>
            </div>

            <div class="shipping-details">
                Ship Date: {{ $ship_date }}<br>
                Weight: {{ $weight }}<br>
                 {{ $cost_code }}
            </div>

            <div class="ship-to-container">
                <div class="ship-to1">
                    <img src="{{ $qr_code }}" class="qr-small" alt="Address QR" width="40" height="40">
                    <div class="ship-to-address">
                        {{ $to_address1 }}

                        @if ($to_address2 != '')
                            <br>
                            {{ $to_address2 }}
                        @endif
                        <br>
                        <div class="font-big">

                            {{ $to_full_address }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tracking-section">
            <div class="tracking-title">USPS TRACKING #EP</div>
            <img src="{{ $barcode_url }}" class="barcode" alt="Tracking Barcode">
            <div class="tracking-number">{{ $barcode_number }}</div>
        </div>
        <div class="footer">
            <div class="reference-number">{{ $reference_number }}</div>
            <img src="{{ $qr_code }}" alt="QR Code" class="qr-code">
        </div>
    </div>
</body>

</html>
