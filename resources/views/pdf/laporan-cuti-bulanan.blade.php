<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan Rekapitulasi Cuti Pegawai</title>
    <style>
        @page {
            margin: 20px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 11px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .center {
            text-align: center;
        }
        .badge {
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
        }
        .badge-warning { background-color: #f59e0b; color: #fff; }
        .badge-success { background-color: #10b981; color: #fff; }
        .badge-danger { background-color: #ef4444; color: #fff; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Bulanan Rekapitulasi Cuti Pegawai</h2>
        <p>Dinas Kelautan dan Perikanan Provinsi Lampung</p>
        <p>Tahun: {{ $filters['tahun'] }} | Bulan: {{ \Carbon\Carbon::create()->month($filters['bulan'])->translatedFormat('F') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">Tanggal Pengajuan</th>
                <th width="12%">NIP</th>
                <th width="15%">Nama Pegawai</th>
                <th width="12%">Bidang</th>
                <th width="10%">Jenis Cuti</th>
                <th width="15%">Tanggal Cuti</th>
                <th width="12%">Lama Cuti</th>
                <th width="8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengajuan as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->tanggal_pengajuan?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $item->pegawai->nip ?? '-' }}</td>
                    <td style="font-weight: bold;">{{ $item->pegawai->nama_lengkap ?? '-' }}</td>
                    <td>
                        {{ $item->pegawai->bidang->nama_bidang ?? '-' }}
                        @if($item->pegawai->sub_bagian)
                            <div style="font-size: 8px; color: #666; margin-top: 2px;">{{ $item->pegawai->sub_bagian }}</div>
                        @endif
                    </td>
                    <td>{{ $item->jenisCuti->nama_cuti ?? '-' }}</td>
                    <td>
                        {{ $item->tanggal_mulai?->format('d/m/Y') }} s.d. {{ $item->tanggal_selesai?->format('d/m/Y') }}
                    </td>
                    <td class="center">{{ $item->lama_cuti_display }}</td>
                    <td class="center">
                        @if($item->status === 'menunggu')
                            <span class="badge badge-warning">Menunggu</span>
                        @elseif($item->status === 'disetujui')
                            <span class="badge badge-success">Disetujui</span>
                        @else
                            <span class="badge badge-danger">Ditolak</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="center">Tidak ada data pengajuan cuti pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh SIPENCUTI pada: {{ $generated }}
    </div>
</body>
</html>
