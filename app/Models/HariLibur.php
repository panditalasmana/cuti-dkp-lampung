<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    use HasFactory;

    protected $table = 'hari_libur';

    protected $fillable = [
        'tanggal',
        'keterangan',
        'is_cuti_bersama',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_cuti_bersama' => 'boolean',
    ];
}
