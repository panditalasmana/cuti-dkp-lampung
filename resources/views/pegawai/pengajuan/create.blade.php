@extends('layouts.app')
@section('title', 'Ajukan Cuti')

@section('breadcrumb')
    <a href="{{ route('pegawai.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Ajukan Cuti</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Pengajuan Cuti</h1>
        <p class="page-subtitle">Isi formulir pengajuan cuti sesuai format ASN</p>
    </div>
    <a href="{{ route('pegawai.riwayat.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-clock-history me-1"></i>Riwayat
    </a>
</div>

<div class="row g-4">
    <!-- Form -->
    <div class="col-12 col-xl-8">
        <div class="card card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom"><i class="bi bi-file-earmark-text me-2"></i>Formulir Pengajuan Cuti ASN</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pegawai.pengajuan.store') }}" id="formPengajuan">
                    @csrf

                    <!-- Data Pegawai (readonly display) -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title"><i class="bi bi-person me-1"></i>Data Pegawai</h6>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label text-muted small">Nama Lengkap</label>
                                <input type="text" class="form-control bg-light" value="{{ $pegawai->nama_lengkap }}" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small">NIP</label>
                                <input type="text" class="form-control bg-light" value="{{ $pegawai->nip }}" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small">Jabatan</label>
                                <input type="text" class="form-control bg-light" value="{{ $pegawai->jabatan->nama_jabatan ?? '-' }}" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small">Bidang / UPTD</label>
                                <input type="text" class="form-control bg-light" value="{{ $pegawai->bidang->nama_bidang ?? '-' }}" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small">Sub Bagian / Seksi</label>
                                <input type="text" class="form-control bg-light" value="{{ $pegawai->sub_bagian ?? '-' }}" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small">Pangkat/Golongan</label>
                                <input type="text" class="form-control bg-light" value="{{ $pegawai->pangkat ?? '-' }}" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small">Sisa Cuti Tahunan</label>
                                <input type="text" class="form-control bg-light fw-bold text-primary"
                                       value="{{ $pegawai->sisa_cuti_tahunan }} hari" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Data Cuti -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title"><i class="bi bi-calendar-check me-1"></i>Data Permohonan Cuti</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Jenis Cuti <span class="text-danger">*</span></label>
                                <select name="jenis_cuti_id" id="jenisCutiSelect"
                                        class="form-select @error('jenis_cuti_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenis Cuti --</option>
                                    @foreach($jenisCuti as $jc)
                                        @if($jc->kode_cuti === 'CM' && $pegawai->jenis_kelamin !== 'P')
                                            @continue
                                        @endif
                                        <option value="{{ $jc->id }}"
                                                data-potong="{{ $jc->potong_kuota ? 1 : 0 }}"
                                                data-keterangan="{{ $jc->keterangan }}"
                                                data-dasar="{{ $jc->dasar_hukum }}"
                                                {{ old('jenis_cuti_id') == $jc->id ? 'selected' : '' }}>
                                            {{ $jc->nama_cuti }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenis_cuti_id')<div class="invalid-feedback">{{ $message }}</div>@enderror

                                <!-- Info Jenis Cuti -->
                                <div id="infoJenisCuti" class="alert alert-info mt-2 p-2 small d-none">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <span id="infoJenisCutiText"></span>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="text" name="tanggal_mulai" id="tanggalMulai"
                                       class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                       value="{{ old('tanggal_mulai') }}"
                                       placeholder="Pilih Tanggal Mulai" required>
                                @error('tanggal_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="text" name="tanggal_selesai" id="tanggalSelesai"
                                       class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                       value="{{ old('tanggal_selesai') }}"
                                       placeholder="Pilih Tanggal Selesai" required>
                                @error('tanggal_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- Preview Lama Cuti -->
                            <div class="col-12">
                                <div id="lamaCutiPreview" class="lama-cuti-box d-none">
                                    <i class="bi bi-calendar2-week me-2"></i>
                                    Lama Cuti: <strong id="lamaCutiValue">0</strong> <span id="lamaCutiSatuan">Hari Kerja</span>
                                    <span id="lamaCutiNote" class="text-muted small">(Senin–Jumat, tidak termasuk weekend)</span>
                                </div>
                                <div id="lamaCutiLoading" class="text-muted small d-none">
                                    <div class="spinner-border spinner-border-sm me-1"></div>Menghitung hari kerja...
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Alasan Permohonan Cuti <span class="text-danger">*</span></label>
                                <textarea name="alasan_cuti" id="alasanCuti" rows="3"
                                          class="form-control @error('alasan_cuti') is-invalid @enderror"
                                          placeholder="Tuliskan alasan pengajuan cuti secara jelas..." required
                                          minlength="10" maxlength="1000">{{ old('alasan_cuti') }}</textarea>
                                <div class="d-flex justify-content-between mt-1">
                                    @error('alasan_cuti')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted ms-auto"><span id="charCount">0</span>/1000</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat Selama Cuti -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title"><i class="bi bi-geo-alt me-1"></i>Alamat Selama Cuti</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat Lengkap Selama Cuti <span class="text-danger">*</span></label>
                                <textarea name="alamat_selama_cuti" rows="2"
                                          class="form-control @error('alamat_selama_cuti') is-invalid @enderror"
                                          placeholder="Alamat tempat tinggal selama menjalani cuti"
                                          required maxlength="500">{{ old('alamat_selama_cuti', $pegawai->alamat) }}</textarea>
                                @error('alamat_selama_cuti')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">No. Telepon yang Dapat Dihubungi</label>
                                <input type="text" name="no_telp_selama_cuti"
                                       class="form-control @error('no_telp_selama_cuti') is-invalid @enderror"
                                       value="{{ old('no_telp_selama_cuti', $pegawai->no_telepon) }}"
                                       placeholder="08xxxxxxxxxx" maxlength="15">
                                @error('no_telp_selama_cuti')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>                    <!-- Penandatangan Dokumen Cuti -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title"><i class="bi bi-pencil-square me-1"></i>Penandatangan Dokumen Cuti</h6>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Tanda Tangan Atasan <span class="text-danger">*</span></label>
                                <select name="atasan_langsung_select" class="form-select @error('atasan_langsung_select') is-invalid @enderror" required>
                                    <option value="">-- Pilih Atasan --</option>
                                    <option value="Ir. BANI ISPRIYANTO, M.M.|196904101995031002|Kepala Dinas" {{ old('atasan_langsung_select') == 'Ir. BANI ISPRIYANTO, M.M.|196904101995031002|Kepala Dinas' ? 'selected' : '' }}>
                                        Ir. BANI ISPRIYANTO, M.M. (Kepala Dinas - NIP. 196904101995031002)
                                    </option>
                                    <option value="A. FAISAL, A.Pi.|197402031999031006|Sekretaris Dinas" {{ old('atasan_langsung_select') == 'A. FAISAL, A.Pi.|197402031999031006|Sekretaris Dinas' ? 'selected' : '' }}>
                                        A. FAISAL, A.Pi. (Sekretaris Dinas - NIP. 197402031999031006)
                                    </option>
                                    <option value="Hardian Sylvanandra Prayitno S.Pi. MM.|198503222008041001|Kepala Bidang Pengawasan Sumber Daya Kelautan dan Perikanan" {{ old('atasan_langsung_select') == 'Hardian Sylvanandra Prayitno S.Pi. MM|198503222008041001|Kepala Bidang Pengawasan Sumber Daya Kelautan dan Perikanan' ? 'selected' : '' }}>
                                        Hardian Sylvanandra Prayitno S.Pi. MM (Kepala Bidang Pengawasan Sumber Daya Kelautan dan Perikanan - NIP. 198503222008041001) 
                                    </option>
                                    <option value="Sadariah S.P M.M.|197009301996022001|Kepala Bidang Pengelolaan Ruang Laut" {{ old('atasan_langsung_select') == 'Sadariah S.P M.M.|197009301996022001|Kepala Bidang Pengelolaan Ruang Laut' ? 'selected' : '' }}>
                                        Sadariah S.P M.M. (Kepala Bidang Pengelolaan Ruang Laut - NIP. 197009301996022001) 
                                    </option>
                                    <option value="Nazdan S.Pi. MP.|197306102002121002|Kepala Bidang Perikanan Budidaya dan Penguatan Daya Saing" {{ old('atasan_langsung_select') == 'Nazdan S.Pi. MP.|197306102002121002|Kepala Bidang Perikanan Budidaya dan Penguatan Daya Saing' ? 'selected' : '' }}>
                                        Nazdan S.Pi. MP. (Kepala Bidang Perikanan Budidaya dan Penguatan Daya Saing - NIP. 197306102002121002) 
                                    </option>
                                    <option value="Sri Rihatun Dhamayanti S.Pi. M.M.|197307172000032004|Kepala UPTD Penerapan Mutu Hasil Perikanan" {{ old('atasan_langsung_select') == 'Sri Rihatun Dhamayanti S.Pi. M.M.|197307172000032004|Kepala UPTD Penerapan Mutu Hasil Perikanan' ? 'selected' : '' }}>
                                        Sri Rihatun Dhamayanti S.Pi. M.M. (Kepala UPTD Penerapan Mutu Hasil Perikanan - NIP. 197307172000032004) 
                                    </option>
                                    <option value="Zainal K S.Pi M.Ling.|197104051997031007|Kepala UPTD Pelabuhan Perikanan Labuhan Maringgai" {{ old('atasan_langsung_select') == 'Zainal K S.Pi M.Ling.|197104051997031007|Kepala UPTD Pelabuhan Perikanan Labuhan Maringgai' ? 'selected' : '' }}>
                                        Zainal K S.Pi M.Ling. (Kepala UPTD Pelabuhan Perikanan Labuhan Maringgai - NIP. 197104051997031007) 
                                    </option>
                                    <option value="Ahmad Darma Habibillah S.Pi M.E.|198006182005011007|Kepala UPTD Pelabuhan Perikanan Lempasing" {{ old('atasan_langsung_select') == 'Ahmad Darma Habibillah S.Pi M.E.|198006182005011007|Kepala UPTD Pelabuhan Perikanan Lempasing' ? 'selected' : '' }}>
                                        Ahmad Darma Habibillah S.Pi M.E. (Kepala UPTD Pelabuhan Perikanan Lempasing - NIP. 198006182005011007) 
                                    </option>
                                    <option value="Sukarsono S.Pi.|197111081999031004|Kepala UPTD Pelabuhan Perikanan Kota Agung" {{ old('atasan_langsung_select') == 'Sukarsono S.Pi.|197111081999031004|Kepala UPTD Pelabuhan Perikanan Kota Agung' ? 'selected' : '' }}>
                                        Sukarsono S.Pi. (Kepala UPTD Pelabuhan Perikanan Kota Agung - NIP. 197111081999031004) 
                                    </option>
                                    <option value="Zulhaidir SP M.Si.|197204292000031003|Kepala UPTD Pelabuhan Perikanan Kalianda" {{ old('atasan_langsung_select') == 'Zulhaidir SP M.Si.|197204292000031003|Kepala UPTD Pelabuhan Perikanan Kalianda' ? 'selected' : '' }}>
                                        Zulhaidir SP M.Si. (Kepala UPTD Pelabuhan Perikanan Kalianda - NIP. 197204292000031003) 
                                    </option>
                                </select>
                                @error('atasan_langsung_select')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Tanda Tangan Pejabat yang Berwenang <span class="text-danger">*</span></label>
                                <select name="pejabat_wenang_select" class="form-select @error('pejabat_wenang_select') is-invalid @enderror" id="pejabatWenangSelect" required>
                                    <option value="">-- Pilih Pejabat yang Berwenang --</option>
                                    <option value="RAHMAT MIRZANI DJAUSAL|-|Gubernur Lampung" {{ old('pejabat_wenang_select') == 'RAHMAT MIRZANI DJAUSAL|-|Gubernur Lampung' ? 'selected' : '' }}>
                                        RAHMAT MIRZANI DJAUSAL (Gubernur Lampung)
                                    </option>
                                    <option value="Dr. MARINDO KURNIAWAN, S.T., M.M.|198012062002121010|Sekretaris Daerah Provinsi Lampung" {{ old('pejabat_wenang_select') == 'Dr. MARINDO KURNIAWAN, S.T., M.M.|198012062002121010|Sekretaris Daerah Provinsi Lampung' ? 'selected' : '' }}>
                                        Dr. MARINDO KURNIAWAN, S.T., M.M. (Sekretaris Daerah Provinsi Lampung - NIP. 198012062002121010)
                                    </option>
                                    <option value="RENDY RISWANDI.S.STP, M.Si|197705261997121001|Kepala Badan Kepegawaian Daerah" {{ old('pejabat_wenang_select') == 'RENDY RISWANDI.S.STP, M.Si|197705261997121001|Kepala Badan Kepegawaian Daerah' ? 'selected' : '' }}>
                                        RENDY RISWANDI.S.STP, M.Si (Kepala BKD - NIP. 197705261997121001)
                                    </option>
                                    <option value="Ir. BANI ISPRIYANTO, M.M.|196904101995031002|Kepala Dinas" {{ old('pejabat_wenang_select') == 'Ir. BANI ISPRIYANTO, M.M.|196904101995031002|Kepala Dinas' ? 'selected' : '' }}>
                                        Ir. BANI ISPRIYANTO, M.M. (Kepala Dinas - NIP. 196904101995031002)
                                    </option>
                                </select>
                                @error('pejabat_wenang_select')<div class="invalid-feedback">{{ $message }}</div>@enderror

                                <!-- Pratinjau Tujuan Surat PDF -->
                                <div id="tujuanHeaderPreview" class="mt-2 p-2.5 rounded-3 border border-info-subtle bg-info-subtle text-info-emphasis d-none" style="font-size:0.875rem;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-file-earmark-text-fill text-info fs-5"></i>
                                        <div>
                                            <span class="small text-muted d-block" style="font-size:0.75rem;">Tujuan Surat (Kepada Yth.) pada PDF:</span>
                                            <strong id="tujuanHeaderText" class="fw-bold">Kepada Yth. Kepala Dinas Kelautan dan Perikanan Provinsi Lampung</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Paraf Eselon 4 (Opsional) -->
                            <div class="col-12 mt-2">
                                <label class="form-label fw-semibold">Paraf Eselon 4 <span class="text-muted small">(Opsional)</span></label>
                                <select name="eselon_3" class="form-select @error('eselon_3') is-invalid @enderror">
                                    <option value="">-- Pilih Paraf Eselon 4 (Opsional) --</option>
                                    
                                    <optgroup label="Kasubag / Kepala Sub Bagian">
                                        <option value="Imam Komarita S.Pi. M.Si.|198601302011011004|Kepala Sub Bagian Umum dan Kepegawaian Sekretariat Dinas" {{ old('eselon_3') == 'Imam Komarita S.Pi. M.Si.|198601302011011004|Kepala Sub Bagian Umum dan Kepegawaian Sekretariat Dinas' ? 'selected' : '' }}>
                                            Imam Komarita S.Pi. M.Si. (Kasubag Umum & Kepegawaian - NIP. 198601302011011004)
                                        </option>
                                        <option value="Sri Hartati S.Pi. M.Ling.|197704132010012004|Kepala Sub Bagian Keuangan dan Aset Sekretariat Dinas" {{ old('eselon_3') == 'Sri Hartati S.Pi. M.Ling.|197704132010012004|Kepala Sub Bagian Keuangan dan Aset Sekretariat Dinas' ? 'selected' : '' }}>
                                            Sri Hartati S.Pi. M.Ling. (Kasubag Keuangan & Aset - NIP. 197704132010012004)
                                        </option>
                                        <option value="Fahrul Rozi S.E|198009022006041008|Kepala Sub Bagian Tata Usaha UPTD Pelabuhan Perikanan Lempasing" {{ old('eselon_3') == 'Fahrul Rozi S.E|198009022006041008|Kepala Sub Bagian Tata Usaha UPTD Pelabuhan Perikanan Lempasing' ? 'selected' : '' }}>
                                            Fahrul Rozi S.E (Kasubag TU UPTD PP Lempasing - NIP. 198009022006041008)
                                        </option>
                                        <option value="Sarnubi S.Sos|196908242003121001|Kepala Sub Bagian Tata Usaha UPTD Pelabuhan Perikanan Labuhan Maringgai" {{ old('eselon_3') == 'Sarnubi S.Sos|196908242003121001|Kepala Sub Bagian Tata Usaha UPTD Pelabuhan Perikanan Labuhan Maringgai' ? 'selected' : '' }}>
                                            Sarnubi S.Sos (Kasubag TU UPTD PP Labuhan Maringgai - NIP. 196908242003121001)
                                        </option>
                                        <option value="Rifki Andrias S.H. M.H|198503092009021003|Kepala Sub Bagian Tata Usaha UPTD Pelabuhan Perikanan Kota Agung" {{ old('eselon_3') == 'Rifki Andrias S.H. M.H|198503092009021003|Kepala Sub Bagian Tata Usaha UPTD Pelabuhan Perikanan Kota Agung' ? 'selected' : '' }}>
                                            Rifki Andrias S.H. M.H (Kasubag TU UPTD PP Kota Agung - NIP. 198503092009021003)
                                        </option>
                                        <option value="Zulkifli Masruri S.A.N M.M|198010062002121003|Kepala Sub Bagian Tata Usaha UPTD Penerapan Mutu Hasil Perikanan" {{ old('eselon_3') == 'Zulkifli Masruri S.A.N M.M|198010062002121003|Kepala Sub Bagian Tata Usaha UPTD Penerapan Mutu Hasil Perikanan' ? 'selected' : '' }}>
                                            Zulkifli Masruri S.A.N M.M (Kasubag TU UPTD PMHP - NIP. 198010062002121003)
                                        </option>
                                        <option value="Oca Patrycia S.Pd. M.IP|198204092009032003|Kepala Sub Bagian Tata Usaha UPTD Balai Perikanan Budidaya Air Laut dan Payau" {{ old('eselon_3') == 'Oca Patrycia S.Pd. M.IP|198204092009032003|Kepala Sub Bagian Tata Usaha UPTD Balai Perikanan Budidaya Air Laut dan Payau' ? 'selected' : '' }}>
                                            Oca Patrycia S.Pd. M.IP (Kasubag TU UPTD BPBALP - NIP. 198204092009032003)
                                        </option>
                                    </optgroup>
                                    
                                    <optgroup label="Kepala Seksi UPTD">
                                        <option value="Heru Romadi SE M.M|197108231998031007|Kepala Seksi Pengusahaan UPTD Pelabuhan Perikanan Lempasing" {{ old('eselon_3') == 'Heru Romadi SE M.M|197108231998031007|Kepala Seksi Pengusahaan UPTD Pelabuhan Perikanan Lempasing' ? 'selected' : '' }}>
                                            Heru Romadi SE M.M (Kasi Pengusahaan UPTD PP Lempasing - NIP. 197108231998031007)
                                        </option>
                                        <option value="Ummu Hanny S.Pi. M.Si|198007092006042008|Kepala Seksi Sarana dan Prasarana UPTD Pelabuhan Perikanan Kota Agung" {{ old('eselon_3') == 'Ummu Hanny S.Pi. M.Si|198007092006042008|Kepala Seksi Sarana dan Prasarana UPTD Pelabuhan Perikanan Kota Agung' ? 'selected' : '' }}>
                                            Ummu Hanny S.Pi. M.Si (Kasi Sarpras UPTD PP Kota Agung - NIP. 198007092006042008)
                                        </option>
                                        <option value="Reni Sri Mulyani S.ST. M.M|197309091993032004|Kepala Seksi Pengembangan Hasil Perikanan UPTD Penerapan Mutu Hasil Perikanan" {{ old('eselon_3') == 'Reni Sri Mulyani S.ST. M.M|197309091993032004|Kepala Seksi Pengembangan Hasil Perikanan UPTD Penerapan Mutu Hasil Perikanan' ? 'selected' : '' }}>
                                            Reni Sri Mulyani S.ST. M.M (Kasi Pengembangan Hasil Perikanan UPTD PMHP - NIP. 197309091993032004)
                                        </option>
                                        <option value="Okta Lindra Saputra S.St.Pi. M.M|198010262010011008|Kepala Seksi Sarana dan Prasarana UPTD Pelabuhan Perikanan Lempasing" {{ old('eselon_3') == 'Okta Lindra Saputra S.St.Pi. M.M|198010262010011008|Kepala Seksi Sarana dan Prasarana UPTD Pelabuhan Perikanan Lempasing' ? 'selected' : '' }}>
                                            Okta Lindra Saputra S.St.Pi. M.M (Kasi Sarpras UPTD PP Lempasing - NIP. 198010262010011008)
                                        </option>
                                        <option value="RR. Retno Koostati S.Pi M.Ling|197008242005012011|Kepala Seksi Pengujian dan Monitoring UPTD Penerapan Mutu Hasil Perikanan" {{ old('eselon_3') == 'RR. Retno Koostati S.Pi M.Ling|197008242005012011|Kepala Seksi Pengujian dan Monitoring UPTD Penerapan Mutu Hasil Perikanan' ? 'selected' : '' }}>
                                            RR. Retno Koostati S.Pi M.Ling (Kasi Pengujian & Monitoring UPTD PMHP - NIP. 197008242005012011)
                                        </option>
                                        <option value="Dedi Sukma Supriatna S.St.Pi. M.M.|198607152010011013|Kepala Seksi Sarana dan Prasarana UPTD Pelabuhan Perikanan Labuhan Maringgai" {{ old('eselon_3') == 'Dedi Sukma Supriatna S.St.Pi. M.M.|198607152010011013|Kepala Seksi Sarana dan Prasarana UPTD Pelabuhan Perikanan Labuhan Maringgai' ? 'selected' : '' }}>
                                            Dedi Sukma Supriatna S.St.Pi. M.M. (Kasi Sarpras UPTD PP Labuhan Maringgai - NIP. 198607152010011013)
                                        </option>
                                        <option value="Fredy Agusta S.Pi M.Si|198508022010011012|Kepala Seksi Pengusahaan UPTD Pelabuhan Perikanan Labuhan Maringgai" {{ old('eselon_3') == 'Fredy Agusta S.Pi M.Si|198508022010011012|Kepala Seksi Pengusahaan UPTD Pelabuhan Perikanan Labuhan Maringgai' ? 'selected' : '' }}>
                                            Fredy Agusta S.Pi M.Si (Kasi Pengusahaan UPTD PP Labuhan Maringgai - NIP. 198508022010011012)
                                        </option>
                                        <option value="Yeni Melia S.E. M.M|198106022010012003|Kepala Seksi Pengusahaan UPTD Pelabuhan Perikanan Kota Agung" {{ old('eselon_3') == 'Yeni Melia S.E. M.M|198106022010012003|Kepala Seksi Pengusahaan UPTD Pelabuhan Perikanan Kota Agung' ? 'selected' : '' }}>
                                            Yeni Melia S.E. M.M (Kasi Pengusahaan UPTD PP Kota Agung - NIP. 198106022010012003)
                                        </option>
                                        <option value="Loekito Agung Pambudi S.Pi|197703132010011006|Kepala Seksi Pelayanan dan Pengendalian Mutu Perbenihan UPTD Balai Perikanan Budidaya Air Laut dan Payau" {{ old('eselon_3') == 'Loekito Agung Pambudi S.Pi|197703132010011006|Kepala Seksi Pelayanan dan Pengendalian Mutu Perbenihan UPTD Balai Perikanan Budidaya Air Laut dan Payau' ? 'selected' : '' }}>
                                            Loekito Agung Pambudi S.Pi (Kasi Yan & Dal Mutu Perbenihan UPTD BPBALP - NIP. 197703132010011006)
                                        </option>
                                        <option value="Doni Widyasmoro S.Pi. M.Si.|198310242010011011|Kepala Seksi Sarana dan Prasarana UPTD Pelabuhan Perikanan Kalianda" {{ old('eselon_3') == 'Doni Widyasmoro S.Pi. M.Si.|198310242010011011|Kepala Seksi Sarana dan Prasarana UPTD Pelabuhan Perikanan Kalianda' ? 'selected' : '' }}>
                                            Doni Widyasmoro S.Pi. M.Si. (Kasi Sarpras UPTD PP Kalianda - NIP. 198310242010011011)
                                        </option>
                                        <option value="Nelson Parizal S.Pi|197908242010011002|Kepala Seksi Pengusahaan UPTD Pelabuhan Perikanan Kalianda" {{ old('eselon_3') == 'Nelson Parizal S.Pi|197908242010011002|Kepala Seksi Pengusahaan UPTD Pelabuhan Perikanan Kalianda' ? 'selected' : '' }}>
                                            Nelson Parizal S.Pi (Kasi Pengusahaan UPTD PP Kalianda - NIP. 197908242010011002)
                                        </option>
                                    </optgroup>
                                </select>
                                @error('eselon_3')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Perhatian:</strong> Setelah pengajuan dikirim, sistem akan otomatis membuat surat cuti dalam format PDF.
                        Cetak surat tersebut, minta tanda tangan Kepala Bidang, kemudian serahkan ke Admin untuk diverifikasi.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="bi bi-send me-1"></i>Kirim Pengajuan
                        </button>
                        <a href="{{ route('pegawai.dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-12 col-xl-4">
        <div class="card card-custom mb-4">
            <div class="card-header-custom">
                <h5 class="card-title-custom"><i class="bi bi-info-circle me-1"></i>Informasi Penting</h5>
            </div>
            <div class="card-body">
                <div class="info-steps">
                    <div class="info-step">
                        <div class="info-step__num">1</div>
                        <div class="info-step__text">Isi formulir pengajuan cuti dengan lengkap dan benar.</div>
                    </div>
                    <div class="info-step">
                        <div class="info-step__num">2</div>
                        <div class="info-step__text">Sistem otomatis membuat PDF surat cuti resmi ASN.</div>
                    </div>
                    <div class="info-step">
                        <div class="info-step__num">3</div>
                        <div class="info-step__text">Cetak surat, minta tanda tangan Kepala Bidang secara fisik.</div>
                    </div>
                    <div class="info-step">
                        <div class="info-step__num">4</div>
                        <div class="info-step__text">Serahkan formulir fisik yang telah ditandatangani kepada Admin.</div>
                    </div>
                    <div class="info-step">
                        <div class="info-step__num">5</div>
                        <div class="info-step__text">Admin memverifikasi dan mengunggah scan surat. Status diperbarui.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom"><i class="bi bi-calendar-check me-1"></i>Jenis Cuti Tersedia</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($jenisCuti as $jc)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold small">{{ $jc->nama_cuti }}</div>
                            </div>
                            @if($jc->potong_kuota)
                                <span class="badge bg-warning text-dark small">Potong Cuti Tahunan</span>
                            @else
                                <span class="badge bg-success text-white small">Kuota Terpisah</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    /* Styling Flatpickr disabled days to be red and clearly marked */
    .flatpickr-day.flatpickr-disabled,
    .flatpickr-day.flatpickr-disabled:hover {
        background-color: #f8d7da !important;
        color: #dc3545 !important;
        cursor: not-allowed !important;
        text-decoration: line-through;
        opacity: 0.8;
    }
</style>

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const sisaCuti  = {{ $pegawai->sisa_cuti_tahunan }};
const usedDates = @json($usedDates);

// Inisialisasi Flatpickr dengan daftar tanggal terpakai
const fpMulai = flatpickr("#tanggalMulai", {
    locale: "id",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d F Y",
    minDate: "today",
    disable: usedDates,
    onChange: function(selectedDates, dateStr, instance) {
        if (selectedDates.length > 0) {
            fpSelesai.set("minDate", dateStr);
            
            if (checkDateConflict()) return;
            hitungLamaCuti();
        }
    }
});

const fpSelesai = flatpickr("#tanggalSelesai", {
    locale: "id",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d F Y",
    minDate: "today",
    disable: usedDates,
    onChange: function(selectedDates, dateStr, instance) {
        if (checkDateConflict()) return;
        hitungLamaCuti();
    }
});

// Periksa apakah rentang tanggal yang dipilih menabrak hari yang sudah terpakai
function checkDateConflict() {
    const mulaiVal = document.getElementById('tanggalMulai').value;
    const selesaiVal = document.getElementById('tanggalSelesai').value;

    if (!mulaiVal || !selesaiVal) return false;

    const start = new Date(mulaiVal);
    const end = new Date(selesaiVal);
    
    let hasConflict = false;
    let conflictDateStr = "";

    let current = new Date(start);
    while (current <= end) {
        const yyyy = current.getFullYear();
        const mm = String(current.getMonth() + 1).padStart(2, '0');
        const dd = String(current.getDate()).padStart(2, '0');
        const dateStr = `${yyyy}-${mm}-${dd}`;

        if (usedDates.includes(dateStr)) {
            hasConflict = true;
            conflictDateStr = dateStr;
            break;
        }
        current.setDate(current.getDate() + 1);
    }

    if (hasConflict) {
        const formattedConflictDate = new Date(conflictDateStr).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        Swal.fire({
            title: 'Hari Sudah Terpakai!',
            text: `Rentang tanggal yang Anda pilih menabrak tanggal cuti yang sudah Anda ajukan sebelumnya (${formattedConflictDate}). Silakan pilih tanggal lain.`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545',
        });

        fpMulai.clear();
        fpSelesai.clear();
        document.getElementById('lamaCutiPreview').classList.add('d-none');
        return true;
    }
    return false;
}

// Hitung lama cuti otomatis via AJAX
let hitungTimer = null;

function hitungLamaCuti() {
    const mulai   = document.getElementById('tanggalMulai').value;
    const selesai = document.getElementById('tanggalSelesai').value;

    const jenisCutiId = document.getElementById('jenisCutiSelect').value;

    if (!mulai || !selesai || selesai < mulai || !jenisCutiId) {
        document.getElementById('lamaCutiPreview').classList.add('d-none');
        return;
    }

    document.getElementById('lamaCutiLoading').classList.remove('d-none');
    document.getElementById('lamaCutiPreview').classList.add('d-none');

    clearTimeout(hitungTimer);
    hitungTimer = setTimeout(() => {
        fetch('{{ route("pegawai.pengajuan.hitung-hari") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ tanggal_mulai: mulai, tanggal_selesai: selesai, jenis_cuti_id: jenisCutiId }),
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('lamaCutiLoading').classList.add('d-none');
            document.getElementById('lamaCutiValue').textContent = data.lama_cuti;
            document.getElementById('lamaCutiSatuan').textContent = data.satuan_display;
            
            const noteDisplay = document.getElementById('lamaCutiNote');
            if (noteDisplay) {
                if (data.satuan_display === 'Hari Kerja') {
                    noteDisplay.textContent = '(Senin–Jumat, tidak termasuk weekend)';
                } else {
                    noteDisplay.textContent = '(Termasuk akhir pekan dan hari libur)';
                }
            }
            
            document.getElementById('lamaCutiPreview').classList.remove('d-none');

            // Cek jenis cuti potong kuota
            const sel = document.getElementById('jenisCutiSelect');
            if (sel.value) {
                const opt = sel.options[sel.selectedIndex];
                if (opt.dataset.potong === '1' && data.lama_cuti > sisaCuti) {
                    document.getElementById('lamaCutiPreview').classList.add('lama-cuti-box--danger');
                } else {
                    document.getElementById('lamaCutiPreview').classList.remove('lama-cuti-box--danger');
                }
            }
        })
        .catch(() => {
            document.getElementById('lamaCutiLoading').classList.add('d-none');
        });
    }, 500);
}

// Jenis Cuti Info
document.getElementById('jenisCutiSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const info = document.getElementById('infoJenisCuti');
    const text = document.getElementById('infoJenisCutiText');

    if (this.value && opt.dataset.keterangan) {
        text.textContent = opt.dataset.keterangan;
        if (opt.dataset.dasar) {
            text.textContent += ' | Dasar Hukum: ' + opt.dataset.dasar;
        }
        info.classList.remove('d-none');
    } else {
        info.classList.add('d-none');
    }
    hitungLamaCuti();
});

// Char counter alasan
document.getElementById('alasanCuti').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
});

