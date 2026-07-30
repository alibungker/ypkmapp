<?php

namespace Tests\Feature;

use App\Models\Kelompok;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PhaseOneAccessTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, ?Kelompok $kelompok = null, array $wilayah = []): User
    {
        return User::create([
            'name' => 'Test ' . $role . ' ' . uniqid(),
            'email' => uniqid($role . '_') . '@example.test',
            'password' => Hash::make('test-password'),
            'role' => $role,
            'kelompok_id' => $kelompok?->id,
            'wilayah_kabupaten' => $wilayah['kabupaten'] ?? null,
            'wilayah_kecamatan' => $wilayah['kecamatan'] ?? null,
            'wilayah_desa' => $wilayah['desa'] ?? null,
            'is_active' => true,
        ]);
    }

    private function kelompok(string $suffix, string $daerah = 'Aceh Tamiang'): Kelompok
    {
        return Kelompok::create([
            'nama' => 'Kelompok Test ' . $suffix,
            'kode' => 'TEST-' . $suffix . '-' . uniqid(),
            'daerah' => $daerah,
            'kecamatan' => 'Sekerak',
            'desa' => 'Juar',
            'jumlah_anggota' => 0,
        ]);
    }

    public function test_ketua_tidak_dapat_mengakses_modul_relawan_atau_mutasi_admin(): void
    {
        $kelompok = $this->kelompok('A');
        $ketua = $this->user('ketua_kelompok', $kelompok);

        $this->actingAs($ketua)->get('/relawan')->assertForbidden();
        $this->actingAs($ketua)->post('/kelompok', [])->assertForbidden();
        $this->actingAs($ketua)->get('/distribusi/create')->assertForbidden();
    }

    public function test_relawan_dapat_mengakses_modul_operasional_tetapi_bukan_admin(): void
    {
        $relawan = $this->user('relawan', null, [
            'kabupaten' => 'Aceh Tamiang',
            'kecamatan' => 'Sekerak',
            'desa' => 'Juar',
        ]);

        $this->actingAs($relawan)->get('/relawan')->assertOk();
        $this->actingAs($relawan)->get('/users')->assertForbidden();
        $this->actingAs($relawan)->get('/keuangan')->assertForbidden();
    }

    public function test_ketua_hanya_dapat_melihat_kelompok_sendiri(): void
    {
        $milikSendiri = $this->kelompok('OWN');
        $kelompokLain = $this->kelompok('OTHER', 'Aceh Utara');
        $ketua = $this->user('ketua_kelompok', $milikSendiri);

        $this->actingAs($ketua)->get(route('kelompok.show', $milikSendiri))->assertOk();
        $this->actingAs($ketua)->get(route('kelompok.show', $kelompokLain))->assertForbidden();
        $this->actingAs($ketua)->get(route('kelompok.anggota', $kelompokLain))->assertForbidden();
    }

    public function test_admin_dapat_mengakses_modul_pengelolaan(): void
    {
        $admin = $this->user('admin');

        $this->actingAs($admin)->get('/users')->assertOk();
        $this->actingAs($admin)->get('/distribusi/create')->assertOk();
        $this->actingAs($admin)->get('/keuangan')->assertOk();
    }
}
