<?php

namespace Tests\Feature;

use App\Models\Distribusi;
use App\Models\Kelompok;
use App\Models\Penerima;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PhaseTwoFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Tahap Dua',
            'email' => 'admin-phase2@example.test',
            'password' => Hash::make('test-password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);
    }

    private function kelompok(): Kelompok
    {
        return Kelompok::create([
            'nama' => 'Kelompok Uji Tahap Dua',
            'kode' => 'P2-' . uniqid(),
            'daerah' => 'Aceh Tamiang',
            'kecamatan' => 'Sekerak',
            'desa' => 'Juar',
        ]);
    }

    private function penerima(Kelompok $kelompok, string $nik, bool $menerima = false): Penerima
    {
        return Penerima::create([
            'nik' => $nik,
            'nama' => 'Penerima Uji Tahap Dua',
            'alamat' => 'Desa Juar',
            'kabupaten' => 'Aceh Tamiang',
            'kecamatan' => 'Sekerak',
            'desa' => 'Juar',
            'phone' => '081200000000',
            'sumber_data' => 'relawan',
            'status' => 'terverifikasi',
            'kelompok_id' => $kelompok->id,
            'terima_bantuan' => $menerima,
        ]);
    }

    private function payload(Kelompok $kelompok): array
    {
        return [
            'nama_kegiatan' => 'Distribusi Uji Tahap Dua',
            'tanggal' => '2026-07-30',
            'lokasi' => 'Juar, Sekerak',
            'titik_koordinat' => '4.250000,97.950000',
            'kelompok_id' => $kelompok->id,
            'jenis_bantuan' => 'Paket sembako',
            'jumlah_paket' => 10,
            'estimasi_nilai_total' => '',
            'sumber_dana' => '',
            'status' => 'direncanakan',
            'catatan' => '',
        ];
    }

    public function test_distribusi_normalizes_optional_numeric_values(): void
    {
        $admin = $this->admin();
        $kelompok = $this->kelompok();
        $penerima = $this->penerima($kelompok, '1101010101010001');

        $response = $this->actingAs($admin)->post('/distribusi', $this->payload($kelompok));

        $response->assertRedirect(route('distribusi.index'));
        $this->assertDatabaseHas('distribusis', [
            'nama_kegiatan' => 'Distribusi Uji Tahap Dua',
            'estimasi_nilai_total' => 0,
            'sumber_dana' => '',
        ]);
        $this->assertDatabaseHas('penerima_distribusi', [
            'penerima_id' => $penerima->id,
            'status' => 'terjadwal',
        ]);
    }

    public function test_peta_and_laporan_render_database_data(): void
    {
        $admin = $this->admin();
        $kelompok = $this->kelompok();
        $this->penerima($kelompok, '1101010101010002', true);
        Distribusi::create(array_merge($this->payload($kelompok), [
            'kode_distribusi' => 'DST-PHASE2',
            'created_by' => $admin->id,
            'estimasi_nilai_total' => 250000,
            'sumber_dana' => 'Donatur',
        ]));

        $this->actingAs($admin)->get('/peta')
            ->assertOk()
            ->assertSee('Distribusi Uji Tahap Dua');

        $this->actingAs($admin)->get('/laporan')
            ->assertOk()
            ->assertSee('Distribusi Uji Tahap Dua')
            ->assertSee('250.000');
    }

    public function test_laporan_csv_is_downloadable(): void
    {
        $admin = $this->admin();
        $kelompok = $this->kelompok();
        Distribusi::create(array_merge($this->payload($kelompok), [
            'kode_distribusi' => 'DST-CSV-PHASE2',
            'created_by' => $admin->id,
            'estimasi_nilai_total' => 100000,
            'sumber_dana' => 'Donatur',
        ]));

        $response = $this->actingAs($admin)->get('/laporan/export-csv');
        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('DST-CSV-PHASE2', $response->streamedContent());
    }

    public function test_api_wilayah_returns_cascading_master_data(): void
    {
        DB::table('wilayah')->insert([
            ['kode' => '11.16', 'nama' => 'Kabupaten Aceh Tamiang'],
            ['kode' => '11.16.07', 'nama' => 'Sekerak'],
            ['kode' => '11.16.07.2001', 'nama' => 'Juar'],
        ]);

        $this->get('/api/wilayah/kabupaten')->assertOk()->assertJsonFragment(['kode' => '11.16']);
        $this->get('/api/wilayah/kecamatan/11.16')->assertOk()->assertJsonFragment(['nama' => 'Sekerak']);
        $this->get('/api/wilayah/desa/11.16.07')->assertOk()->assertJsonFragment(['nama' => 'Juar']);
    }
}
