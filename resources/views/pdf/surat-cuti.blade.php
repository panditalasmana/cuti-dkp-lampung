<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Permintaan dan Pemberian Cuti</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 28pt 64.5pt 24pt 64.5pt; /* top right bottom left — top & bottom dikecilkan agar muat di A4 */
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            font-weight: normal; 
            color: #000;
            line-height: 1.05;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }
        td, th {
            border: 0.5px solid #000;
            padding: 1px 3px;
            vertical-align: top;
            font-weight: normal;
        }
        tr:last-child td {
            border-bottom: 0.5px solid #000;
        }
        td:last-child {
            border-right: 0.5px solid #000;
        }
        .no-border { border: none !important; }
        .center { text-align: center; }
        .bold { font-weight: 600; }
        .title {
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            margin: 4px 0 6px 0;
        }
        .checkbox {
            text-align: left;
            font-weight: bold;
            font-weight: normal;
        }
        .checkbox-symbol {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }
        .notes-list {
            font-size: 12px;
            margin-top: 3px;
            line-height: 1.2;
        }
        .strike {
            text-decoration: line-through;
        }
    </style>
</head>
<body>

@php
    $lamaVal = $pengajuan->lama_cuti;
    $satuan = 'hari';
    if (in_array($jenisCuti->kode_cuti, ['CM', 'CB_HAJI'])) {
        $satuan = 'bulan';
        $displayAngka = ($lamaVal >= 30 ? round($lamaVal / 30) : $lamaVal);
    } elseif ($lamaVal >= 365) {
        $satuan = 'tahun';
        $displayAngka = round($lamaVal / 365);
    } else {
        $satuan = 'hari';
        $displayAngka = $lamaVal;
    }

    $strHari  = ($satuan === 'hari')  ? 'hari'  : '<span class="strike">hari</span>';
    $strBulan = ($satuan === 'bulan') ? 'bulan' : '<span class="strike">bulan</span>';
    $strTahun = ($satuan === 'tahun') ? 'tahun' : '<span class="strike">tahun</span>';
    
    $formatStrikethrough = "({$strHari}/{$strBulan}/{$strTahun})*";

    // Dynamic Header Tujuan berdasarkan Pejabat yang Berwenang
    $pejabatJabatanRaw = strtolower($pengajuan->pejabat_jabatan ?? '');
    if (str_contains($pejabatJabatanRaw, 'gubernur')) {
        $headerTujuanLine1 = 'Gubernur Lampung';
        $headerTujuanLine2 = '';
    } elseif (str_contains($pejabatJabatanRaw, 'sekretaris daerah') || str_contains($pejabatJabatanRaw, 'sekda')) {
        $headerTujuanLine1 = 'Sekretaris Daerah';
        $headerTujuanLine2 = 'Provinsi Lampung';
    } elseif (str_contains($pejabatJabatanRaw, 'dinas')) {
        $headerTujuanLine1 = 'Kepala Dinas Kelautan dan Perikanan';
        $headerTujuanLine2 = 'Provinsi Lampung';
    } else {
        $headerTujuanLine1 = 'Kepala Badan Kepegawaian Daerah';
        $headerTujuanLine2 = 'Provinsi Lampung';
    }
@endphp

{{-- HEADER PERMOHONAN --}}
<table class="no-border" width="100%" style="margin-bottom:8px;">
    <tr>
        <td class="no-border" width="58%"></td>
        <td class="no-border" width="27%">
            Bandar Lampung,
            {{ \Carbon\Carbon::parse($pengajuan->created_at ?? now())->locale('id')->translatedFormat('d F Y') }}
        </td>
    </tr>
    <tr>
        <td class="no-border"></td>
        <td class="no-border">
            Kepada
        </td>
        <td class="no-border"></td>
    </tr>
    <tr>
        <td class="no-border" style="text-align:right; padding-right:8px;">
            Yth.
        </td>
        <td class="no-border" style="white-space: nowrap;">
            {{ $headerTujuanLine1 }}
        </td>
        <td class="no-border"></td>
    </tr>
    @if(!empty($headerTujuanLine2))
    <tr>
        <td class="no-border"></td>
        <td class="no-border">
            {{ $headerTujuanLine2 }}
        </td>
        <td class="no-border"></td>
    </tr>
    @endif
    <tr>
        <td class="no-border"></td>
        <td class="no-border">
            <div style="padding-left:0;">
                di
            </div>
            <div style="padding-left:18px;">
                TELUKBETUNG
            </div>
        </td>
        <td class="no-border"></td>
    </tr>
</table>
<div style="height:6px;"></div>

<div class="title">FORMULIR PERMINTAAN DAN PEMBERIAN CUTI</div>
<div style="height:6px;"></div>

