<?php

namespace Tests\Feature;

use App\Models\Distribusi;
use App\Models\Kelompok;
use App\Models\Penerima;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
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

    public function test_keuangan_index_uses_modal_forms_for_dana_and_biaya(): void
    {
        $response = $this->actingAs($this->admin())->get(route('keuangan.index'));

        $response->assertOk();
        $response->assertSee('id="createDanaModal"', false);
        $response->assertSee('id="createBiayaModal"', false);
        $response->assertSee('data-open-finance-modal="createDanaModal"', false);
        $response->assertSee('data-open-finance-modal="createBiayaModal"', false);
    }

    public function test_invalid_biaya_reopens_originating_modal_with_input(): void
    {
        $response = $this->actingAs($this->admin())->from(route('keuangan.index'))->post(route('keuangan.biaya.store'), [
            'form_type' => 'biaya',
            'kategori' => 'transportasi',
            'deskripsi' => '',
            'jumlah' => 500000,
            'tanggal' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('keuangan.index'));
        $response->assertSessionHasErrors('deskripsi');
        $response->assertSessionHasInput('form_type', 'biaya');
    }

    public function test_barang_index_uses_create_modals_for_kegiatan_and_pembelian(): void
    {
        $response = $this->actingAs($this->admin())->get(route('barang.index'));

        $response->assertOk();
        $response->assertSee('id="createKegiatanModal"', false);
        $response->assertSee('id="createPembelianModal"', false);
        $response->assertSee('data-open-modal="createKegiatanModal"', false);
        $response->assertSee('data-open-modal="createPembelianModal"', false);
        $response->assertSee('Tambah Kegiatan');
        $response->assertSee('Pembelian Barang');
    }

    public function test_barang_create_modal_preserves_invalid_kegiatan_input_and_reopens(): void
    {
        $response = $this->actingAs($this->admin())->from(route('barang.index'))->post(route('barang.kegiatan.store'), [
            'nama_anggaran' => '',
            'kategori' => 'barang_bantuan',
            'target_paket' => 100,
            'satuan' => 'paket',
            'anggaran' => 1000000,
            'realisasi' => 0,
            'catatan' => 'Rencana',
            'form_type' => 'kegiatan',
        ]);

        $response->assertRedirect(route('barang.index'));
        $response->assertSessionHasErrors('nama_anggaran');
        $response->assertSessionHasInput('form_type', 'kegiatan');
    }

    public function test_kegiatan_can_allocate_multiple_items_without_changing_purchase_gap(): void
    {
        $beras = \App\Models\PembelianBarang::create([
            'nama_barang' => 'Beras Integrasi', 'batch' => 'B1',
            'qty_rencana' => 100, 'qty_terbeli' => 80, 'qty_belum' => 20,
            'harga_satuan' => 100000, 'anggaran' => 10000000,
            'realisasi' => 8000000, 'sisa' => 2000000, 'persen_real' => 80,
        ]);
        $minyak = \App\Models\PembelianBarang::create([
            'nama_barang' => 'Minyak Integrasi', 'batch' => 'M1',
            'qty_rencana' => 60, 'qty_terbeli' => 50, 'qty_belum' => 10,
            'harga_satuan' => 20000, 'anggaran' => 1200000,
            'realisasi' => 1000000, 'sisa' => 200000, 'persen_real' => 83.3,
        ]);

        $response = $this->actingAs($this->admin())->post(route('barang.kegiatan.store'), [
            'nama_anggaran' => 'Distribusi Multi Barang', 'kategori' => 'barang_bantuan',
            'target_paket' => 30, 'satuan' => 'paket', 'anggaran' => 0,
            'realisasi' => 0, 'catatan' => 'Integrasi stok', 'form_type' => 'kegiatan',
            'barang' => [
                ['pembelian_barang_id' => $beras->id, 'jumlah' => 30],
                ['pembelian_barang_id' => $minyak->id, 'jumlah' => 25],
            ],
        ]);

        $response->assertRedirect(route('barang.index', ['tab' => 'kegiatan']));
        $kegiatanId = \Illuminate\Support\Facades\DB::table('anggarans')->where('nama_anggaran', 'Distribusi Multi Barang')->value('id');
        $this->assertDatabaseHas('kegiatan_barang', ['anggaran_id' => $kegiatanId, 'pembelian_barang_id' => $beras->id, 'jumlah' => 30]);
        $this->assertDatabaseHas('kegiatan_barang', ['anggaran_id' => $kegiatanId, 'pembelian_barang_id' => $minyak->id, 'jumlah' => 25]);
        $this->assertDatabaseHas('pembelian_barang', ['id' => $beras->id, 'qty_belum' => 20]);
        $this->assertDatabaseHas('pembelian_barang', ['id' => $minyak->id, 'qty_belum' => 10]);
    }

    public function test_successful_kegiatan_store_redirects_to_kegiatan_tab(): void
    {
        $response = $this->actingAs($this->admin())->post(route('barang.kegiatan.store'), [
            'nama_anggaran' => 'Kegiatan Tab Uji',
            'kategori' => 'barang_bantuan',
            'target_paket' => 10,
            'satuan' => 'paket',
            'anggaran' => 1000000,
            'realisasi' => 0,
            'catatan' => 'Uji tab',
            'form_type' => 'kegiatan',
        ]);

        $response->assertRedirect(route('barang.index', ['tab' => 'kegiatan']));
    }

    public function test_successful_pembelian_store_redirects_to_pembelian_tab(): void
    {
        $response = $this->actingAs($this->admin())->post(route('barang.pembelian.store'), [
            'nama_barang' => 'Barang Tab Uji',
            'batch' => 'Batch Uji',
            'qty_rencana' => 10,
            'qty_terbeli' => 2,
            'harga_satuan' => 100000,
            'form_type' => 'pembelian',
        ]);

        $response->assertRedirect(route('barang.index', ['tab' => 'pembelian']));
    }

    public function test_successful_pembelian_update_redirects_to_pembelian_tab(): void
    {
        $pembelian = \App\Models\PembelianBarang::create([
            'nama_barang' => 'Barang Sebelum Update',
            'batch' => 'Batch Lama',
            'qty_rencana' => 10,
            'qty_terbeli' => 2,
            'qty_belum' => 8,
            'harga_satuan' => 100000,
            'anggaran' => 1000000,
            'realisasi' => 200000,
            'sisa' => 800000,
            'persen_real' => 20,
        ]);

        $response = $this->actingAs($this->admin())->put(route('barang.pembelian.update', $pembelian), [
            'nama_barang' => 'Barang Setelah Update',
            'batch' => 'Batch Baru',
            'qty_rencana' => 10,
            'qty_terbeli' => 5,
            'harga_satuan' => 100000,
        ]);

        $response->assertRedirect(route('barang.index', ['tab' => 'pembelian']));
        $this->assertDatabaseHas('pembelian_barang', [
            'id' => $pembelian->id,
            'nama_barang' => 'Barang Setelah Update',
        ]);
    }

    public function test_pembelian_calculates_financial_totals_from_price_and_quantities(): void
    {
        $response = $this->actingAs($this->admin())->post(route('barang.pembelian.store'), [
            'nama_barang' => 'Beras Uji Otomatis',
            'batch' => 'Batch Uji',
            'qty_rencana' => 100,
            'qty_terbeli' => 40,
            'harga_satuan' => 150000,
            'anggaran' => 1,
            'realisasi' => 1,
            'form_type' => 'pembelian',
        ]);

        $response->assertRedirect(route('barang.index', ['tab' => 'pembelian']));
        $this->assertDatabaseHas('pembelian_barang', [
            'nama_barang' => 'Beras Uji Otomatis',
            'anggaran' => 15000000,
            'realisasi' => 6000000,
            'sisa' => 9000000,
            'qty_belum' => 60,
            'persen_real' => 40,
        ]);
    }

    public function test_distribusi_normalizes_optional_numeric_values(): void
    {
        $admin = $this->admin();
        $kelompok = $this->kelompok();
        $penerima = $this->penerima($kelompok, '1101010101010001');
        Storage::fake('public');
        $payload = array_merge($this->payload($kelompok), [
            'bukti_file' => UploadedFile::fake()->create('bukti-distribusi.pdf', 100, 'application/pdf'),
        ]);

        $response = $this->actingAs($admin)->post('/distribusi', $payload);

        $response->assertRedirect(route('distribusi.index'));
        $this->assertDatabaseHas('distribusis', [
            'nama_kegiatan' => 'Distribusi Uji Tahap Dua',
            'estimasi_nilai_total' => 0,
            'sumber_dana' => '',
        ]);
        $distribusi = Distribusi::where('nama_kegiatan', 'Distribusi Uji Tahap Dua')->firstOrFail();
        $this->assertNotNull($distribusi->bukti_file);
        Storage::disk('public')->assertExists($distribusi->bukti_file);
        $this->assertDatabaseHas('penerima_distribusi', [
            'penerima_id' => $penerima->id,
            'status' => 'terjadwal',
        ]);
    }

    public function test_distribusi_can_store_multiple_photos_and_documents(): void
    {
        $admin = $this->admin();
        $kelompok = $this->kelompok();
        Storage::fake('public');

        $payload = array_merge($this->payload($kelompok), [
            'lampiran' => [
                UploadedFile::fake()->create('foto-lapangan-1.jpg', 100, 'image/jpeg'),
                UploadedFile::fake()->create('foto-lapangan-2.png', 120, 'image/png'),
                UploadedFile::fake()->create('berita-acara.pdf', 200, 'application/pdf'),
            ],
        ]);

        $this->actingAs($admin)->post('/distribusi', $payload)
            ->assertRedirect(route('distribusi.index'));

        $distribusi = Distribusi::where('nama_kegiatan', 'Distribusi Uji Tahap Dua')->firstOrFail();
        $lampiran = DB::table('distribusi_lampirans')
            ->where('distribusi_id', $distribusi->id)
            ->orderBy('id')
            ->get();

        $this->assertCount(3, $lampiran);
        $this->assertSame(
            ['foto-lapangan-1.jpg', 'foto-lapangan-2.png', 'berita-acara.pdf'],
            $lampiran->pluck('nama_asli')->all()
        );
        $this->assertSame(['foto', 'foto', 'dokumen'], $lampiran->pluck('jenis')->all());
        foreach ($lampiran as $file) {
            Storage::disk('public')->assertExists($file->path);
        }

        $this->get(route('distribusi.edit', $distribusi))
            ->assertOk()
            ->assertSee('name="lampiran[]"', false)
            ->assertSee('multiple', false)
            ->assertSee('foto-lapangan-1.jpg')
            ->assertSee('berita-acara.pdf');

        $this->get(route('distribusi.show', $distribusi))
            ->assertOk()
            ->assertSee('Dokumentasi Lapangan')
            ->assertSee('foto-lapangan-2.png')
            ->assertSee('berita-acara.pdf');
    }

    public function test_distribusi_rejects_unsupported_and_oversized_attachments(): void
    {
        $admin = $this->admin();
        $kelompok = $this->kelompok();
        Storage::fake('public');

        $this->actingAs($admin)->from(route('distribusi.create'))->post('/distribusi', array_merge($this->payload($kelompok), [
            'lampiran' => [UploadedFile::fake()->create('program.exe', 100, 'application/octet-stream')],
        ]))->assertRedirect(route('distribusi.create'))
            ->assertSessionHasErrors('lampiran.0');

        $this->from(route('distribusi.create'))->post('/distribusi', array_merge($this->payload($kelompok), [
            'lampiran' => [UploadedFile::fake()->create('terlalu-besar.pdf', 5121, 'application/pdf')],
        ]))->assertRedirect(route('distribusi.create'))
            ->assertSessionHasErrors('lampiran.0');

        $this->assertDatabaseCount('distribusis', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_distribusi_rejects_more_than_ten_attachments_per_request(): void
    {
        $admin = $this->admin();
        $kelompok = $this->kelompok();
        Storage::fake('public');
        $files = [];
        for ($i = 1; $i <= 11; $i++) {
            $files[] = UploadedFile::fake()->create("foto-{$i}.jpg", 10, 'image/jpeg');
        }

        $this->actingAs($admin)->from(route('distribusi.create'))->post('/distribusi', array_merge($this->payload($kelompok), [
            'lampiran' => $files,
        ]))->assertRedirect(route('distribusi.create'))
            ->assertSessionHasErrors('lampiran');

        $this->assertDatabaseCount('distribusis', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_distribusi_update_can_add_and_remove_selected_attachments(): void
    {
        $admin = $this->admin();
        $kelompok = $this->kelompok();
        Storage::fake('public');

        $this->actingAs($admin)->post('/distribusi', array_merge($this->payload($kelompok), [
            'lampiran' => [
                UploadedFile::fake()->create('hapus-saya.jpg', 100, 'image/jpeg'),
                UploadedFile::fake()->create('tetap-ada.pdf', 100, 'application/pdf'),
            ],
        ]))->assertRedirect(route('distribusi.index'));

        $distribusi = Distribusi::where('nama_kegiatan', 'Distribusi Uji Tahap Dua')->firstOrFail();
        $existing = DB::table('distribusi_lampirans')
            ->where('distribusi_id', $distribusi->id)
            ->orderBy('id')
            ->get();

        $this->put(route('distribusi.update', $distribusi), array_merge($this->payload($kelompok), [
            'hapus_lampiran' => [$existing[0]->id],
            'lampiran' => [UploadedFile::fake()->create('tambahan-lapangan.png', 150, 'image/png')],
        ]))->assertRedirect(route('distribusi.index'));

        $remaining = DB::table('distribusi_lampirans')
            ->where('distribusi_id', $distribusi->id)
            ->orderBy('id')
            ->get();

        $this->assertCount(2, $remaining);
        $this->assertSame(['tetap-ada.pdf', 'tambahan-lapangan.png'], $remaining->pluck('nama_asli')->all());
        $this->assertDatabaseMissing('distribusi_lampirans', ['id' => $existing[0]->id]);
        Storage::disk('public')->assertMissing($existing[0]->path);
        Storage::disk('public')->assertExists($existing[1]->path);
        Storage::disk('public')->assertExists($remaining->last()->path);
    }

    public function test_non_admin_cannot_upload_distribution_attachments(): void
    {
        $kelompok = $this->kelompok();
        $ketua = User::create([
            'name' => 'Ketua Uji Lampiran',
            'email' => 'ketua-lampiran@example.test',
            'password' => Hash::make('test-password'),
            'role' => 'ketua_kelompok',
            'status' => 'aktif',
            'kelompok_id' => $kelompok->id,
        ]);
        Storage::fake('public');

        $this->actingAs($ketua)->post('/distribusi', array_merge($this->payload($kelompok), [
            'lampiran' => [UploadedFile::fake()->create('tidak-boleh.jpg', 100, 'image/jpeg')],
        ]))->assertForbidden();

        $this->assertDatabaseCount('distribusis', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_removing_legacy_attachment_clears_legacy_column_and_file(): void
    {
        $admin = $this->admin();
        $kelompok = $this->kelompok();
        Storage::fake('public');

        $this->actingAs($admin)->post('/distribusi', array_merge($this->payload($kelompok), [
            'bukti_file' => UploadedFile::fake()->create('bukti-tunggal-lama.pdf', 100, 'application/pdf'),
        ]));
        $distribusi = Distribusi::where('nama_kegiatan', 'Distribusi Uji Tahap Dua')->firstOrFail();
        $legacy = DB::table('distribusi_lampirans')->where('distribusi_id', $distribusi->id)->first();

        $this->put(route('distribusi.update', $distribusi), array_merge($this->payload($kelompok), [
            'hapus_lampiran' => [$legacy->id],
        ]))->assertRedirect(route('distribusi.index'));

        $this->assertNull($distribusi->fresh()->bukti_file);
        $this->assertDatabaseMissing('distribusi_lampirans', ['id' => $legacy->id]);
        Storage::disk('public')->assertMissing($legacy->path);
    }

    public function test_deleting_distribusi_cleans_up_all_attachment_files(): void
    {
        $admin = $this->admin();
        $kelompok = $this->kelompok();
        Storage::fake('public');

        $this->actingAs($admin)->post('/distribusi', array_merge($this->payload($kelompok), [
            'lampiran' => [
                UploadedFile::fake()->create('foto-hapus.jpg', 100, 'image/jpeg'),
                UploadedFile::fake()->create('dokumen-hapus.pdf', 100, 'application/pdf'),
            ],
        ]));

        $distribusi = Distribusi::where('nama_kegiatan', 'Distribusi Uji Tahap Dua')->firstOrFail();
        $paths = DB::table('distribusi_lampirans')
            ->where('distribusi_id', $distribusi->id)
            ->pluck('path');

        $this->delete(route('distribusi.destroy', $distribusi))
            ->assertRedirect(route('distribusi.index'));

        $this->assertDatabaseMissing('distribusis', ['id' => $distribusi->id]);
        $this->assertDatabaseCount('distribusi_lampirans', 0);
        foreach ($paths as $path) {
            Storage::disk('public')->assertMissing($path);
        }
    }

    public function test_multi_attachment_migration_preserves_legacy_single_proof(): void
    {
        $admin = $this->admin();
        $kelompok = $this->kelompok();
        $distribusi = Distribusi::create(array_merge($this->payload($kelompok), [
            'kode_distribusi' => 'DST-LEGACY-01',
            'bukti_file' => 'distribusi/bukti/bukti-lama.pdf',
            'created_by' => $admin->id,
        ]));

        Schema::drop('distribusi_lampirans');
        $migration = require database_path('migrations/2026_07_30_150000_create_distribusi_lampirans_table.php');
        $migration->up();

        $this->assertDatabaseHas('distribusi_lampirans', [
            'distribusi_id' => $distribusi->id,
            'path' => 'distribusi/bukti/bukti-lama.pdf',
            'nama_asli' => 'bukti-lama.pdf',
            'jenis' => 'dokumen',
            'created_by' => $admin->id,
        ]);

        $migration->down();
        $this->assertFalse(Schema::hasTable('distribusi_lampirans'));
        $this->assertSame('distribusi/bukti/bukti-lama.pdf', $distribusi->fresh()->bukti_file);
        $migration->up();
    }

    public function test_multi_attachment_migration_refuses_lossy_rollback(): void
    {
        $admin = $this->admin();
        $kelompok = $this->kelompok();
        $distribusi = Distribusi::create(array_merge($this->payload($kelompok), [
            'kode_distribusi' => 'DST-MULTI-01',
            'created_by' => $admin->id,
        ]));
        foreach (['foto-1.jpg', 'foto-2.jpg'] as $name) {
            DB::table('distribusi_lampirans')->insert([
                'distribusi_id' => $distribusi->id,
                'path' => 'distribusi/lampiran/' . $name,
                'nama_asli' => $name,
                'jenis' => 'foto',
                'created_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $migration = require database_path('migrations/2026_07_30_150000_create_distribusi_lampirans_table.php');
        try {
            $migration->down();
            $this->fail('Rollback seharusnya ditolak agar beberapa lampiran tidak hilang.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Rollback dibatalkan', $e->getMessage());
        }
        $this->assertTrue(Schema::hasTable('distribusi_lampirans'));
        $this->assertDatabaseCount('distribusi_lampirans', 2);
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
