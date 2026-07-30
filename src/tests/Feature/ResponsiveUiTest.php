<?php

namespace Tests\Feature;

use App\Models\Kelompok;
use App\Models\Penerima;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResponsiveUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_layout_exposes_accessible_mobile_navigation(): void
    {
        $response = $this->actingAs($this->admin())->get('/');

        $response->assertOk()
            ->assertSee('id="mobile-menu-button"', false)
            ->assertSee('aria-controls="app-sidebar"', false)
            ->assertSee('id="sidebar-overlay"', false)
            ->assertSee('@media (max-width: 1023px)', false)
            ->assertDontSee('@@media', false)
            ->assertSee('id="app-sidebar" class="sidebar" aria-label="Navigasi utama" aria-hidden="true" inert', false)
            ->assertSee('if (sidebar) sidebar.inert = !drawerOpen;', false)
            ->assertSee("if (mainArea) mainArea.inert = drawerOpen;", false)
            ->assertSee("if (event.key !== 'Tab') return;", false)
            ->assertSee('class="skip-link"', false);
    }

    public function test_penerima_index_masks_nik_in_list_view(): void
    {
        $kelompok = Kelompok::create([
            'nama' => 'Kelompok UI',
            'kode' => 'UI-001',
            'daerah' => 'Aceh Tamiang',
            'kecamatan' => 'Sekerak',
            'desa' => 'Juar',
        ]);

        Penerima::create([
            'nik' => '1116127112800001',
            'nama' => 'Penerima Uji',
            'alamat' => 'Alamat uji',
            'phone' => '08123456789',
            'kabupaten' => 'Aceh Tamiang',
            'kecamatan' => 'Sekerak',
            'desa' => 'Juar',
            'status' => 'terverifikasi',
            'sumber_data' => 'mandiri',
            'kelompok_id' => $kelompok->id,
        ]);
        Penerima::create([
            'nik' => '12345678',
            'nama' => 'Data Malformed',
            'alamat' => 'Alamat uji',
            'phone' => '08123456780',
            'kabupaten' => 'Aceh Tamiang',
            'kecamatan' => 'Sekerak',
            'desa' => 'Juar',
            'status' => 'pending',
            'sumber_data' => 'mandiri',
            'kelompok_id' => $kelompok->id,
        ]);

        $response = $this->actingAs($this->admin())->get('/penerima');

        $response->assertOk()
            ->assertSee('1116••••••••0001', false)
            ->assertSee('12••••78', false)
            ->assertDontSee('1116127112800001')
            ->assertDontSee('12345678');

        User::create([
            'nik' => '87654321',
            'name' => 'Pengguna Malformed',
            'email' => 'malformed@example.test',
            'password' => Hash::make('password'),
            'role' => 'relawan',
            'status' => 'aktif',
        ]);

        $this->get('/users')
            ->assertOk()
            ->assertSee('87••••21', false)
            ->assertDontSee('87654321');

        $this->get('/relawan')
            ->assertOk()
            ->assertSee('12••••78', false)
            ->assertDontSee('12345678');

        $this->get('/kelompok/' . $kelompok->id)
            ->assertOk()
            ->assertSee('12••••78', false)
            ->assertDontSee('>12345678<', false);
    }

    public function test_public_registration_requires_privacy_consent(): void
    {
        $this->get('/daftar')
            ->assertOk()
            ->assertSee('name="privacy_consent"', false)
            ->assertSee('Kebijakan Privasi');

        $payload = [
            'nik' => '1116127112800099',
            'nama' => 'Pendaftar Uji',
            'alamat' => 'Alamat uji',
            'phone' => '08123456789',
            'jumlah_keluarga' => 2,
        ];

        $response = $this->from('/daftar')->post('/daftar', $payload);
        $response->assertRedirect('/daftar')->assertSessionHasErrors('privacy_consent');
        $this->assertDatabaseMissing('penerimas', ['nik' => $payload['nik']]);

        $invalidNik = $this->from('/daftar')->post('/daftar', array_merge($payload, [
            'nik' => '12345678',
            'privacy_consent' => '1',
        ]));
        $invalidNik->assertRedirect('/daftar')->assertSessionHasErrors('nik');
        $this->assertDatabaseMissing('penerimas', ['nik' => '12345678']);

        $accepted = $this->post(route('penerima.daftar'), array_merge($payload, ['privacy_consent' => '1']));
        $accepted->assertRedirect(route('penerima.daftar.form'));
        $accepted->assertSessionHas('success');
        $this->assertDatabaseHas('penerimas', ['nik' => $payload['nik']]);
    }

    public function test_public_registration_must_be_assigned_to_group_before_verification(): void
    {
        $penerima = Penerima::create([
            'nik' => '1116127112800077',
            'nama' => 'Pendaftar Tanpa Kelompok',
            'alamat' => 'Alamat uji',
            'phone' => '08123456777',
            'kabupaten' => 'Aceh Tamiang',
            'kecamatan' => 'Sekerak',
            'desa' => 'Juar',
            'status' => 'pending',
            'sumber_data' => 'mandiri',
            'kelompok_id' => null,
        ]);

        $blocked = $this->actingAs($this->admin())
            ->from('/penerima')
            ->post(route('penerima.verify', $penerima), ['status' => 'terverifikasi']);

        $blocked->assertRedirect('/penerima')
            ->assertSessionHas('error', 'Tetapkan kelompok penerima sebelum melakukan verifikasi.');
        $this->assertSame('pending', $penerima->fresh()->status);
        $this->assertNull($penerima->fresh()->verified_at);

        $kelompok = Kelompok::create([
            'nama' => 'Kelompok Verifikasi',
            'kode' => 'UI-VERIFY',
            'daerah' => 'Aceh Tamiang',
            'kecamatan' => 'Sekerak',
            'desa' => 'Juar',
        ]);
        $penerima->update(['kelompok_id' => $kelompok->id]);

        $this->post(route('penerima.verify', $penerima), ['status' => 'terverifikasi'])
            ->assertSessionHas('success');
        $this->assertSame('terverifikasi', $penerima->fresh()->status);
        $this->assertNotNull($penerima->fresh()->verified_at);
    }

    public function test_kelompok_nullable_migration_can_rollback_when_no_null_rows_exist(): void
    {
        $migration = require database_path('migrations/2026_07_30_120000_make_penerima_kelompok_nullable.php');

        $migration->down();
        $column = collect(\Illuminate\Support\Facades\Schema::getColumns('penerimas'))
            ->firstWhere('name', 'kelompok_id');
        $this->assertFalse($column['nullable']);

        $migration->up();
        $column = collect(\Illuminate\Support\Facades\Schema::getColumns('penerimas'))
            ->firstWhere('name', 'kelompok_id');
        $this->assertTrue($column['nullable']);
    }

    public function test_kelompok_nullable_migration_refuses_unsafe_rollback(): void
    {
        Penerima::create([
            'nik' => '1116127112800088',
            'nama' => 'Belum Berkelompok',
            'alamat' => 'Alamat uji',
            'phone' => '08123456788',
            'kabupaten' => 'Aceh Tamiang',
            'kecamatan' => 'Sekerak',
            'desa' => 'Juar',
            'status' => 'pending',
            'sumber_data' => 'mandiri',
            'kelompok_id' => null,
        ]);

        $migration = require database_path('migrations/2026_07_30_120000_make_penerima_kelompok_nullable.php');

        $this->expectException(\RuntimeException::class);
        $migration->down();
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin UI',
            'email' => 'admin-ui@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);
    }
}
