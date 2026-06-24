<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Permintaan dan Pemberian Cuti</title>

    <style>

        @page{
            margin:25px;
        }

        body{
            font-family: DejaVu Sans, sans-serif;
            font-size:11px;
            color:#000;
            line-height:1.3;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        td,th{
            border:1px solid #000;
            padding:4px;
            vertical-align:top;
        }

        .no-border{
            border:none;
        }

        .center{
            text-align:center;
        }

        .right{
            text-align:right;
        }

        .bold{
            font-weight:bold;
        }

        .title{
            font-size:15px;
            font-weight:bold;
            text-align:center;
            margin-top:8px;
            margin-bottom:8px;
        }

        .subtitle{
            font-size:12px;
            text-align:center;
            margin-bottom:10px;
        }

        .section{
            background:#efefef;
            font-weight:bold;
        }

        .checkbox{
            width:16px;
            text-align:center;
        }

        .small{
            font-size:10px;
        }

        .ttd{
            height:80px;
        }

    </style>

</head>

<body>

{{-- ============================== --}}
{{-- HEADER --}}
{{-- ============================== --}}

<table class="no-border">

<tr>

<td class="no-border" width="60%">

</td>

<td class="no-border">

Bandar Lampung,
{{ now()->translatedFormat('d F Y') }}

</td>

</tr>

<tr>

<td class="no-border">

</td>

<td class="no-border">

Kepada

Yth. Kepala Dinas Kelautan dan Perikanan

Provinsi Lampung

di

Bandar Lampung

</td>

</tr>

</table>

<br>

<div class="title">

FORMULIR PERMINTAAN DAN PEMBERIAN CUTI

</div>

<div class="subtitle">

Nomor :
{{ $pengajuan->nomor_surat }}

</div>

{{-- ============================== --}}
{{-- BAGIAN I --}}
{{-- ============================== --}}

<table>

<tr>

<td colspan="4" class="section">

I. DATA PEGAWAI

</td>

</tr>

<tr>

<td width="20%">

Nama

</td>

<td width="30%">

{{ $pegawai->nama_lengkap }}

</td>

<td width="20%">

NIP

</td>

<td>

{{ $pegawai->nip }}

</td>

</tr>

<tr>

<td>

Jabatan

</td>

<td>

{{ $jabatan->nama_jabatan }}

</td>

<td>

Golongan

</td>

<td>

{{ $pegawai->pangkat }}

</td>

</tr>

<tr>

<td>

Unit Kerja

</td>

<td>

{{ $bidang->nama_bidang }}

</td>

<td>

Masa Kerja

</td>

<td>

{{ $pegawai->masa_kerja }}

</td>

</tr>

<tr>

<td>

Tempat / Tgl Lahir

</td>

<td>

{{ $pegawai->tempat_lahir }},
{{ \Carbon\Carbon::parse($pegawai->tanggal_lahir)->translatedFormat('d F Y') }}

</td>

<td>

Jenis Kelamin

</td>

<td>

{{ $pegawai->jenis_kelamin=='L' ? 'Laki-Laki' : 'Perempuan' }}

</td>

</tr>

<tr>

<td>

Email

</td>

<td>

{{ $pegawai->email }}

</td>

<td>

Sisa Cuti

</td>

<td>

{{ $pegawai->sisa_cuti_tahunan }} Hari

</td>

</tr>

</table>

<br>

{{-- ============================== --}}
{{-- BAGIAN II --}}
{{-- ============================== --}}

<table>

<tr>
    <td colspan="8" class="section">
        II. JENIS CUTI YANG DIAMBIL
    </td>
</tr>

<tr>

<td class="checkbox">
{{ $jenisCuti->kode_cuti=='CT' ? '✓' : '' }}
</td>

<td>
Cuti Tahunan
</td>

<td class="checkbox">
{{ $jenisCuti->kode_cuti=='CB' ? '✓' : '' }}
</td>

<td>
Cuti Besar
</td>

<td class="checkbox">
{{ $jenisCuti->kode_cuti=='CS' ? '✓' : '' }}
</td>

<td>
Cuti Sakit
</td>

<td class="checkbox">
{{ $jenisCuti->kode_cuti=='CM' ? '✓' : '' }}
</td>

<td>
Cuti Melahirkan
</td>

</tr>

<tr>

<td class="checkbox">
{{ $jenisCuti->kode_cuti=='CAK' ? '✓' : '' }}
</td>

<td>
Cuti Karena Alasan Penting
</td>

<td class="checkbox">
{{ $jenisCuti->kode_cuti=='CLN' ? '✓' : '' }}
</td>

<td colspan="5">
Cuti di Luar Tanggungan Negara
</td>

</tr>

</table>

<br>

{{-- ============================== --}}
{{-- BAGIAN III --}}
{{-- ============================== --}}

<table>

<tr>

<td class="section">

III. ALASAN CUTI

</td>

</tr>

<tr>

<td style="height:60px">

{{ $pengajuan->alasan }}

</td>

</tr>

</table>

<br>

{{-- ============================== --}}
{{-- BAGIAN IV --}}
{{-- ============================== --}}

<table>

<tr>

<td colspan="6" class="section">

IV. LAMANYA CUTI

</td>

</tr>

<tr>

<td width="18%">

Selama

</td>

<td width="15%">

<b>

{{ $pengajuan->lama_cuti }}

</b>

Hari

</td>

<td width="12%">

Mulai

</td>

<td width="20%">

{{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->translatedFormat('d F Y') }}

</td>

<td width="12%">

Sampai

</td>

<td>

{{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->translatedFormat('d F Y') }}

</td>

</tr>

</table>

<br>

{{-- ============================== --}}
{{-- BAGIAN V --}}
{{-- ============================== --}}

<table>

<tr>

<td colspan="8" class="section">

V. CATATAN CUTI

</td>

</tr>

<tr>

<td width="22%">

Cuti Tahunan

</td>

<td width="13%">

{{ $pegawai->sisa_cuti_tahunan }}

Hari

</td>

<td width="10%">

N-2

</td>

<td width="10%">

-

</td>

<td width="10%">

N-1

</td>

<td width="10%">

-

</td>

<td width="10%">

N

</td>

<td>

{{ $pegawai->sisa_cuti_tahunan }}

</td>

</tr>

<tr>

<td>

Cuti Besar

</td>

<td colspan="7">

-

</td>

</tr>

<tr>

<td>

Cuti Sakit

</td>

<td colspan="7">

-

</td>

</tr>

<tr>

<td>

Cuti Melahirkan

</td>

<td colspan="7">

-

</td>

</tr>

<tr>

<td>

Cuti Karena Alasan Penting

</td>

<td colspan="7">

-

</td>

</tr>

<tr>

<td>

Cuti di Luar Tanggungan Negara

</td>

<td colspan="7">

-

</td>

</tr>

</table>

<br>

{{-- ============================== --}}
{{-- BAGIAN VI --}}
{{-- ============================== --}}

<table>

<tr>

<td colspan="4" class="section">

VI. ALAMAT SELAMA MENJALANKAN CUTI

</td>

</tr>

<tr>

<td width="55%">

{{ $pengajuan->alamat_selama_cuti }}

</td>

<td width="15%">

Telepon

</td>

<td>

{{ $pengajuan->no_hp ?? '-' }}

</td>

</tr>

</table>

<br>
{{-- ============================== --}}
{{-- BAGIAN VII --}}
{{-- ============================== --}}

<table>

<tr>
    <td colspan="2" class="section">
        VII. PERTIMBANGAN ATASAN LANGSUNG
    </td>
</tr>

<tr>
    <td width="35%">
        Keputusan
    </td>

    <td height="45">

        ☐ Disetujui

        <br>

        ☐ Ditangguhkan

        <br>

        ☐ Perubahan

        <br>

        ☐ Tidak Disetujui

    </td>
</tr>

<tr>
    <td>
        Catatan
    </td>

    <td height="60">

    </td>
</tr>

</table>

<br>

{{-- ============================== --}}
{{-- BAGIAN VIII --}}
{{-- ============================== --}}

<table>

<tr>
    <td colspan="2" class="section">
        VIII. KEPUTUSAN PEJABAT YANG BERWENANG MEMBERIKAN CUTI
    </td>
</tr>

<tr>

<td width="35%">

Keputusan

</td>

<td height="55">

☐ Disetujui

<br>

☐ Ditangguhkan

<br>

☐ Perubahan

<br>

☐ Tidak Disetujui

</td>

</tr>

<tr>

<td>

Catatan

</td>

<td height="70">

</td>

</tr>

</table>

<br>

{{-- ============================== --}}
{{-- KETERANGAN --}}
{{-- ============================== --}}

<table>

<tr>

<td class="section">

KETERANGAN

</td>

</tr>

<tr>

<td class="small">

1. Formulir ini dibuat secara otomatis oleh Sistem Informasi Pengajuan Cuti Pegawai.

<br><br>

2. Persetujuan akhir dilakukan oleh pejabat yang berwenang sesuai ketentuan yang berlaku.

<br><br>

3. Setelah disetujui, formulir ini dicetak sebagai dokumen fisik untuk proses administrasi dan penandatanganan manual.

<br><br>

4. Apabila terdapat perubahan data, formulir harus dicetak kembali.

</td>

</tr>

</table>

<br>

<div style="text-align:center;font-size:10px;color:#666;">

Dicetak oleh Sistem Informasi Pengajuan Cuti Pegawai

<br>

Dinas Kelautan dan Perikanan Provinsi Lampung

<br>

Tanggal Cetak :
{{ now()->translatedFormat('d F Y H:i') }}

</div>

</body>

</html>