// Listener Pratinjau Tujuan Surat PDF
const pejabatSelect = document.getElementById('pejabatWenangSelect');
const previewBox = document.getElementById('tujuanHeaderPreview');
const previewText = document.getElementById('tujuanHeaderText');

function updateTujuanPreview() {
    if (!pejabatSelect || !previewBox || !previewText) return;
    const val = pejabatSelect.value;
    if (!val) {
        previewBox.classList.add('d-none');
        return;
    }
    
    const parts = val.split('|');
    const jabatan = (parts[2] || '').toLowerCase();
    
    let tujuan = '';
    if (jabatan.includes('gubernur')) {
        tujuan = 'Kepada Yth. Gubernur Lampung';
    } else if (jabatan.includes('sekretaris daerah') || jabatan.includes('sekda')) {
        tujuan = 'Kepada Yth. Sekretaris Daerah Provinsi Lampung';
    } else if (jabatan.includes('dinas')) {
        tujuan = 'Kepada Yth. Kepala Dinas Kelautan dan Perikanan Provinsi Lampung';
    } else if (jabatan.includes('bkd') || jabatan.includes('kepegawaian daerah')) {
        tujuan = 'Kepada Yth. Kepala Badan Kepegawaian Daerah Provinsi Lampung';
    } else {
        tujuan = 'Kepada Yth. ' + parts[2] + ' Provinsi Lampung';
    }
    
    previewText.textContent = tujuan;
    previewBox.classList.remove('d-none');
}

if (pejabatSelect) {
    pejabatSelect.addEventListener('change', updateTujuanPreview);
    updateTujuanPreview();
}

// Submit confirm
document.getElementById('formPengajuan').addEventListener('submit', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Kirim Pengajuan Cuti?',
        text: 'Setelah dikirim, pengajuan tidak dapat diubah. Pastikan semua data sudah benar.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kirim',
        cancelButtonText: 'Cek Kembali',
        confirmButtonColor: '#0B5FA5',
    }).then(r => { if (r.isConfirmed) this.submit(); });
});
</script>
@endpush