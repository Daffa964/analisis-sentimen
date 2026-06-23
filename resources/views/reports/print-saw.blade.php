<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Perangkingan Pelayanan - RSUD dr. Loekmono Hadi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            padding: 20px;
            font-size: 12px;
        }
        
        /* Kop Surat */
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            text-align: center;
            position: relative;
        }
        .kop-surat h1 {
            font-size: 16px;
            margin: 0;
            text-transform: uppercase;
            font-weight: bold;
        }
        .kop-surat h2 {
            font-size: 18px;
            margin: 5px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-surat p {
            font-size: 10px;
            margin: 5px 0 0 0;
            color: #555;
        }
        
        /* Document Title */
        .doc-title {
            text-align: center;
            margin-bottom: 25px;
            text-transform: uppercase;
        }
        .doc-title h3 {
            font-size: 14px;
            margin: 0;
            text-decoration: underline;
        }
        .doc-title p {
            font-size: 11px;
            margin: 5px 0 0 0;
        }

        /* Info Block */
        .info-block {
            margin-bottom: 15px;
            font-size: 11px;
        }
        .info-block table {
            width: 100%;
        }
        .info-block td {
            padding: 2px 0;
        }

        /* General Table */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
        }
        table.data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 11px;
        }
        table.data-table td.text-left {
            text-align: left;
        }
        table.data-table tr.highlight {
            font-weight: bold;
            background-color: #fafafa;
        }

        /* Signatures */
        .signature-section {
            margin-top: 50px;
            width: 100%;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-space {
            height: 70px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Print formatting */
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .page-break {
                page-break-before: always;
            }
        }
        
        .print-btn-container {
            margin-bottom: 20px;
            text-align: right;
        }
        .print-btn {
            background-color: #16a34a;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="print-btn-container no-print">
        <button onclick="window.print()" class="print-btn">Cetak Halaman Ini</button>
    </div>

    <!-- Kop Surat RSUD -->
    <div class="kop-surat">
        <h1>Pemerintah Kabupaten Kudus</h1>
        <h2>RSUD dr. Loekmono Hadi</h2>
        <p>Jl. Dokter Lukmono Hadi No.19, Kudus, Jawa Tengah 59312 | Telp: (0291) 431846</p>
    </div>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        <h3>Laporan Perangkingan Kualitas Pelayanan Rawat Inap</h3>
        <p>Berdasarkan Metode AHP & SAW</p>
    </div>

    <!-- Metadata Laporan -->
    <div class="info-block">
        <table>
            <tr>
                <td style="width: 120px;">Periode Survei</td>
                <td style="width: 10px;">:</td>
                <td><strong>{{ $period->name }}</strong> ({{ $period->start_date->format('d/m/Y') }} s.d {{ $period->end_date->format('d/m/Y') }})</td>
            </tr>
            <tr>
                <td>Kategori Bangsal</td>
                <td>:</td>
                <td><strong>{{ strtoupper($category) }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>:</td>
                <td>{{ $datePrinted }} WIB</td>
            </tr>
        </table>
    </div>

    <!-- 1. Ranking Table -->
    <h4 style="margin-bottom: 8px; font-size: 12px;">I. HASIL PERANGKINGAN AKHIR (SAW)</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 60px;">Peringkat</th>
                <th>Nama Bangsal</th>
                <th style="width: 150px;">Nilai Preferensi (V)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sawResults['rankings'] as $item)
                <tr class="{{ $item['rank'] == 1 ? 'highlight' : '' }}">
                    <td>{{ $item['rank'] }}</td>
                    <td class="text-left">{{ $item['ward_name'] }}</td>
                    <td><strong>{{ number_format($item['preference_value'], 4) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-bottom: 20px;">
        <p style="font-size: 11px; font-style: italic;">
            * Catatan: Bangsal peringkat 1 menunjukkan kualitas pelayanan terbaik berdasarkan penilaian pasien pada periode ini.
        </p>
    </div>

    <!-- 2. Decision Matrix -->
    <h4 style="margin-bottom: 8px; font-size: 12px;">II. MATRIKS KEPUTUSAN (X)</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-left">Alternatif (Bangsal)</th>
                @foreach($criteria as $c)
                    <th>{{ $c->code }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($sawResults['rankings'] as $item)
                @php $wId = $item['ward_id']; @endphp
                <tr>
                    <td class="text-left">{{ $item['ward_name'] }}</td>
                    @foreach($criteria as $c)
                        <td>{{ number_format($sawResults['raw_matrix'][$wId][$c->id], 4) }}</td>
                    @endforeach
                </tr>
            @endforeach
            <tr class="highlight">
                <td class="text-left">Nilai Max (Benefit)</td>
                @foreach($criteria as $c)
                    <td>{{ number_format($sawResults['max_values'][$c->id], 4) }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <!-- 3. AHP Weights -->
    <h4 style="margin-bottom: 8px; font-size: 12px;">III. BOBOT AHP KRITERIA (W)</h4>
    <table class="data-table">
        <thead>
            <tr>
                @foreach($criteria as $c)
                    <th>{{ $c->code }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr class="highlight">
                @foreach($criteria as $c)
                    <td>{{ number_format($sawResults['weights'][$c->id], 4) }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <!-- 4. Recommendations -->
    <h4 style="margin-bottom: 8px; font-size: 12px; page-break-before: auto;">IV. REKOMENDASI TINDAKAN PERBAIKAN MUTU</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 150px; text-align: left;">Peringkat & Bangsal</th>
                <th style="text-align: left;">Rekomendasi Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sawResults['rankings'] as $item)
                <tr>
                    <td class="text-left" style="font-weight: bold; vertical-align: top;">
                        #{{ $item['rank'] }} - {{ $item['ward_name'] }}
                        <div style="font-size: 10px; font-weight: normal; color: #555; margin-top: 4px;">
                            Indeks: {{ number_format($item['preference_value'] * 100, 1) }}%
                        </div>
                    </td>
                    <td class="text-left" style="line-height: 1.5; vertical-align: top;">
                        {!! Str::markdown($item['recommendation']) !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Keterangan Kriteria -->
    <div style="font-size: 10px; margin-bottom: 30px;">
        <strong>Keterangan Kriteria:</strong>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); margin-top: 5px;">
            @foreach($criteria as $c)
                <div>{{ $c->code }}: {{ $c->name }}</div>
            @endforeach
        </div>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p><strong>Kepala Bidang Pelayanan RSUD</strong></p>
            <div class="signature-space"></div>
            <p class="signature-name">dr. H. Abdul Malik, M.Kes</p>
            <p>NIP. 19740512 200312 1 002</p>
        </div>
        
        <div class="signature-box">
            <p>Kudus, {{ now()->translatedFormat('d F Y') }}</p>
            <p><strong>Petugas Analis Sistem</strong></p>
            <div class="signature-space"></div>
            <p class="signature-name">{{ auth()->user()->name }}</p>
            <p>User Email: {{ auth()->user()->email }}</p>
        </div>
    </div>

</body>
</html>
