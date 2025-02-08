<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style type="text/css">
        @font-face {
            font-family: 'Helvetica Neue';
            src: url('../fonts/HelveticaNeueRoman.otf') format('opentype'),
                url('../fonts/HelveticaNeueItalic.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Helvetica Neue';
            src: url('../fonts/HelveticaNeueBold.otf') format('opentype');
            font-weight: bold;
            font-style: normal;
        }

        body {
            font-family: 'Helvetica Neue', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            text-indent: 0;
            text-transform: uppercase !important;
        }

        p {
            color: black;
            font-family: 'Helvetica Neue', sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 10.5pt;
            margin: 0pt;
        }

        h1 {
            color: black;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 19.5pt;
        }

        h2 {
            color: black;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 12.5pt;
        }

        h3 {
            color: black;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 11.5pt;
        }

        h4 {
            color: black;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 9.5pt;
        }
    </style>
</head>

<body>
    <table style="border:1px solid black; width: 360px;" cellspacing="0">
        <tbody>
            <tr>
                <td style="border-right:2px solid black;">
                    <p style="padding-top: 4pt;padding-left: 30pt;text-indent: 0pt;text-align: left;">
                        <span
                            style="color: black; font-family:&quot;Arial Narrow&quot;, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 60pt;">P</span>
                    </p>
                </td>
                <td>
                    <div class="textbox" style="border:2px solid #000000; width: 60%;margin: 1rem;float: right;">
                        <p style="margin: 2px;">
                            <span
                                style="color: black; font-family:Arial Narrow, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 9.5pt;">U.S.
                                POSTAGE PAID ROLLO</span>
                        </p>
                        <p style="margin: 2px;">
                            <span
                                style="color: black; font-weight: bold; text-decoration: none; font-size: 9.5pt;">ePostage</span>
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="border-top: 2px solid black;margin:auto;padding: 0.3em;" colspan="2">
                    <h1 style="padding-left: 9pt;text-indent: 0pt;text-align: center;">PRIORITY MAIL</h1>
                </td>
            </tr>
            <tr>
                <td style="border-top: 2px solid black;margin:auto;padding: 0.3em;">
                    <p style="padding-left: 9pt;text-indent: 0pt;text-align: left; margin-top:7px">
                        {{ $invoice->from_name }}</p>
                    <p style="padding-left: 9pt;text-indent: 0pt;text-align: left;">{{ $invoice->from_address1 }}</p>
                    <p style="padding-left: 9pt;text-indent: 0pt;line-height: 12pt;text-align: left;">
                        {{ $invoice->from_city . ' ' . $invoice->from_state . ' ' . $invoice->from_postcode }}
                    </p>
                </td>
                <td style="border-top: 2px solid black;margin:auto;padding: 0.3em;padding-right:14px;">
                    <p style="margin-top:-20px;text-align:right;">Ship Date: {{ date('d-m-Y') }}</p>
                    <p style="text-align:right;">Weight: {{ $invoice->weight }}</p>
                    <h2 style="text-align:right;">0001</h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="margin-top: 4rem;height: 3rem;"></td>
            </tr>
            <tr>
                <td style="padding:0 1rem;" colspan="2">
                    <span>
                        @php
                            $barcodePath = public_path($invoice->barcode_path_gs1_datamatrix);
                        @endphp
                        <img width="52" height="52"
                            src="{{ file_exists($barcodePath) ? \App\Helpers\Helper::base64_img($barcodePath) : '' }}">
                    </span>
                    <span style="display:inline-block; width:80%;">
                        <h2>{{ $invoice->to_name }}</h2>
                        <h2>{{ $invoice->to_address1 }}</h2>
                        <h2>{{ $invoice->to_city . ' ' . $invoice->to_state . ' ' . $invoice->to_postcode }}</h2>
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-top: 2px solid black;padding: 1rem; text-align: center;">
                    <h3>USPS TRACKING # EP</h3>
                    @php
                        $trackingBarcodePath = public_path($invoice->barcode_path_gs128);
                    @endphp
                    <img width="286" height="61"
                        src="{{ file_exists($trackingBarcodePath) ? \App\Helpers\Helper::base64_img($trackingBarcodePath) : '' }}" />
                </td>
            </tr>
            <tr>
                <td style="border-top: 2px solid black;padding: 1rem;">
                    <h4 style="text-align: left;">50797</h4>
                </td>
                <td style="border-top: 2px solid black;padding: 1rem;text-align: right;">
                    <img width="52" height="52"
                        src="{{ file_exists($barcodePath) ? \App\Helpers\Helper::base64_img($barcodePath) : '' }}">
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
