<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penandatangan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penandatangan';

    protected $fillable = [
        'kategori',
        'nama',
        'nip',
        'jabatan',
        'pangkat_golongan',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    // ─── Scopes ────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // ─── Accessors ─────────────────────────────────────────────────
    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            'pejabat_wenang'               => 'Pejabat Berwenang',
            'atasan_langsung'              => 'Atasan Langsung',
            'eselon_4', 'pejabat_pengawas' => 'Pejabat Pengawas',
            default                        => ucfirst($this->kategori),
        };
    }

    public function getPipedValueAttribute(): string
    {
        return "{$this->nama}|{$this->nip}|{$this->jabatan}";
    }
}
