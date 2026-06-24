<?php

namespace App\Services;

use App\Repositories\ActivityLogRepository;

class ActivityLogService
{
    public function __construct(private ActivityLogRepository $repo) {}

    public function logLogin(string $nip): void
    {
        $this->repo->log('login', 'auth', "Login berhasil menggunakan NIP: {$nip}");
    }

    public function logLogout(): void
    {
        $this->repo->log('logout', 'auth', 'Logout dari sistem');
    }

    public function logCreate(string $module, string $desc, object $subject): void
    {
        $this->repo->log('create', $module, $desc, $subject);
    }

    public function logUpdate(string $module, string $desc, object $subject, array $old, array $new): void
    {
        $this->repo->log('update', $module, $desc, $subject, $old, $new);
    }

    public function logDelete(string $module, string $desc): void
    {
        $this->repo->log('delete', $module, $desc);
    }

    public function logUpload(string $module, string $desc, object $subject): void
    {
        $this->repo->log('upload', $module, $desc, $subject);
    }

    public function logStatus(string $module, string $desc, object $subject): void
    {
        $this->repo->log('status_change', $module, $desc, $subject);
    }

    public function paginate(int $perPage = 20, array $filters = [])
    {
        return $this->repo->paginate($perPage, $filters);
    }
}