{{-- I. DATA PEGAWAI --}}
<table>
    <tr><td colspan="4" class="bold">I. DATA PEGAWAI</td></tr>
    <tr>
        <td width="10%" style="padding-bottom:5px;">Nama</td>
        <td width="41%" class="bold">{{ $pegawai->nama_lengkap }}</td>
        <td width="13%" style="padding-bottom:5px;">NIP</td>
        <td width="34%">{{ $pegawai->nip }}</td>
    </tr>
    <tr>
        <td style="padding-bottom:5px;">Jabatan</td>
        <td>{{ $jabatan->nama_jabatan }}</td>
        <td style="padding-bottom:5px;">Masa Kerja</td>
        <td>{{ $pegawai->masa_kerja }}</td>
    </tr>
    <tr>
        <td style="padding-bottom:5px;">Unit Kerja</td>
        <td>Dinas Kelautan dan Perikanan Provinsi Lampung</td>
        <td style="padding-bottom:5px;">Pangkat/Gol</td>
        <td>{{ $pegawai->pangkat }}</td>
    </tr>
</table>
<div style="height:6px;"></div>

{{-- II. JENIS CUTI YANG DIAMBIL --}}
<table>
    <tr><td colspan="4" class="bold">II. JENIS CUTI YANG DIAMBIL **</td></tr>
    <tr>
        <td width="30%">1. Cuti Tahunan</td>
        <td class="checkbox" width="19%"><span class="checkbox-symbol">{{ $jenisCuti->kode_cuti == 'CT'  ? '√' : '' }}</span></td>
        <td width="23%">2. Cuti Besar</td>
        <td class="checkbox" width="28%"><span class="checkbox-symbol">{{ in_array($jenisCuti->kode_cuti, ['CB', 'CB_UMROH', 'CB_HAJI'])  ? '√' : '' }}</span></td>
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
<div style="height:6px;"></div>

{{-- III. ALASAN CUTI --}}
<table style="table-layout: fixed;">
    <tr><td class="bold">III. ALASAN CUTI</td></tr>
    <tr><td style="height:32px; vertical-align:top;">{{ $pengajuan->alasan_cuti }}</td></tr>
</table>
<div style="height:6px;"></div>

{{-- IV. LAMANYA CUTI & V. CATATAN CUTI --}}
<table style="margin-bottom:0px;">
    <tr><td colspan="6" class="bold">IV. LAMANYA CUTI</td></tr>
    <tr>
        <td width="10%" style="border-bottom:none;">Selama</td>
        <td width="28%" style="border-bottom:none;">
            <b>{{ $displayAngka }}</b> {!! $formatStrikethrough !!}
        </td>
        <td width="14%" style="border-bottom:none;">Mulai Tanggal</td>
        <td width="20%" style="border-bottom:none;">{{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->translatedFormat('d F Y') }}</td>
        <td width="6%" class="center" style="border-bottom:none;">s/d</td>
        <td width="22%" style="border-bottom:none;">{{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->translatedFormat('d F Y') }}</td>
    </tr>
</table>
<table style="margin-top:0px;">
    <tr>
        <td colspan="5" class="bold">V. CATATAN CUTI</td>
    </tr>
    <tr>
        <td colspan="3" width="35%">1. CUTI TAHUNAN</td>
        <td width="42%">2. CUTI BESAR</td>
        <td width="23%"></td>
    </tr>
    <tr>
        <td width="12%" class="left">Tahun</td>
        <td width="8%" class="left">Sisa</td>
        <td width="15%" class="left">Keterangan</td>
        <td width="42%">3. CUTI SAKIT</td>
        <td width="23%"></td>
    </tr>
    <tr>
        <td class="left">N-2</td>
        <td class="left">0</td>
        <td></td>
        <td>4. CUTI MELAHIRKAN</td>
        <td></td>
    </tr>
    <tr>
        <td class="left">N-1</td>
        <td class="left">0</td>
        <td></td>
        <td>5. CUTI KARENA ALASAN PENTING</td>
        <td></td>
    </tr>
    <tr>
        <td class="left">N</td>
        <td class="left">{{ $pegawai->sisa_cuti_tahunan }}</td>
        <td></td>
        <td>6. CUTI DI LUAR TANGGUNGAN NEGARA</td>
        <td></td>
    </tr>
</table>
<div style="height:6px;"></div>

