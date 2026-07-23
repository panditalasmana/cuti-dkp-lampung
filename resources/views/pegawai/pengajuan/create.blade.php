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
        <p class="page-subtitle">Isi formulir pengajuan cuti sesuai format resmi ASN BKD Lampung</p>
    </div>
    <a href="{{ route('pegawai.riwayat.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-clock-history me-1"></i>Riwayat
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-10 col-xl-9">
        <div class="card card-custom shadow-sm">
            <div class="card-header-custom bg-primary text-white py-3">
                <h5 class="card-title-custom text-white mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>Formulir Permintaan dan Pemberian Cuti (ASN / PPPK)
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('pegawai.pengajuan.store') }}" id="formPengajuan">
                    @csrf

                    <!-- I. DATA PEGAWAI -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title text-primary fw-bold border-bottom pb-2 mb-3">
                            I. DATA PEGAWAI
                        </h6>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label text-muted small fw-semibold">Nama Lengkap</label>
                                <input type="text" class="form-control bg-light fw-medium" value="{{ $pegawai->nama_lengkap }}" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small fw-semibold">NIP</label>
                                <input type="text" class="form-control bg-light fw-medium" value="{{ $pegawai->nip }}" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small fw-semibold">Jabatan</label>
                                <input type="text" class="form-control bg-light" value="{{ $pegawai->jabatan->nama_jabatan ?? '-' }}" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small fw-semibold">Masa Kerja</label>
                                <input type="text" class="form-control bg-light" value="{{ $pegawai->masa_kerja ?? '-' }}" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small fw-semibold">Unit Kerja</label>
                                <input type="text" class="form-control bg-light" value="Dinas Kelautan dan Perikanan Provinsi Lampung" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small fw-semibold">Pangkat / Golongan</label>
                                <input type="text" class="form-control bg-light" value="{{ $pegawai->pangkat ?? '-' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- II. JENIS CUTI YANG DIAMBIL & IV. LAMANYA CUTI -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title text-primary fw-bold border-bottom pb-2 mb-3">
                            II. JENIS CUTI YANG DIAMBIL & IV. LAMANYA CUTI
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Pilih Jenis Cuti <span class="text-danger">*</span></label>
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

                                <div id="infoJenisCuti" class="alert alert-info mt-2 p-2 small d-none">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <span id="infoJenisCutiText"></span>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Mulai Tanggal <span class="text-danger">*</span></label>
                                <input type="text" name="tanggal_mulai" id="tanggalMulai"
                                       class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                       value="{{ old('tanggal_mulai') }}"
                                       placeholder="Pilih Tanggal Mulai" required>
                                @error('tanggal_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Sampai Dengan Tanggal <span class="text-danger">*</span></label>
                                <input type="text" name="tanggal_selesai" id="tanggalSelesai"
                                       class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                       value="{{ old('tanggal_selesai') }}"
                                       placeholder="Pilih Tanggal Selesai" required>
                                @error('tanggal_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <div id="lamaCutiPreview" class="lama-cuti-box d-none bg-light p-3 rounded border">
                                    <i class="bi bi-calendar2-week text-primary me-2 fs-5"></i>
                                    Durasi Cuti: <strong id="lamaCutiValue" class="text-primary fs-5">0</strong> <span id="lamaCutiSatuan" class="fw-bold">Hari Kerja</span>
                                    <span id="lamaCutiNote" class="text-muted small ms-2">(Senin–Jumat, tidak termasuk tanggal merah)</span>
                                </div>
                                <div id="lamaCutiLoading" class="text-muted small d-none mt-2">
                                    <div class="spinner-border spinner-border-sm me-1 text-primary"></div>Menghitung hari kerja...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- III. ALASAN CUTI -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title text-primary fw-bold border-bottom pb-2 mb-3">
                            III. ALASAN CUTI
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Alasan Lengkap Pengajuan Cuti <span class="text-danger">*</span></label>
                                <textarea name="alasan_cuti" id="alasanCuti" rows="3"
                                          class="form-control @error('alasan_cuti') is-invalid @enderror"
                                          placeholder="Tuliskan alasan permohonan cuti Anda secara jelas..." required
                                          minlength="10" maxlength="1000">{{ old('alasan_cuti') }}</textarea>
                                <div class="d-flex justify-content-between mt-1">
                                    @error('alasan_cuti')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted ms-auto"><span id="charCount">0</span>/1000</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- VI. ALAMAT SELAMA MENJALANKAN CUTI -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title text-primary fw-bold border-bottom pb-2 mb-3">
                            VI. ALAMAT SELAMA MENJALANKAN CUTI
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat Lengkap Selama Cuti <span class="text-danger">*</span></label>
                                <textarea name="alamat_selama_cuti" rows="2"
                                          class="form-control @error('alamat_selama_cuti') is-invalid @enderror"
                                          placeholder="Alamat tempat tinggal/domisili selama menjalankan cuti"
                                          required maxlength="500">{{ old('alamat_selama_cuti', $pegawai->alamat) }}</textarea>
                                @error('alamat_selama_cuti')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">No. Telepon yang Dapat Dihubungi <span class="text-danger">*</span></label>
                                <input type="text" name="no_telp_selama_cuti"
                                       class="form-control @error('no_telp_selama_cuti') is-invalid @enderror"
                                       value="{{ old('no_telp_selama_cuti', $pegawai->no_telepon) }}"
                                       placeholder="08xxxxxxxxxx" maxlength="15" required>
                                @error('no_telp_selama_cuti')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- VII & VIII. PERTIMBANGAN ATASAN & PEJABAT BERWENANG -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title text-primary fw-bold border-bottom pb-2 mb-3">
                            VII & VIII. PENANDATANGAN DOKUMEN CUTI
                        </h6>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Atasan Langsung <span class="text-danger">*</span></label>
                                <select name="atasan_langsung_select" class="form-select @error('atasan_langsung_select') is-invalid @enderror" required>
                                    <option value="">-- Pilih Atasan Langsung --</option>
                                    <option value="Ir. BANI ISPRIYANTO, M.M.|196904101995031002|Kepala Dinas" {{ old('atasan_langsung_select') == 'Ir. BANI ISPRIYANTO, M.M.|196904101995031002|Kepala Dinas' ? 'selected' : '' }}>
                                        Ir. BANI ISPRIYANTO, M.M. (Kepala Dinas - NIP. 196904101995031002)
                                    </option>
                                    <option value="A. FAISAL, A.Pi.|197402031999031006|Sekretaris Dinas" {{ old('atasan_langsung_select') == 'A. FAISAL, A.Pi.|197402031999031006|Sekretaris Dinas' ? 'selected' : '' }}>
                                        A. FAISAL, A.Pi. (Sekretaris Dinas - NIP. 197402031999031006)
                                    </option>
                                    <option value="Hardian Sylvanandra Prayitno S.Pi. MM.|198503222008041001|Kepala Bidang Pengawasan Sumber Daya Kelautan dan Perikanan" {{ old('atasan_langsung_select') == 'Hardian Sylvanandra Prayitno S.Pi. MM|198503222008041001|Kepala Bidang Pengawasan Sumber Daya Kelautan dan Perikanan' ? 'selected' : '' }}>
                                        Hardian Sylvanandra Prayitno S.Pi. MM (Kepala Bidang Pengawasan SDKP - NIP. 198503222008041001) 
                                    </option>
                                    <option value="Sadariah S.P M.M.|197009301996022001|Kepala Bidang Pengelolaan Ruang Laut" {{ old('atasan_langsung_select') == 'Sadariah S.P M.M.|197009301996022001|Kepala Bidang Pengelolaan Ruang Laut' ? 'selected' : '' }}>
                                        Sadariah S.P M.M. (Kepala Bidang PRL - NIP. 197009301996022001) 
                                    </option>
                                    <option value="Nazdan S.Pi. MP.|197306102002121002|Kepala Bidang Perikanan Budidaya dan Penguatan Daya Saing" {{ old('atasan_langsung_select') == 'Nazdan S.Pi. MP.|197306102002121002|Kepala Bidang Perikanan Budidaya dan Penguatan Daya Saing' ? 'selected' : '' }}>
                                        Nazdan S.Pi. MP. (Kepala Bidang PBPDS - NIP. 197306102002121002) 
                                    </option>
                                    <option value="Sri Rihatun Dhamayanti S.Pi. M.M.|197307172000032004|Kepala UPTD Penerapan Mutu Hasil Perikanan" {{ old('atasan_langsung_select') == 'Sri Rihatun Dhamayanti S.Pi. M.M.|197307172000032004|Kepala UPTD Penerapan Mutu Hasil Perikanan' ? 'selected' : '' }}>
                                        Sri Rihatun Dhamayanti S.Pi. M.M. (Kepala UPTD PMHP - NIP. 197307172000032004) 
                                    </option>
                                    <option value="Zainal K S.Pi M.Ling.|197104051997031007|Kepala UPTD Pelabuhan Perikanan Labuhan Maringgai" {{ old('atasan_langsung_select') == 'Zainal K S.Pi M.Ling.|197104051997031007|Kepala UPTD Pelabuhan Perikanan Labuhan Maringgai' ? 'selected' : '' }}>
                                        Zainal K S.Pi M.Ling. (Kepala UPTD PP Labuhan Maringgai - NIP. 197104051997031007) 
                                    </option>
                                    <option value="Ahmad Darma Habibillah S.Pi M.E.|198006182005011007|Kepala UPTD Pelabuhan Perikanan Lempasing" {{ old('atasan_langsung_select') == 'Ahmad Darma Habibillah S.Pi M.E.|198006182005011007|Kepala UPTD Pelabuhan Perikanan Lempasing' ? 'selected' : '' }}>
                                        Ahmad Darma Habibillah S.Pi M.E. (Kepala UPTD PP Lempasing - NIP. 198006182005011007) 
                                    </option>
                                    <option value="Sukarsono S.Pi.|197111081999031004|Kepala UPTD Pelabuhan Perikanan Kota Agung" {{ old('atasan_langsung_select') == 'Sukarsono S.Pi.|197111081999031004|Kepala UPTD Pelabuhan Perikanan Kota Agung' ? 'selected' : '' }}>
                                        Sukarsono S.Pi. (Kepala UPTD PP Kota Agung - NIP. 197111081999031004) 
                                    </option>
                                    <option value="Zulhaidir SP M.Si.|197204292000031003|Kepala UPTD Pelabuhan Perikanan Kalianda" {{ old('atasan_langsung_select') == 'Zulhaidir SP M.Si.|197204292000031003|Kepala UPTD Pelabuhan Perikanan Kalianda' ? 'selected' : '' }}>
                                        Zulhaidir SP M.Si. (Kepala UPTD PP Kalianda - NIP. 197204292000031003) 
                                    </option>
                                </select>
                                @error('atasan_langsung_select')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Pejabat yang Berwenang <span class="text-danger">*</span></label>
                                <select name="pejabat_wenang_select" class="form-select @error('pejabat_wenang_select') is-invalid @enderror" required>
                                    <option value="">-- Pilih Pejabat yang Berwenang --</option>
                                    <option value="RENDY RISWANDI, S.STP.,M.Si|197705261997121001|Kepala Badan Kepegawaian Daerah" {{ old('pejabat_wenang_select') == 'RENDY RISWANDI, S.STP.,M.Si|197705261997121001|Kepala Badan Kepegawaian Daerah' ? 'selected' : '' }}>
                                        RENDI RESWANDI, S.STP.,M.Si (Kepala BKD - NIP. 19770526 199712 1 001)
                                    </option>
                                    <option value="Ir. BANI ISPRIYANTO, M.M.|196904101995031002|Kepala Dinas" {{ old('pejabat_wenang_select') == 'Ir. BANI ISPRIYANTO, M.M.|196904101995031002|Kepala Dinas' ? 'selected' : '' }}>
                                        Ir. BANI ISPRIYANTO, M.M. (Kepala Dinas - NIP. 19690410 199503 1 002)
                                    </option>
                                    <option value="Dr. MARINDO KURNIAWAN, S.T., M.M.|198012062002121010|Sekretaris Daerah Provinsi Lampung" {{ old('pejabat_wenang_select') == 'Dr. MARINDO KURNIAWAN, S.T., M.M.|198012062002121010|Sekretaris Daerah Provinsi Lampung' ? 'selected' : '' }}>
                                        Dr. MARINDO KURNIAWAN, S.T., M.M. (Sekretaris Daerah Provinsi Lampung - NIP. 19801206 200212 1 010)
                                    </option>
                                    <option value="RAHMAT MIRZANI DJAUSAL, S.T., M.M.|-|Gubernur Lampung" {{ old('pejabat_wenang_select') == 'RAHMAT MIRZANI DJAUSAL, S.T., M.M.|-|Gubernur Lampung' ? 'selected' : '' }}>
                                        RAHMAT MIRZANI DJAUSAL, S.T., M.M. (Gubernur Lampung)
                                    </option>
                                </select>
                                @error('pejabat_wenang_select')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 mt-3">
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

                    <div class="alert alert-warning border-0 shadow-sm mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <strong>Perhatian:</strong> Setelah permohonan dikirim, sistem akan otomatis menerbitkan PDF Surat Cuti sesuai format resmi BKD Lampung di atas.
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('pegawai.dashboard') }}" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4" id="btnSubmit">
                            <i class="bi bi-send me-1"></i>Kirim Pengajuan Cuti
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
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

