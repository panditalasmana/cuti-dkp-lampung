<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';

    protected $fillable = [
        'pengajuan_cuti_id',
        'uploaded_by',
        'jenis_dokumen',
        'nama_file',
        'path_file',
        'mime_type',
        'ukuran_file',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'ukuran_file' => 'integer',
        ];
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->path_file);
    }

    public function getUkuranFormatAttribute(): string
    {
        $bytes = $this->ukuran_file;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        return number_format($bytes / 1024, 2) . ' KB';
    }

    public function isImage(): bool
    {
        return in_array($this->mime_type, ['image/jpeg', 'image/png', 'image/jpg']);
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function pengajuanCuti(): BelongsTo
    {
        return $this->belongsTo(PengajuanCuti::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}