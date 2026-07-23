<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Permintaan dan Pemberian Cuti</title>
    <style>
        @page { margin: 10px 20px; }
        body {
            font-family: 'Times-Roman', 'Times New Roman', Times, serif;
            font-size: 9px;
            color: #000;
            line-height: 1.15;
        }
        table { width: 100%; border-collapse: collapse; margin: 0; }
        td, th { border: 1px solid #000; padding: 2px 4px; vertical-align: top; }
        .no-border { border: none; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .title { font-size: 11px; font-weight: bold; text-align: center; margin: 3px 0; }
        .checkbox { width: 16px; text-align: center; font-weight: bold; }
        .checkbox-symbol { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; }
        .spacer { height: 3px; line-height: 3px; font-size: 1px; }
        .notes-list { font-size: 8px; margin-top: 2px; line-height: 1.1; }
    </style>
</head>
<body>

@php
    $lamaVal = $pengajuan->lama_cuti;
    $satuan = 'hari';
    if (in_array($jenisCuti->kode_cuti, ['CM', 'CB_HAJI'])) {
        $satuan = 'bulan';
        $displayLama = ($lamaVal >= 30 ? round($lamaVal / 30) : $lamaVal) . ' Bulan';
    } elseif ($lamaVal >= 365) {
        $satuan = 'tahun';
        $displayLama = round($lamaVal / 365) . ' Tahun';
    } else {
        $satuan = 'hari';
        $displayLama = $lamaVal . ' Hari';
    }
@endphp

{{-- HEADER PERMOHONAN --}}
<table class="no-border" style="margin-bottom: 2px;">
    <tr>
        <td class="no-border" width="55%"></td>
        <td class="no-border" width="45%">
            Bandar Lampung, {{ \Carbon\Carbon::parse($pengajuan->created_at ?? now())->translatedFormat('d F Y') }}<br>
            Kepada<br>
            Yth. Kepala Badan Kepegawaian Daerah<br>
            Provinsi Lampung<br>
            di<br>
            &nbsp;&nbsp;&nbsp;&nbsp;TELUKBETUNG
        </td>
    </tr>
</table>

<div class="title">FORMULIR PERMINTAAN DAN PEMBERIAN CUTI</div>

{{-- BAGIAN I --}}
<table>
    <tr><td colspan="4" class="bold">I. DATA PEGAWAI</td></tr>
    <tr>
        <td width="15%">Nama</td>
        <td width="35%" class="bold">{{ $pegawai->nama_lengkap }}</td>
        <td width="15%">NIP</td>
        <td width="35%">{{ $pegawai->nip }}</td>
    </tr>
    <tr>
        <td>Jabatan</td>
        <td>{{ $jabatan->nama_jabatan }}</td>
        <td>Masa Kerja</td>
        <td>{{ $pegawai->masa_kerja }}</td>
    </tr>
    <tr>
        <td>Unit Kerja</td>
        <td>Dinas Kelautan dan Perikanan Provinsi Lampung</td>
        <td>Pangkat/Gol</td>
        <td>{{ $pegawai->pangkat }}</td>
    </tr>
</table>

<div class="spacer"></div>

{{-- BAGIAN II (2 KOLOM SAJA PERSIS SEPERTI GAMBAR ACUAN) --}}
<table>
    <tr><td colspan="4" class="bold">II. JENIS CUTI YANG DIAMBIL **</td></tr>
    <tr>
        <td width="40%">1. Cuti Tahunan</td>
        <td class="checkbox" width="10%"><span class="checkbox-symbol">{{ $jenisCuti->kode_cuti == 'CT'  ? '√' : '' }}</span></td>
        <td width="40%">2. Cuti Besar</td>
        <td class="checkbox" width="10%"><span class="checkbox-symbol">{{ in_array($jenisCuti->kode_cuti, ['CB', 'CB_UMROH', 'CB_HAJI'])  ? '√' : '' }}</span></td>
    </tr>
    <tr>
        <td>3. Cuti Sakit</td>
        <td class="checkbox"><span class="checkbox-symbol">{{ $jenisCuti->kode_cuti == 'CS'  ? '√' : '' }}</span></td>
        <td>4. Cuti Melahirkan</td>
        <td class="checkbox"><span class="checkbox-symbol">{{ $jenisCuti->kode_cuti == 'CM'  ? '√' : '' }}</span></td>
    </tr>
    <tr>
        <td>5. Cuti Karena Alasan Penting</td>
        <td class="checkbox"><span class="checkbox-symbol">{{ $jenisCuti->kode_cuti == 'CAK' ? '√' : '' }}</span></td>
        <td>6. Cuti di Luar Tanggungan Negara</td>
        <td class="checkbox"><span class="checkbox-symbol">{{ $jenisCuti->kode_cuti == 'CLN' ? '√' : '' }}</span></td>
    </tr>
</table>

<div class="spacer"></div>

{{-- BAGIAN III --}}
<table>
    <tr><td class="bold">III. ALASAN CUTI</td></tr>
    <tr><td style="height:20px; vertical-align:top;">{{ $pengajuan->alasan_cuti }}</td></tr>
</table>

<div class="spacer"></div>

{{-- BAGIAN IV (FORMAT SELAMA HARI/BULAN/TAHUN DENGAN CORET DYNAMIC) --}}
<table>
    <tr><td colspan="6" class="bold">IV. LAMANYA CUTI</td></tr>
    <tr>
        <td width="10%">Selama</td>
        <td width="28%">
            <b>{{ $displayLama }}</b> 
            (@if($satuan == 'hari')hari@else<span style="text-decoration:line-through;">hari</span>@endif/@if($satuan == 'bulan')bulan@else<span style="text-decoration:line-through;">bulan</span>@endif/@if($satuan == 'tahun')tahun@else<span style="text-decoration:line-through;">tahun</span>@endif)*
        </td>
        <td width="14%">Mulai Tanggal</td>
        <td width="20%">{{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->translatedFormat('d F Y') }}</td>
        <td width="6%" class="center">s/d</td>
        <td width="22%">{{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->translatedFormat('d F Y') }}</td>
    </tr>
</table>

<div class="spacer"></div>

{{-- BAGIAN V. CATATAN CUTI --}}
<table>
    <tr>
        <td colspan="5" class="bold">V. CATATAN CUTI</td>
    </tr>
    <tr>
        <td colspan="3" width="48%">1. CUTI TAHUNAN</td>
        <td width="42%">2. CUTI BESAR</td>
        <td width="10%"></td>
    </tr>
    <tr>
        <td width="12%" class="center">Tahun</td>
        <td width="12%" class="center">Sisa</td>
        <td width="24%" class="center">Keterangan</td>
        <td>3. CUTI SAKIT</td>
        <td></td>
    </tr>
    <tr>
        <td class="center">N-2</td>
        <td class="center">0</td>
        <td></td>
        <td>4. CUTI MELAHIRKAN</td>
        <td></td>
    </tr>
    <tr>
        <td class="center">N-1</td>
        <td class="center">0</td>
        <td></td>
        <td>5. CUTI KARENA ALASAN PENTING</td>
        <td></td>
    </tr>
    <tr>
        <td class="center">N</td>
        <td class="center">{{ $pegawai->sisa_cuti_tahunan }}</td>
        <td></td>
        <td>6. CUTI DI LUAR TANGGUNGAN NEGARA</td>
        <td></td>
    </tr>
</table>

<div class="spacer"></div>

{{-- BAGIAN VI --}}
<table>
    <tr><td colspan="2" class="bold">VI. ALAMAT SELAMA MENJALANKAN CUTI</td></tr>
    <tr>
        <td width="50%" style="height:85px; vertical-align:top;">
            {{ $pengajuan->alamat_selama_cuti }}
        </td>
        <td width="50%" style="padding:0; vertical-align:top;">
            <table style="width:100%; border:none;">
                <tr>
                    <td width="20%" style="border-top:none; border-left:none; border-bottom:1px solid #000;">TELP</td>
                    <td style="border-top:none; border-right:none; border-left:1px solid #000; border-bottom:1px solid #000;">
                        {{ $pengajuan->no_telp_selama_cuti ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="height:95px; border:none; text-align:center; vertical-align:top; padding:6px 4px 4px 4px;">
                        Hormat saya,<br>
                        <div style="height:90px;"></div>
                        <span style="font-weight:bold; text-decoration:underline;">{{ $pegawai->nama_lengkap }}</span><br>
                        NIP. {{ $pegawai->nip }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div class="spacer"></div>

{{-- BAGIAN VII (SPACE TTD ATASAN) --}}
<table>
    <tr><td colspan="5" class="bold">VII. PERTIMBANGAN ATASAN LANGSUNG</td></tr>
    <tr>
        <td class="center bold" width="16%">DISETUJUI</td>
        <td class="center bold" width="16%">PERUBAHAN****</td>
        <td class="center bold" width="18%">DITANGGUHKAN****</td>
        <td class="center bold" width="18%">TIDAK DISETUJUI****</td>
        <td class="center bold" width="32%">
            {{ strtoupper($pengajuan->atasan_jabatan ?? 'KEPALA DINAS') }}
        </td>
    </tr>
    <tr>
        <td style="height:120px"></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="height:120px; text-align:center; vertical-align:bottom; padding-bottom:4px;">
            <span class="bold" style="text-decoration:underline;">{{ $pengajuan->atasan_nama ?? 'Ir. BANI ISPRIYANTO, M.M.' }}</span><br>
            NIP. {{ $pengajuan->atasan_nip ?? '19690410 199503 1 002' }}
        </td>
    </tr>
</table>

<div class="spacer"></div>

{{-- BAGIAN VIII (SPACE TTD PEJABAT BERWENANG) --}}
<table>
    <tr><td colspan="5" class="bold center">KEPUTUSAN PEJABAT YANG BERWENANG MEMBERIKAN CUTI</td></tr>
    <tr>
        <td class="center bold" width="16%">DISETUJUI</td>
        <td class="center bold" width="16%">PERUBAHAN****</td>
        <td class="center bold" width="18%">DITANGGUHKAN****</td>
        <td class="center bold" width="18%">TIDAK DISETUJUI****</td>
        <td class="center bold" width="32%">
            Kepala Badan Kepegawaian<br>Daerah
        </td>
    </tr>
    <tr>
        <td style="height:120px"></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="height:120px; text-align:center; vertical-align:bottom; padding-bottom:4px;">
            <span class="bold" style="text-decoration:underline;">RENDI RESWANDI, S.STP.,M.Si</span><br>
            NIP. 19770526 199712 1 001
        </td>
    </tr>
</table>

{{-- FOOTER CATATAN --}}
<div class="notes-list">
    Catatan :<br>
    * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Coret yang tidak perlu<br>
    ** &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pilih salah satu dengan memberi tanda centang ( √ )<br>
    *** &nbsp;&nbsp;&nbsp;&nbsp;Diisi oleh pejabat yang menangani bidang kepegawaian sebelum PNS mengajukan cuti<br>
    **** &nbsp;&nbsp;Diberi tanda centang dan alasannya<br>
    N &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;= Cuti tahun berjalan<br>
    N - 1 &nbsp;&nbsp;= Sisa cuti 1 tahun sebelumnya<br>
    N - 2 &nbsp;&nbsp;= Sisa cuti 2 tahun sebelumnya
</div>

</body>
</html>