const fpMulai = flatpickr("#tanggalMulai", {
    locale: "id",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d F Y",
    minDate: "today",
    disable: [
        function(date) {
            return (date.getDay() === 0 || date.getDay() === 6);
        },
        ...usedDates
    ],
    onChange: function(selectedDates, dateStr) {
        if (selectedDates[0]) {
            fpSelesai.set("minDate", dateStr);
        }
        hitungHari();
    }
});

const fpSelesai = flatpickr("#tanggalSelesai", {
    locale: "id",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d F Y",
    minDate: "today",
    disable: [
        function(date) {
            return (date.getDay() === 0 || date.getDay() === 6);
        },
        ...usedDates
    ],
    onChange: function() {
        hitungHari();
    }
});

const jenisCutiSelect = document.getElementById('jenisCutiSelect');
const infoJenisCuti   = document.getElementById('infoJenisCuti');
const infoText        = document.getElementById('infoJenisCutiText');

jenisCutiSelect.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const ket      = selected.dataset.keterangan;
    const dasar    = selected.dataset.dasar;

    if (this.value && (ket || dasar)) {
        infoText.innerHTML = (ket ? ket : '') + (dasar ? '<br><small class="text-muted">Dasar Hukum: ' + dasar + '</small>' : '');
        infoJenisCuti.classList.remove('d-none');
    } else {
        infoJenisCuti.classList.add('d-none');
    }
    hitungHari();
});

const alasanInput = document.getElementById('alasanCuti');
const charCount   = document.getElementById('charCount');
alasanInput.addEventListener('input', function() {
    charCount.textContent = this.value.length;
});

function hitungHari() {
    const mulai   = document.getElementById('tanggalMulai').value;
    const selesai = document.getElementById('tanggalSelesai').value;

    const preview = document.getElementById('lamaCutiPreview');
    const valEl   = document.getElementById('lamaCutiValue');
    const loading = document.getElementById('lamaCutiLoading');

    if (!mulai || !selesai) {
        preview.classList.add('d-none');
        return;
    }

    loading.classList.remove('d-none');
    preview.classList.add('d-none');

    fetch("{{ route('pegawai.pengajuan.hitung-hari') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ tanggal_mulai: mulai, tanggal_selesai: selesai })
    })
    .then(res => res.json())
    .then(data => {
        loading.classList.add('d-none');
        if (data.lama_cuti !== undefined) {
            valEl.textContent = data.lama_cuti;
            preview.classList.remove('d-none');
        }
    })
    .catch(() => {
        loading.classList.add('d-none');
    });
}
</script>
@endpush