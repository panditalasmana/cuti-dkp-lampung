<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->admin()->create([
            'nip'      => '198501012010011001',
            'password' => bcrypt('Admin@DKP2026'),
        ]);

        $response = $this->post('/login', [
            'nip'        => '198501012010011001',
            'password'   => 'Admin@DKP2026',
            'login_type' => 'admin',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_login_is_throttled_after_too_many_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'nip'      => '199111152025211022',
                'password' => 'wrong-password',
            ]);
        }

        // The 6th attempt should be blocked by throttle (429 Too Many Requests)
        $response = $this->post('/login', [
            'nip'      => '199111152025211022',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
    }

    public function test_pegawai_can_login_and_access_dashboard_directly(): void
    {
        $user = User::factory()->create([
            'nip'      => '199111152025211022',
            'role'     => 'pegawai',
            'password' => bcrypt('1991'),
        ]);

        $response = $this->post('/login', [
            'nip'        => '199111152025211022',
            'password'   => '1991',
            'login_type' => 'pegawai',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/pegawai/dashboard');
    }
}
