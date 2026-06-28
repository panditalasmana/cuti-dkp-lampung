<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengajuanCuti extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengajuan_cuti';

    protected $fillable = [
        'pegawai_id',
        'jenis_cuti_id',
        'nomor_surat',
        'tanggal_mulai',
        'tanggal_selesai',
        'lama_cuti',
        'alasan_cuti',
        'alamat_selama_cuti',
        'no_telp_selama_cuti',
        'status',
        'catatan_admin',
        'tanggal_pengajuan',
        'tanggal_verifikasi',
        'diverifikasi_oleh',
        'pdf_surat',
        'atasan_nama',
        'atasan_nip',
        'atasan_jabatan',
        'pejabat_nama',
        'pejabat_nip',
        'pejabat_jabatan',
    ];

    protected $casts = [
    'tanggal_mulai'      => 'date',
    'tanggal_selesai'    => 'date',
    'tanggal_pengajuan'  => 'datetime',
    'tanggal_verifikasi' => 'datetime',
    'lama_cuti'          => 'integer',
];

    
    // ─── Status Constants ──────────────────────────────────────────
    const STATUS_MENUNGGU  = 'menunggu';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK   = 'ditolak';

    // ─── Accessors ─────────────────────────────────────────────────
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU  => 'Menunggu Verifikasi',
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_DITOLAK   => 'Ditolak',
            default                => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU  => 'badge-warning',
            self::STATUS_DISETUJUI => 'badge-success',
            self::STATUS_DITOLAK   => 'badge-danger',
            default                => 'badge-secondary',
        };
    }

    public function getPdfUrlAttribute(): ?string
    {
        if ($this->pdf_surat) {
            return asset('storage/' . $this->pdf_surat);
        }
        return null;
    }

    // ─── Scopes ────────────────────────────────────────────────────
    public function scopeMenunggu($query)
    {
        return $query->where('status', self::STATUS_MENUNGGU);
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', self::STATUS_DISETUJUI);
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', self::STATUS_DITOLAK);
    }

    public function scopeTahunIni($query)
    {
        return $query->whereYear('tanggal_pengajuan', now()->year);
    }

    // ─── Relationships ─────────────────────────────────────────────
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jenisCuti(): BelongsTo
    {
        return $this->belongsTo(JenisCuti::class);
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class);
    }

    public function scanSurat(): HasOne
    {
        return $this->hasOne(Dokumen::class)->where('jenis_dokumen', 'scan_surat_ditandatangani')
                    ->latest();
    }
}