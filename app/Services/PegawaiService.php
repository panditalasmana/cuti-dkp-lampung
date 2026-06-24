<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\User;
use App\Repositories\PegawaiRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PegawaiService
{
    public function __construct(
        private PegawaiRepository $repo,
        private ActivityLogService $logService,
    ) {}

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->repo->paginate($perPage, $filters);
    }

    public function findById(int $id): Pegawai
    {
        return $this->repo->findById($id);
    }

    public function findByUserId(int $userId): ?Pegawai
    {
        return $this->repo->findByUserId($userId);
    }

    public function create(array $data): Pegawai
    {
        return DB::transaction(function () use ($data) {
            // Cek NIP unik
            if ($this->repo->findByNip($data['nip'])) {
                throw ValidationException::withMessages(['nip' => 'NIP sudah terdaftar dalam sistem.']);
            }

            // Buat User terlebih dahulu
            $user = User::create([
                'nip'      => $data['nip'],
                'name'     => $data['nama_lengkap'],
                'email'    => $data['email'] ?? null,
                'password' => Hash::make($data['password'] ?? 'Pegawai@123'),
                'role'     => 'pegawai',
            ]);

            // Handle foto upload
            if (isset($data['foto']) && $data['foto']->isValid()) {
                $path          = $data['foto']->store('pegawai/foto', 'public');
                $data['foto']  = $path;
            } else {
                unset($data['foto']);
            }

            // Hapus field password dari data pegawai
            unset($data['password']);

            $pegawai = $this->repo->create(array_merge($data, ['user_id' => $user->id]));
            $this->logService->logCreate('pegawai', "Menambah pegawai: {$pegawai->nama_lengkap} (NIP: {$pegawai->nip})", $pegawai);

            return $pegawai;
        });
    }

    public function update(Pegawai $pegawai, array $data): Pegawai
    {
        return DB::transaction(function () use ($pegawai, $data) {
            $old = $pegawai->toArray();

            // Handle foto
            if (isset($data['foto']) && $data['foto']->isValid()) {
                // Hapus foto lama
                if ($pegawai->foto) {
                    Storage::disk('public')->delete($pegawai->foto);
                }
                $data['foto'] = $data['foto']->store('pegawai/foto', 'public');
            } else {
                unset($data['foto']);
            }

            // Update user terkait
            $pegawai->user->update([
                'name'  => $data['nama_lengkap'],
                'email' => $data['email'] ?? null,
            ]);

            // Update password jika diisi
            if (!empty($data['password'])) {
                $pegawai->user->update(['password' => Hash::make($data['password'])]);
            }
            unset($data['password']);

            $pegawai = $this->repo->update($pegawai, $data);
            $this->logService->logUpdate('pegawai', "Mengubah data pegawai: {$pegawai->nama_lengkap}", $pegawai, $old, $pegawai->toArray());

            return $pegawai;
        });
    }

    public function delete(Pegawai $pegawai): void
    {
        if ($pegawai->pengajuanCuti()->whereIn('status', ['menunggu'])->exists()) {
            throw ValidationException::withMessages(['pegawai' => 'Pegawai memiliki pengajuan cuti yang sedang diproses.']);
        }

        DB::transaction(function () use ($pegawai) {
            $nama = $pegawai->nama_lengkap;

            if ($pegawai->foto) {
                Storage::disk('public')->delete($pegawai->foto);
            }

            $this->repo->delete($pegawai);
            $pegawai->user->delete();

            $this->logService->logDelete('pegawai', "Menghapus pegawai: {$nama}");
        });
    }

    public function updateProfil(Pegawai $pegawai, array $data): Pegawai
    {
        return DB::transaction(function () use ($pegawai, $data) {
            // Hanya boleh update field tertentu untuk pegawai sendiri
            $allowed = ['alamat', 'no_telepon', 'email', 'foto'];
            $filtered = array_intersect_key($data, array_flip($allowed));

            if (isset($filtered['foto']) && $filtered['foto']->isValid()) {
                if ($pegawai->foto) {
                    Storage::disk('public')->delete($pegawai->foto);
                }
                $filtered['foto'] = $filtered['foto']->store('pegawai/foto', 'public');
            } else {
                unset($filtered['foto']);
            }

            if (!empty($data['email'])) {
                $pegawai->user->update(['email' => $data['email']]);
            }

            return $this->repo->update($pegawai, $filtered);
        });
    }

    public function gantiPassword(User $user, string $newPassword): void
    {
        $user->update(['password' => Hash::make($newPassword)]);
        $this->logService->logUpdate('auth', 'Ganti password', $user, [], []);
    }

    public function countAll(): int
    {
        return $this->repo->countAll();
    }
}