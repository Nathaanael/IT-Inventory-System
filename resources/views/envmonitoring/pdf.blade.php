<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Monitoring Lingkungan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .kop-surat { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .kop-surat p { margin: 2px 0; }
        .chart-container { margin-bottom: 5px; text-align: center; page-break-inside: avoid; }
        .chart-container img { max-width: 100%; height: auto; border: 1px solid #ddd; padding: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <div class="kop-surat" style="text-align: center; border-bottom: none; margin-bottom: 20px;">
        <!-- Placeholder untuk Logo Indofood -->
        <div style="display: inline-block; width: 150px; height: 60px; border: 1px dashed #999; text-align: center; line-height: 60px; font-weight: bold; background: #eee;">
            <img src="{{ asset('images/logo/logo_ind.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
        </div>
    </div>

    <h2 style="text-align: center; margin-bottom: 15px;">Laporan Monitoring Lingkungan (Suhu & Kelembaban)</h2>
    
    <table style="border: none; margin-bottom: 20px; width: 100%;">
        <tr>
            <td style="border: none; padding: 2px; width: 150px;"><strong>Periode Filter</strong></td>
            <td style="border: none; padding: 2px;">: {{ strtoupper($period) }}</td>
        </tr>
        <tr>
            <td style="border: none; padding: 2px;"><strong>Tanggal Cetak</strong></td>
            <td style="border: none; padding: 2px;">: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}</td>
        </tr>
    </table>


    @if($tempImage)
    <div class="chart-container">
        <h3 style="margin-bottom: 5px;">Grafik Suhu</h3>
        <img src="{{ $tempImage }}" alt="Grafik Suhu" />
    </div>
    @endif

    @if($humidImage)
    <div class="chart-container">
        <h3 style="margin-bottom: 30px;">Grafik Kelembaban</h3>
        <img src="{{ $humidImage }}" alt="Grafik Kelembaban" />
    </div>
    @endif

    <div style="margin-top: 25px; page-break-inside: avoid;">
        <h3 style="margin-bottom: 5px; text-align: center;">Ringkasan Data Suhu</h3>
        <table style="margin-bottom: 25px; width: 100%; border-collapse: collapse;">
            <tr>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; background-color: #f2f2f2;">Suhu Tertinggi (Max)</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; background-color: #f2f2f2;">Suhu Terendah (Min)</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; background-color: #f2f2f2;">Suhu Rata-rata (Avg)</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; background-color: #f2f2f2;">Kejadian Suhu "BAHAYA"</th>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $summary['maxTemp'] }} °C</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $summary['minTemp'] }} °C</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $summary['avgTemp'] }} °C</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center; {{ $summary['dangerCount'] > 0 ? 'color: red; font-weight: bold;' : '' }}">{{ $summary['dangerCount'] }} Kali</td>
            </tr>
        </table>
    </div>

</body>
</html>
