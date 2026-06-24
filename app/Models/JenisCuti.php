<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisCuti extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jenis_cuti';

    protected $fillable = [
        'kode_cuti',
        'nama_cuti',
        'maks_hari',
        'potong_kuota',
        'perlu_lampiran',
        'keterangan',
        'dasar_hukum',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'maks_hari'      => 'integer',
            'potong_kuota'   => 'boolean',
            'perlu_lampiran' => 'boolean',
            'is_active'      => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function pengajuanCuti(): HasMany
    {
        return $this->hasMany(PengajuanCuti::class);
    }
}