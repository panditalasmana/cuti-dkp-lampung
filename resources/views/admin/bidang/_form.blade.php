<div class="row g-3">
    <div class="col-sm-4">
        <label class="form-label fw-semibold">Kode Bidang <span class="text-danger">*</span></label>
        <input type="text" name="kode_bidang" class="form-control @error('kode_bidang') is-invalid @enderror"
               value="{{ old('kode_bidang', $bidang->kode_bidang ?? '') }}"
               placeholder="Contoh: PTBP" maxlength="10" style="text-transform:uppercase" required>
        @error('kode_bidang')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Kode unik bidang, maks. 10 karakter.</div>
    </div>

    <div class="col-sm-8">
        <label class="form-label fw-semibold">Nama Bidang <span class="text-danger">*</span></label>
        <input type="text" name="nama_bidang" class="form-control @error('nama_bidang') is-invalid @enderror"
               value="{{ old('nama_bidang', $bidang->nama_bidang ?? '') }}"
               placeholder="Nama lengkap bidang" maxlength="200" required>
        @error('nama_bidang')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-8">
        <label class="form-label fw-semibold">Nama Kepala Bidang</label>
        <input type="text" name="kepala_bidang" class="form-control @error('kepala_bidang') is-invalid @enderror"
               value="{{ old('kepala_bidang', $bidang->kepala_bidang ?? '') }}"
               placeholder="Nama dan gelar kepala bidang" maxlength="200">
        <div class="form-text">Digunakan pada formulir surat cuti ASN.</div>
        @error('kepala_bidang')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-4">
        <label class="form-label fw-semibold">NIP Kepala Bidang</label>
        <input type="text" name="nip_kepala_bidang" class="form-control @error('nip_kepala_bidang') is-invalid @enderror"
               value="{{ old('nip_kepala_bidang', $bidang->nip_kepala_bidang ?? '') }}"
               placeholder="18 digit NIP" maxlength="18" inputmode="numeric">
        @error('nip_kepala_bidang')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Keterangan</label>
        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                  rows="3" placeholder="Deskripsi singkat bidang..." maxlength="500">{{ old('keterangan', $bidang->keterangan ?? '') }}</textarea>
        @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @isset($bidang)
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                       {{ old('is_active', $bidang->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="isActive">Bidang Aktif</label>
            </div>
        </div>
    @endisset
</div>