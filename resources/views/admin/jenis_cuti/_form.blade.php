<div class="row g-3">
    <div class="col-sm-4">
        <label class="form-label fw-semibold">Kode Cuti <span class="text-danger">*</span></label>
        <input type="text" name="kode_cuti"
               class="form-control @error('kode_cuti') is-invalid @enderror"
               value="{{ old('kode_cuti', $jenisCuti->kode_cuti ?? '') }}"
               placeholder="Contoh: CT, CS, CM" maxlength="30"
               style="text-transform:uppercase" required>
        @error('kode_cuti')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-8">
        <label class="form-label fw-semibold">Nama Jenis Cuti <span class="text-danger">*</span></label>
        <input type="text" name="nama_cuti"
               class="form-control @error('nama_cuti') is-invalid @enderror"
               value="{{ old('nama_cuti', $jenisCuti->nama_cuti ?? '') }}"
               placeholder="Nama lengkap jenis cuti" maxlength="200" required>
        @error('nama_cuti')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-6 d-flex align-items-center pt-3">
        <div class="form-check form-switch mt-3">
            <input class="form-check-input" type="checkbox" id="potongKuota" checked disabled>
            <input type="hidden" name="potong_kuota" value="1">
            <label class="form-check-label fw-semibold" for="potongKuota">
                Memotong Kuota Cuti Tahunan
            </label>
            <div class="form-text">Mengurangi jatah cuti tahunan (Wajib Ya).</div>
        </div>
    </div>

    <div class="col-sm-6 d-flex align-items-center pt-3">
        <div class="form-check form-switch mt-3">
            <input class="form-check-input" type="checkbox" id="perluLampiran" checked disabled>
            <input type="hidden" name="perlu_lampiran" value="1">
            <label class="form-check-label fw-semibold" for="perluLampiran">
                Wajib Upload Lampiran
            </label>
            <div class="form-text">Lampiran dokumen pendukung wajib dilampirkan (Wajib Ya).</div>
        </div>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Dasar Hukum</label>
        <input type="text" name="dasar_hukum"
               class="form-control @error('dasar_hukum') is-invalid @enderror"
               value="{{ old('dasar_hukum', $jenisCuti->dasar_hukum ?? '') }}"
               placeholder="Contoh: Pasal 7 PP No. 11 Tahun 2017" maxlength="500">
        @error('dasar_hukum')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Keterangan</label>
        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                  rows="3" placeholder="Penjelasan singkat tentang jenis cuti ini..."
                  maxlength="500">{{ old('keterangan', $jenisCuti->keterangan ?? '') }}</textarea>
        @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @isset($jenisCuti)
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active"
                       id="isActive" value="1"
                       {{ old('is_active', $jenisCuti->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="isActive">Jenis Cuti Aktif</label>
            </div>
        </div>
    @endisset
</div>