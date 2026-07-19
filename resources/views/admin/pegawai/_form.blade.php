<div class="row g-3">
    {{-- NIP (only on create) --}}
    @if(!isset($pegawai))
    <div class="col-sm-6">
        <label class="form-label fw-semibold">NIP <span class="text-danger">*</span></label>
        <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
               value="{{ old('nip') }}" placeholder="18 digit NIP" maxlength="18" inputmode="numeric" autocomplete="new-username" required>
        @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @else
    <div class="col-sm-6">
        <label class="form-label fw-semibold">NIP</label>
        <input type="text" class="form-control bg-light" value="{{ $pegawai->nip }}" readonly>
        <div class="form-text">NIP tidak dapat diubah.</div>
    </div>
    @endif

    <div class="col-sm-6">
        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror"
               value="{{ old('nama_lengkap', $pegawai->nama_lengkap ?? '') }}" placeholder="Nama lengkap beserta gelar" required>
        @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-6">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $pegawai->email ?? '') }}" placeholder="email@dkp.lampungprov.go.id" autocomplete="new-username">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-6">
        <label class="form-label fw-semibold">Password {{ isset($pegawai) ? '(Kosongkan jika tidak diubah)' : '' }}</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
               placeholder="{{ isset($pegawai) ? 'Isi untuk mengubah password' : 'Default: 4 digit depan NIP' }}"
               autocomplete="new-password">
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12"><hr class="my-1"><p class="text-muted small fw-semibold mb-0">Data Kepegawaian</p></div>

    <div class="col-sm-6">
        <label class="form-label fw-semibold">Bidang <span class="text-danger">*</span></label>
        <select name="bidang_id" id="bidangSelect" class="form-select @error('bidang_id') is-invalid @enderror" required>
            <option value="">-- Pilih Bidang --</option>
            @foreach($bidang as $b)
                <option value="{{ $b->id }}" data-kode="{{ $b->kode_bidang }}" {{ old('bidang_id', $pegawai->bidang_id ?? '') == $b->id ? 'selected' : '' }}>
                    {{ $b->nama_bidang }}
                </option>
            @endforeach
        </select>
        @error('bidang_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-6">
        <label class="form-label fw-semibold">Sub Bagian</label>
        <select name="sub_bagian" id="subBagianSelect" class="form-select @error('sub_bagian') is-invalid @enderror">
            <option value="">-- Pilih Sub Bagian (Opsional) --</option>
            <option value="Sub Bagian Keuangan dan Aset" {{ old('sub_bagian', $pegawai->sub_bagian ?? '') === 'Sub Bagian Keuangan dan Aset' ? 'selected' : '' }}>Sub Bagian Keuangan dan Aset</option>
            <option value="Sub Bagian Umum dan Kepegawaian" {{ old('sub_bagian', $pegawai->sub_bagian ?? '') === 'Sub Bagian Umum dan Kepegawaian' ? 'selected' : '' }}>Sub Bagian Umum dan Kepegawaian</option>
            <option value="Sub Bagian Tata Usaha" {{ old('sub_bagian', $pegawai->sub_bagian ?? '') === 'Sub Bagian Tata Usaha' ? 'selected' : '' }}>Sub Bagian Tata Usaha</option>
        </select>
        @error('sub_bagian')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-6">
        <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
        <select name="jabatan_id" class="form-select @error('jabatan_id') is-invalid @enderror" required>
            <option value="">-- Pilih Jabatan --</option>
            @foreach($jabatan as $j)
                <option value="{{ $j->id }}" {{ old('jabatan_id', $pegawai->jabatan_id ?? '') == $j->id ? 'selected' : '' }}>
                    {{ $j->nama_jabatan }}
                </option>
            @endforeach
        </select>
        @error('jabatan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-4">
        <label class="form-label fw-semibold">Jenis Pegawai <span class="text-danger">*</span></label>
        <select name="jenis_pegawai" class="form-select @error('jenis_pegawai') is-invalid @enderror" required>
            <option value="">-- Pilih --</option>
            @foreach(['PNS','PPPK'] as $j)
                <option value="{{ $j }}" {{ old('jenis_pegawai', $pegawai->jenis_pegawai ?? '') === $j ? 'selected' : '' }}>{{ $j }}</option>
            @endforeach
        </select>
        @error('jenis_pegawai')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-4">
        <label class="form-label fw-semibold">Pangkat/Golongan</label>
        <input type="text" name="pangkat" class="form-control" value="{{ old('pangkat', $pegawai->pangkat ?? '') }}" placeholder="cth: Penata Muda / III-a">
    </div>

    <div class="col-sm-4">
        <label class="form-label fw-semibold">Sisa Cuti Tahunan <span class="text-danger">*</span></label>
        <input type="number" name="sisa_cuti_tahunan" class="form-control @error('sisa_cuti_tahunan') is-invalid @enderror"
               value="{{ old('sisa_cuti_tahunan', $pegawai->sisa_cuti_tahunan ?? 12) }}" min="0" max="72" required>
        @error('sisa_cuti_tahunan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-4">
        <label class="form-label fw-semibold">Tanggal Masuk (TMT) <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_masuk" class="form-control @error('tanggal_masuk') is-invalid @enderror"
               value="{{ old('tanggal_masuk', isset($pegawai) ? $pegawai->tanggal_masuk->format('Y-m-d') : '') }}" required>
        @error('tanggal_masuk')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12"><hr class="my-1"><p class="text-muted small fw-semibold mb-0">Data Pribadi</p></div>

    <div class="col-sm-4">
        <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
        <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
            <option value="">-- Pilih --</option>
            <option value="L" {{ old('jenis_kelamin', $pegawai->jenis_kelamin ?? '') === 'L' ? 'selected' : '' }}>Laki-Laki</option>
            <option value="P" {{ old('jenis_kelamin', $pegawai->jenis_kelamin ?? '') === 'P' ? 'selected' : '' }}>Perempuan</option>
        </select>
        @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-4">
        <label class="form-label fw-semibold">Tempat Lahir <span class="text-danger">*</span></label>
        <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror"
               value="{{ old('tempat_lahir', $pegawai->tempat_lahir ?? '') }}" required>
        @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-4">
        <label class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror"
               value="{{ old('tanggal_lahir', isset($pegawai) ? $pegawai->tanggal_lahir->format('Y-m-d') : '') }}" required>
        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>


    <div class="col-sm-4">
        <label class="form-label fw-semibold">No. Telepon</label>
        <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon', $pegawai->no_telepon ?? '') }}" maxlength="15">
    </div>

    <div class="col-sm-8">
        <label class="form-label fw-semibold">Foto Pegawai</label>
        <input type="file" name="foto" class="form-control" accept="image/jpg,image/jpeg,image/png">
        <div class="form-text">Format JPG/PNG, maks. 2MB.</div>
        @if(isset($pegawai) && $pegawai->foto)
            <div class="mt-2">
                <img src="{{ asset('storage/'.$pegawai->foto) }}" style="height:80px;border-radius:8px;" alt="foto">
            </div>
        @endif
    </div>

    <div class="col-sm-4">
        <label class="form-label fw-semibold">Status Pegawai <span class="text-danger">*</span></label>
        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
            <option value="1" {{ old('is_active', isset($pegawai) ? ($pegawai->is_active ? '1' : '0') : '1') == '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ old('is_active', isset($pegawai) ? ($pegawai->is_active ? '1' : '0') : '1') == '0' ? 'selected' : '' }}>Non-Aktif</option>
        </select>
        @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>