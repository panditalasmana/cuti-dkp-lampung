<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'nip',
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'      => 'hashed',
            'is_active'     => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // ─── Scopes ────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopePegawai($query)
    {
        return $query->where('role', 'pegawai');
    }

    // ─── Helpers ───────────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPegawai(): bool
    {
        return $this->role === 'pegawai';
    }

    // ─── Relationships ─────────────────────────────────────────────
    public function pegawai(): HasOne
    {
        return $this->hasOne(Pegawai::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function verifikasiPengajuan(): HasMany
    {
        return $this->hasMany(PengajuanCuti::class, 'diverifikasi_oleh');
    }

    public function uploadDokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'uploaded_by');
    }
}