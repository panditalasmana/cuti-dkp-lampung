<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pegawai';

    protected $fillable = [
        'user_id',
        'bidang_id',
        'jabatan_id',
        'nip',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'email',
        'tanggal_masuk',
        'jenis_pegawai',
        'pangkat',
        'foto',
        'sisa_cuti_tahunan',
        'is_active',
    ];

    protected $casts = [
    'tanggal_lahir'     => 'date',
    'tanggal_masuk'     => 'date',
    'is_active'         => 'boolean',
    'sisa_cuti_tahunan' => 'integer',
];
    // ─── Accessors ─────────────────────────────────────────────────
    public function getUmurAttribute(): int
    {
         if (empty($this->tanggal_lahir)) {
        return 0;
    }

    return \Carbon\Carbon::parse($this->tanggal_lahir)->diffInYears(now());
    }

    public function getMasaKerjaAttribute(): string
    {
        if (empty($this->tanggal_masuk)) {
        return '-';
    }

    $tanggalMasuk = \Carbon\Carbon::parse($this->tanggal_masuk);

    $diff = $tanggalMasuk->diff(now());

    return $diff->y . ' Tahun ' . $diff->m . ' Bulan';
}
    

    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan';
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return asset('images/default-avatar.png');
    }

    // ─── Scopes ────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Relationships ─────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function pengajuanCuti(): HasMany
    {
        return $this->hasMany(PengajuanCuti::class);
    }

    public function pengajuanAktif(): HasMany
    {
        return $this->hasMany(PengajuanCuti::class)->whereIn('status', ['menunggu', 'disetujui']);
    }
}