<?php

namespace Tests\Feature;

use App\Models\Bidang;
use App\Models\Jabatan;
use App\Models\JenisCuti;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengajuanCutiTest extends TestCase
{
    use RefreshDatabase;

    public function test_pegawai_can_access_pengajuan_cuti_page(): void
    {
        $user = User::factory()->create([
            'nip'                  => '199111152025211022',
            'role'                 => 'pegawai',
            'password'             => bcrypt('1991'),
            'must_change_password' => false,
        ]);

        $bidang = Bidang::create(['nama_bidang' => 'Sekretariat', 'kode_bidang' => 'SEKR']);
        $jabatan = Jabatan::create(['nama_jabatan' => 'Penata Layanan Operasional', 'kode_jabatan' => 'PLO']);

        Pegawai::create([
            'user_id'            => $user->id,
            'nip'                => '199111152025211022',
            'nama_lengkap'       => 'Gerry Gahara, S.Kom.',
            'bidang_id'          => $bidang->id,
            'jabatan_id'         => $jabatan->id,
            'jenis_kelamin'      => 'L',
            'tempat_lahir'       => 'Bandar Lampung',
            'tanggal_lahir'      => '1991-11-15',
            'tanggal_masuk'      => '2025-01-01',
            'sisa_cuti_tahunan'  => 12,
        ]);

        JenisCuti::create([
            'kode_cuti'     => 'CT',
            'nama_cuti'     => 'Cuti Tahunan',
            'maks_hari'     => 12,
            'potong_kuota'  => true,
            'perlu_lampiran'=> false,
        ]);

        $response = $this->actingAs($user)->get('/pegawai/pengajuan/buat');
        $response->assertStatus(200);
    }
}
