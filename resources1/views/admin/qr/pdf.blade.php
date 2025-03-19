<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Codes</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 1px;
            padding: 0;
        }

        .header,
        .footer {
            text-align: center;
            margin-bottom: 5px;
        }

        .footer {
            margin-top: 10px;
            font-size: 10px;
            color: #888;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: center;
            border: 1px solid #ddd;
            padding: 5px;
            /* Reduced padding */
        }

        .qr-item img {
            width: 40px;
            /* Reduced size of the QR code */
            height: 40px;
            object-fit: contain;
            border: 1px solid #ddd;
            padding: 1px;
            /* Reduced padding inside the QR code box */
            background-color: #fff;
        }

        .qr-id,
        .qr-points {
            margin-top: 1px;
            /* Reduced space between QR code and text */
            font-size: 6px;
            /* Reduced font size */
            color: #333;
            font-weight: bold;
            line-height: 1;
            /* Reduced line-height to minimize vertical space */
        }
    </style>
</head>

<body>

    <div class="header">
        <div>Maa Jasol Paints</div>
        <div>Print date: {{ $generatedAt }}</div>
    </div>

    <table>
        @for ($i = 0; $i < ceil(count($qrs) / 12); $i++)
            <!-- Loop for rows, 4 QR codes per row -->
            <tr>
                @for ($j = 0; $j < 12; $j++)
                    <!-- 4 columns per row -->
                    @php
                        $index = $i * 12 + $j; // Calculate the index for each QR code
                    @endphp
                    <td>
                        @if (isset($qrs[$index]))
                            <div class="qr-item">
                                <img src="{{ public_path('images/qrcodes/' . $qrs[$index]->qr_image) }}" alt="QR Code">
                                <div class="qr-id">{{ $qrs[$index]->qr_id }}</div>
                                <div class="qr-points">{{ $qrs[$index]->points }} POINTS</div>
                            </div>
                        @else
                            <!-- Empty cell if no QR code is available -->
                            <div class="qr-item">
                                <p class="qr-points">No QR Code</p>
                            </div>
                        @endif
                    </td>
                @endfor
            </tr>
        @endfor
    </table>
    <div class="footer">
        {{ config('app.name') }} &copy; {{ now()->year }}
    </div>
</body>
</html>
