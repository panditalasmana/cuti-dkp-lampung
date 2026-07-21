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
        if (str_starts_with($this->path_file, 'pengajuan/')) {
            return asset('storage/' . $this->path_file);
        }

        $disk = config('filesystems.upload_disk', 'public');
        if ($disk === 'google') {
            try {
                return \Illuminate\Support\Facades\Storage::disk('google')->url($this->path_file);
            } catch (\Exception $e) {
                return 'https://docs.google.com/uc?export=download&id=' . $this->path_file;
            }
        }
        
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