{{-- VI. ALAMAT SELAMA MENJALANKAN CUTI --}}
<table style="margin-top:4px;">
    <tr>
        <td colspan="2" class="bold">VI. ALAMAT SELAMA MENJALANKAN CUTI</td>
    </tr>

    <tr>
        <td width="52%" style="border-bottom:none;"></td>
        <td width="48%" style="padding:0; border-bottom:none;">
            <table style="width:100%; border-collapse:collapse; margin:0; padding:0;">
                <tr>
                    <td width="29%" style="padding:0 3px;line-height:18px;border-top:none;border-left:none;border-right:1px solid #000;">
                        TELP
                    </td>
                    <td style="padding:0 3px;line-height:18px;border-top:none;border-left:none;border-right:none;">
                        {{ $pengajuan->no_telp_selama_cuti ?? '-' }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td width="52%" style="height:42px; vertical-align:top; padding-top:4px;">
            {{ $pengajuan->alamat_selama_cuti }}
        </td>
        <td width="48%" style="text-align:center; vertical-align:top; border-top:none;">
            Hormat saya,
            <div style="height:48px;"></div>
            <div style="font-weight:bold;">
                <span style="text-decoration: underline;">{{ $pegawai->nama_lengkap }}</span><br>
                NIP. {{ $pegawai->nip }}
            </div>
        </td>
    </tr>
</table>
<div style="height:6px;"></div>

{{-- VII. PERTIMBANGAN ATASAN LANGSUNG --}}
<table>
    <tr>
        <td colspan="4" class="bold" style="text-align:left; padding-left:4px;">
            VII. PERTIMBANGAN ATASAN LANGSUNG
        </td>
    </tr>
    <tr>
        <td class="center bold" width="12%">DISETUJUI</td>
        <td class="center bold" width="17%">PERUBAHAN****</td>
        <td class="center bold" width="20%">DITANGGUHKAN****</td>
        <td class="center bold" width="51%">TIDAK DISETUJUI****</td>
    </tr>
    <tr>
        <td style="height:56px;"></td>
        <td></td>
        <td></td>
        <td style="padding:0; vertical-align:top;">

            <table style="width:100%; border-collapse:collapse; border:none; margin:0;">
                <!-- BARIS KOSONG -->
                <tr>
                    <td style="height:8px; border:none; border-bottom:0.5px solid #000;"></td>
                </tr>
                
                <tr>
                    <td style="border:none; text-align:center; vertical-align:top;">
                        <div style="font-weight:bold;">
                            {{ strtoupper($pengajuan->atasan_jabatan ?? 'KEPALA DINAS') }}
                        </div>
                        <div style="height:50px;"></div>
                        <div style="display:inline-block;font-weight:bold;border-bottom:1px solid #000;padding:0 2px;line-height:1.1;">
                            {{ strtoupper($pengajuan->atasan_nama ?? 'Ir. BANI ISPRIYANTO M.M.') }}
                        </div>
                        @if(!empty($pengajuan->atasan_nip))
                            <div>
                                NIP. {{ $pengajuan->atasan_nip }}
                            </div>
                        @endif

                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
<div style="height:6px;"></div>

{{-- VIII. KEPUTUSAN PEJABAT YANG BERWENANG --}}
<table>
    <tr><td colspan="4" class="bold center">KEPUTUSAN PEJABAT YANG BERWENANG MEMBERIKAN CUTI</td></tr>
    <tr>
        <td class="center bold" width="20%">DISETUJUI</td>
        <td class="center bold" width="25%">PERUBAHAN****</td>
        <td class="center bold" width="22%">DITANGGUHKAN****</td>
        <td class="center bold" width="33%">TIDAK DISETUJUI****</td>
    </tr>
    
    <tr>
        <td style="height:8px"></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td style="height:65px"></td>
        <td></td>
        <td></td>
        <td style="border-top:none; text-align:center; vertical-align:top;">

            <div style="font-weight:bold;">
                {{ strtoupper($pengajuan->pejabat_jabatan ?? 'Kepala Badan Kepegawaian Daerah') }}
            </div>
            <div style="height:68px;"></div>
            <div style="display:inline-block;font-weight:bold;border-bottom:1px solid #000;padding:0 2px;line-height:1.1;">
                {{ strtoupper($pengajuan->pejabat_nama ?? 'RENDI RESWANDI, S.STP., M.Si') }}
            </div>

            @if(!empty($pengajuan->pejabat_nip))
                <div>
                    NIP. {{ $pengajuan->pejabat_nip }}
                </div>
            @endif

        </td>
    </tr>
</table>

{{-- FOOTER CATATAN --}}
<div style="margin-top:8px; font-size:12px;">
    <div style="margin-bottom:2px;">Catatan :</div>

    <table class="no-border" style="width:100%; border:none; margin:0;">
        <tr>
            <td class="no-border" style="width:8%; padding:0;">*</td>
            <td class="no-border" style="padding:0;">Coret yang tidak perlu</td>
        </tr>

        <tr>
            <td class="no-border" style="padding:0;">**</td>
            <td class="no-border" style="padding:0;">
                Pilih salah satu dengan memberi tanda centang
                <span style="font-family:'DejaVu Sans',sans-serif;">(✓)</span>
            </td>
        </tr>

        <tr>
            <td class="no-border" style="padding:0;">***</td>
            <td class="no-border" style="padding:0;">
                Diisi oleh pejabat yang menangani bidang kepegawaian sebelum PNS mengajukan cuti
            </td>
        </tr>

        <tr>
            <td class="no-border" style="padding:0;">****</td>
            <td class="no-border" style="padding:0;">
                Diberi tanda centang dan alasannya
            </td>
        </tr>

        <tr>
            <td class="no-border" style="padding:0;">N</td>
            <td class="no-border" style="padding:0;">= Cuti tahun berjalan</td>
        </tr>

        <tr>
            <td class="no-border" style="padding:0;">N - 1</td>
            <td class="no-border" style="padding:0;">= Sisa cuti 1 tahun sebelumnya</td>
        </tr>

        <tr>
            <td class="no-border" style="padding:0;">N - 2</td>
            <td class="no-border" style="padding:0;">= Sisa cuti 2 tahun sebelumnya</td>
        </tr>
    </table>
</div>

</body>
</html>