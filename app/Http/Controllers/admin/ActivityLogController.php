<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __construct(private ActivityLogService $service) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['user_id', 'module', 'action', 'tanggal_dari', 'tanggal_sampai']);
        $logs    = $this->service->paginate(20, $filters);
        $users   = User::orderBy('name')->get();
        $modules = ['auth', 'pegawai', 'bidang', 'jabatan', 'jenis_cuti', 'pengajuan', 'dokumen'];

        return view('admin.activity-log.index', compact('logs', 'users', 'modules', 'filters'));
    }
}