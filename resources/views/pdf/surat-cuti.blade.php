<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Permintaan dan Pemberian Cuti</title>
    <style>
        @page { margin: 15px 25px; }
        body {
            font-family: 'Times-Roman', 'Times New Roman', Times, serif;
            font-size: 10px;
            color: #000;
            line-height: 1.2;
        }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #000; padding: 2.5px 4px; vertical-align: top; }
        .no-border { border: none; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .title { font-size: 14px; font-weight: bold; text-align: center; margin: 3px 0; }
        .section { background: #efefef; font-weight: bold; padding: 3px 4px; }
        .checkbox { width: 16px; text-align: center; font-weight: bold; }
        .checkbox-symbol { font-family: 'DejaVu Sans', sans-serif; }
        .spacer { height: 4px; line-height: 4px; font-size: 1px; }
    </style>
</head>
<body>
{{-- HEADER --}}
<table class="no-border">
    <tr>
        <td class="no-border" width="60%"></td>
        <td class="no-border">Bandar Lampung, {{ now()->translatedFormat('d F Y') }}</td>
    </tr>
    <tr>
        <td class="no-border"></td>
        <td class="no-border">
            Kepada<br>
            Yth. Kepala Dinas Kelautan dan Perikanan<br>
            Provinsi Lampung<br>
            di<br>
            Bandar Lampung
        </td>
    </tr>
</table>
<div class="spacer"></div>
<div class="title">FORMULIR PERMINTAAN DAN PEMBERIAN CUTI</div>
<div class="spacer"></div>
{{-- BAGIAN I --}}
<table>
    <tr><td colspan="4" class="section">I. DATA PEGAWAI</td></tr>
    <tr>
        <td width="20%">Nama</td>
        <td width="30%">{{ $pegawai->nama_lengkap }}</td>
        <td width="20%">NIP</td>
        <td>{{ $pegawai->nip }}</td>
    </tr>
    <tr>
        <td>Jabatan</td>
        <td>{{ $jabatan->nama_jabatan }}</td>
        <td>Golongan</td>
        <td>{{ $pegawai->pangkat }}</td>
    </tr>
    <tr>
        <td>Unit Kerja</td>
        <td>{{ $bidang->nama_bidang }}</td>
        <td>Masa Kerja</td>
        <td>{{ $pegawai->masa_kerja }}</td>
    </tr>
    <tr>
        <td>Sisa Cuti</td>
        <td>{{ $pegawai->sisa_cuti_tahunan }} Hari</td>
        <td>Jenis Pegawai</td>
        <td>{{ $pegawai->jenis_pegawai }}</td>
    </tr>
</table>
<div class="spacer"></div>
{{-- BAGIAN II --}}
<table>
    <tr><td colspan="8" class="section">II. JENIS CUTI YANG DIAMBIL</td></tr>
    <tr>
        <td width="20%">Cuti Tahunan</td>
        <td class="checkbox" width="5%"><span class="checkbox-symbol">{{ $jenisCuti->kode_cuti == 'CT'  ? '✓' : '' }}</span></td>
        <td width="20%">Cuti Besar</td>
        <td class="checkbox" width="5%"><span class="checkbox-symbol">{{ $jenisCuti->kode_cuti == 'CB'  ? '✓' : '' }}</span></td>
        <td width="20%">Cuti Sakit</td>
        <td class="checkbox" width="5%"><span class="checkbox-symbol">{{ $jenisCuti->kode_cuti == 'CS'  ? '✓' : '' }}</span></td>
        <td width="20%">Cuti Melahirkan</td>
        <td class="checkbox" width="5%"><span class="checkbox-symbol">{{ $jenisCuti->kode_cuti == 'CM'  ? '✓' : '' }}</span></td>
    </tr>
    <tr>
        <td>Cuti Karena Alasan Penting</td>
        <td class="checkbox"><span class="checkbox-symbol">{{ $jenisCuti->kode_cuti == 'CAK' ? '✓' : '' }}</span></td>
        <td colspan="5">Cuti di Luar Tanggungan Negara</td>
        <td class="checkbox"><span class="checkbox-symbol">{{ $jenisCuti->kode_cuti == 'CLN' ? '✓' : '' }}</span></td>
    </tr>
</table>
<div class="spacer"></div>
{{-- BAGIAN III --}}
<table>
    <tr><td class="section">III. ALASAN CUTI</td></tr>
    <tr><td style="height:32px">{{ $pengajuan->alasan_cuti }}</td></tr>
</table>
<div class="spacer"></div>
{{-- BAGIAN IV --}}
<table>
    <tr><td colspan="6" class="section">IV. LAMANYA CUTI</td></tr>
    <tr>
        <td width="18%">Selama</td>
        <td width="15%"><b>{{ $pengajuan->lama_cuti_display }}</b></td>
        <td width="12%">Mulai</td>
        <td width="20%">{{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->translatedFormat('d F Y') }}</td>
        <td width="12%">Sampai</td>
        <td>{{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->translatedFormat('d F Y') }}</td>
    </tr>
</table>
<div class="spacer"></div>
{{-- BAGIAN V --}}
<table>
    <tr><td colspan="8" class="section">V. CATATAN CUTI</td></tr>
    <tr>
        <td width="22%">Cuti Tahunan</td>
        <td width="13%">{{ $pegawai->sisa_cuti_tahunan }} Hari</td>
        <td width="10%">N-2</td><td width="10%">-</td>
        <td width="10%">N-1</td><td width="10%">-</td>
        <td width="10%">N</td><td>{{ $pegawai->sisa_cuti_tahunan }}</td>
    </tr>
    <tr><td>Cuti Besar</td><td colspan="7">-</td></tr>
    <tr><td>Cuti Sakit</td><td colspan="7">-</td></tr>
    <tr><td>Cuti Melahirkan</td><td colspan="7">-</td></tr>
    <tr><td>Cuti Karena Alasan Penting</td><td colspan="7">-</td></tr>
    <tr><td>Cuti di Luar Tanggungan Negara</td><td colspan="7">-</td></tr>
</table>
<div class="spacer"></div>
{{-- BAGIAN VI --}}
<table>
    <tr><td colspan="2" class="section">VI. ALAMAT SELAMA MENJALANKAN CUTI</td></tr>
    <tr>
        <!-- Left Column: Address -->
        <td width="55%" style="height:120px; vertical-align:top; padding:4px;">
            {{ $pengajuan->alamat_selama_cuti }}
        </td>
        <!-- Right Column: TELP and Signature (Nested Table) -->
        <td width="45%" style="padding:0; vertical-align:top;">
            <table style="width:100%; height:120px; border-collapse:collapse; border:none; margin:0; padding:0;">
                <tr>
                    <td width="30%" style="height:25px; border-top:none; border-left:none; border-bottom:1px solid #000; border-right:1px solid #000; padding:2.5px 4px; vertical-align:middle;">
                        TELP
                    </td>
                    <td style="height:25px; border-top:none; border-left:none; border-right:none; border-bottom:1px solid #000; padding:2.5px 4px; vertical-align:middle;">
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
{{-- BAGIAN VII --}}
<table>
    <tr><td colspan="5" class="section">VII. PERTIMBANGAN ATASAN LANGSUNG</td></tr>
    <tr>
        <td class="center bold" width="17%">DISETUJUI</td>
        <td class="center bold" width="17%">PERUBAHAN****</td>
        <td class="center bold" width="17%">DITANGGUHKAN****</td>
        <td class="center bold" width="17%">TIDAK DISETUJUI****</td>
        <td class="center bold" width="32%">{{ strtoupper($pengajuan->atasan_jabatan ?? 'Sekretaris Dinas') }},</td>
    </tr>
    <tr>
        <td style="height:120px"></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="height:120px; text-align:center; vertical-align:bottom; padding-bottom:4px;">
            <span style="font-weight:bold; text-decoration:underline;">{{ $pengajuan->atasan_nama ?? 'A. FAISAL, A.Pi.' }}</span>
            @if(!empty($pengajuan->atasan_nip) && $pengajuan->atasan_nip !== '-')
                <br>NIP. {{ $pengajuan->atasan_nip }}
            @endif
        </td>
    </tr>
</table>
<div class="spacer"></div>
{{-- BAGIAN VIII --}}
<table>
    <tr>
        <td colspan="5" class="section center">
            KEPUTUSAN PEJABAT YANG BERWENANG MEMBERIKAN CUTI
        </td>
    </tr>
    <tr>
        <td class="center bold" width="17%">DISETUJUI</td>
        <td class="center bold" width="17%">PERUBAHAN****</td>
        <td class="center bold" width="17%">DITANGGUHKAN****</td>
        <td class="center bold" width="17%">TIDAK DISETUJUI****</td>
        <td class="center bold" width="32%">{{ strtoupper($pengajuan->pejabat_jabatan ?? 'Kepala Dinas') }},</td>
    </tr>
    <tr>
        <td style="height:120px"></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="height:120px; text-align:center; vertical-align:bottom; padding-bottom:6px;">
            <span style="font-weight:bold; text-decoration:underline;">
                {{ $pengajuan->pejabat_nama ?? 'Ir. BANI ISPRIYANTO, M.M.' }}
            </span>
            @if(!empty($pengajuan->pejabat_nip) && $pengajuan->pejabat_nip !== '-')
                <br>NIP. {{ $pengajuan->pejabat_nip }}
            @endif
        </td>
    </tr>
</table>
<div class="spacer"></div>
<div style="text-align:center; font-size:9px; color:#666;">
    Dicetak oleh Sistem Informasi Pengajuan Cuti Pegawai<br>
    Dinas Kelautan dan Perikanan Provinsi Lampung<br>
</div>
</body>
</html>