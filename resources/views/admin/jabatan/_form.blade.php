<div class="row g-3">
    <div class="col-sm-4">
        <label class="form-label fw-semibold">Kode Jabatan <span class="text-danger">*</span></label>
        <input type="text" name="kode_jabatan"
               class="form-control @error('kode_jabatan') is-invalid @enderror"
               value="{{ old('kode_jabatan', $jabatan->kode_jabatan ?? '') }}"
               placeholder="Contoh: KASIE" maxlength="30"
               style="text-transform:uppercase" required>
        @error('kode_jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Kode unik jabatan, maks. 30 karakter.</div>
    </div>

    <div class="col-sm-8">
        <label class="form-label fw-semibold">Nama Jabatan <span class="text-danger">*</span></label>
        <input type="text" name="nama_jabatan"
               class="form-control @error('nama_jabatan') is-invalid @enderror"
               value="{{ old('nama_jabatan', $jabatan->nama_jabatan ?? '') }}"
               placeholder="Nama lengkap jabatan" maxlength="200" required>
        @error('nama_jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-4">
        <label class="form-label fw-semibold">Golongan</label>
        <select name="golongan" class="form-select @error('golongan') is-invalid @enderror">
            <option value="">-- Pilih Golongan --</option>
            @foreach(['I', 'II', 'III', 'IV'] as $g)
                <option value="{{ $g }}"
                    {{ old('golongan', $jabatan->golongan ?? '') === $g ? 'selected' : '' }}>
                    Golongan {{ $g }}
                </option>
            @endforeach
        </select>
        @error('golongan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-4">
        <label class="form-label fw-semibold">Eselon</label>
        <input type="text" name="eselon"
               class="form-control @error('eselon') is-invalid @enderror"
               value="{{ old('eselon', $jabatan->eselon ?? '') }}"
               placeholder="Contoh: II-b, IV-a" maxlength="10">
        @error('eselon')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Kosongkan untuk jabatan fungsional.</div>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Keterangan</label>
        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                  rows="3" placeholder="Deskripsi jabatan..." maxlength="500">{{ old('keterangan', $jabatan->keterangan ?? '') }}</textarea>
        @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @isset($jabatan)
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active"
                       id="isActive" value="1"
                       {{ old('is_active', $jabatan->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="isActive">Jabatan Aktif</label>
            </div>
        </div>
    @endisset
</div>