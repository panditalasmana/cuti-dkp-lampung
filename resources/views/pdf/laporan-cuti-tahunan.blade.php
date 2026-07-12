<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tahunan Rekapitulasi Cuti Pegawai</title>
    <style>
        @page {
            margin: 20px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 14px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px 3px;
            text-align: left;
            font-size: 8px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Tahunan Rekapitulasi Cuti Pegawai</h2>
        <p>Dinas Kelautan dan Perikanan Provinsi Lampung</p>
        <p>Tahun: {{ $filters['tahun'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%" rowspan="2">No</th>
                <th width="12%" rowspan="2">NIP</th>
                <th width="18%" rowspan="2">Nama Pegawai</th>
                <th width="17%" rowspan="2">Bidang / Jabatan</th>
                <th colspan="6">Total Penggunaan Cuti Disetujui</th>
                <th width="14%" rowspan="2">Total Cuti Terpakai</th>
            </tr>
            <tr>
                <th width="7%">Tahunan (Hari)</th>
                <th width="7%">Besar (Hari)</th>
                <th width="7%">Sakit (Hari)</th>
                <th width="8%">Melahirkan (Bulan)</th>
                <th width="8%">Alasan Penting (Hari)</th>
                <th width="7%">CLTN (Tahun)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item['pegawai']->nip }}</td>
                    <td style="font-weight: bold;">{{ $item['pegawai']->nama_lengkap }}</td>
                    <td>
                        {{ $item['pegawai']->bidang->nama_bidang ?? '-' }}
                        @if($item['pegawai']->sub_bagian)
                            <div style="font-size: 7px; color: #666;">{{ $item['pegawai']->sub_bagian }}</div>
                        @endif
                    </td>
                    <td class="center">{{ $item['cuti']['CT'] }}</td>
                    <td class="center">{{ $item['cuti']['CB'] }}</td>
                    <td class="center">{{ $item['cuti']['CS'] }}</td>
                    <td class="center">{{ $item['cuti']['CM'] }}</td>
                    <td class="center">{{ $item['cuti']['CAK'] }}</td>
                    <td class="center">{{ $item['cuti']['CLN'] }}</td>
                    <td class="center" style="font-weight: bold;">
                        @php
                            $hari = $item['cuti']['CT'] + $item['cuti']['CB'] + $item['cuti']['CS'] + $item['cuti']['CAK'];
                            $bulan = $item['cuti']['CM'];
                            $tahun = $item['cuti']['CLN'];
                            
                            $parts = [];
                            if ($hari > 0) $parts[] = $hari . ' Hari';
                            if ($bulan > 0) $parts[] = $bulan . ' Bulan';
                            if ($tahun > 0) $parts[] = $tahun . ' Tahun';
                            
                            echo empty($parts) ? '0' : implode(', ', $parts);
                        @endphp
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="center">Tidak ada data pegawai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh SIPENCUTI pada: {{ $generated }}
    </div>
</body>
</html>
