<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifyCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_verify_route_shows_member_info_for_valid_code(): void
    {
        User::create([
            'name' => 'Ahmad Uji',
            'email' => 'verify@test.ypkm.info',
            'password' => bcrypt('secret123'),
            'role' => 'pengurus',
            'kode_keanggotaan' => 'YPKM-PGR-26-999',
            'is_active' => true,
        ]);

        $response = $this->get('/verify/YPKM-PGR-26-999');

        $response->assertStatus(200);
        $response->assertSee('Ahmad Uji');
        $response->assertSee('YPKM-PGR-26-999');
        $response->assertSee('AKTIF');
    }

    public function test_verify_route_returns_404_for_unknown_code(): void
    {
        $this->get('/verify/YPKM-PGR-26-000')->assertNotFound();
    }

    public function test_verify_route_works_without_login(): void
    {
        User::create([
            'name' => 'Relawan Uji',
            'email' => 'relawan@test.ypkm.info',
            'password' => bcrypt('secret123'),
            'role' => 'relawan',
            'kode_keanggotaan' => 'YPKM-RLW-26-0001',
            'is_active' => true,
        ]);

        $this->get('/verify/YPKM-RLW-26-0001')
            ->assertStatus(200)
            ->assertSee('YPKM-RLW-26-0001');
    }
}
