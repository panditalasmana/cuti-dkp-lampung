<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogRepository
{
    public function __construct(private ActivityLog $model) {}

    public function log(
    string $action,
    string $module,
    string $description,
    ?object $subject = null,
    ?array $oldValues = null,
    ?array $newValues = null
): ?ActivityLog
{
    // User belum login
    if (!Auth::check()) {
        return null;
    }

    return $this->model->create([
        'user_id'      => Auth::id(),
        'action'       => $action,
        'module'       => $module,
        'description'  => $description,
        'subject_id'   => $subject?->id,
        'subject_type' => $subject ? get_class($subject) : null,
        'ip_address'   => Request::ip(),
        'user_agent'   => Request::userAgent(),
        'old_values'   => $oldValues,
        'new_values'   => $newValues,
    ]);
}

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with('user')->latest('created_at');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['module'])) {
            $query->where('module', $filters['module']);
        }

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['tanggal_dari'])) {
            $query->whereDate('created_at', '>=', $filters['tanggal_dari']);
        }

        if (!empty($filters['tanggal_sampai'])) {
            $query->whereDate('created_at', '<=', $filters['tanggal_sampai']);
        }

        return $query->paginate($perPage)->withQueryString();
    }
}