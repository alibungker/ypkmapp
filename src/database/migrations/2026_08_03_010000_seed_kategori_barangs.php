<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $kategori = [
            ['kode' => 'pangan',  'nama' => 'Pangan / Sembako',             'jenis_default' => 'bantuan',     'urutan' => 1],
            ['kode' => 'kebersihan', 'nama' => 'Kebersihan & Sanitasi',     'jenis_default' => 'bantuan',     'urutan' => 2],
            ['kode' => 'sandang', 'nama' => 'Sandang / Pakaian',            'jenis_default' => 'bantuan',     'urutan' => 3],
            ['kode' => 'rumah',   'nama' => 'Perlengkapan Rumah Tangga',    'jenis_default' => 'bantuan',     'urutan' => 4],
            ['kode' => 'kesehatan', 'nama' => 'Kesehatan & Medis',          'jenis_default' => 'bantuan',     'urutan' => 5],
            ['kode' => 'pendidikan', 'nama' => 'Pendidikan & ATK',          'jenis_default' => 'bantuan',     'urutan' => 6],
            ['kode' => 'alat_lap', 'nama' => 'Peralatan Lapangan',          'jenis_default' => 'bantuan',     'urutan' => 7],
            ['kode' => 'konsumsi', 'nama' => 'Konsumsi Operasional',        'jenis_default' => 'operasional', 'urutan' => 8],
            ['kode' => 'aset',    'nama' => 'Aset / Inventaris YPKM',       'jenis_default' => 'aset',        'urutan' => 9],
            ['kode' => 'lainnya', 'nama' => 'Lainnya',                      'jenis_default' => 'bantuan',     'urutan' => 99],
        ];
        foreach ($kategori as $k) {
            $k['created_at'] = now();
            $k['updated_at'] = now();
            DB::table('kategori_barangs')->insert($k);
        }
        // Klasifikasi 8 barang lama
        $mapping = [
            'Container Box 52L'  => ['kategori' => 'rumah',    'jenis' => 'bantuan',     'satuan' => 'kotak',  'supplier' => 'Supplier Batch'],
            'Gula Pasir PSM 2 Kg' => ['kategori' => 'pangan',  'jenis' => 'bantuan',     'satuan' => 'kg',     'supplier' => 'Supplier Batch'],
            'Minyak Bimoli 2L'   => ['kategori' => 'pangan',   'jenis' => 'bantuan',     'satuan' => 'liter',  'supplier' => 'Supplier Batch'],
            'Sabun Lifebouy 400ML' => ['kategori' => 'kebersihan', 'jenis' => 'bantuan', 'satuan' => 'pcs',    'supplier' => 'Supplier Batch'],
            'Sampo Lifebouy 340ML' => ['kategori' => 'kebersihan', 'jenis' => 'bantuan', 'satuan' => 'pcs',    'supplier' => 'Supplier Batch'],
            'Ember 12 Liter'     => ['kategori' => 'rumah',    'jenis' => 'bantuan',     'satuan' => 'pcs',    'supplier' => 'Supplier Batch'],
            'Handuk 4 lembar/paket' => ['kategori' => 'sandang', 'jenis' => 'bantuan',  'satuan' => 'paket',  'supplier' => 'Supplier Batch'],
            'Beras Jempol 10 Kg' => ['kategori' => 'pangan',   'jenis' => 'bantuan',     'satuan' => 'kg',     'supplier' => 'Supplier Batch'],
        ];
        $katMap = DB::table('kategori_barangs')->pluck('id', 'kode');
        foreach ($mapping as $nama => $m) {
            DB::table('pembelian_barang')
                ->where('nama_barang', $nama)
                ->update([
                    'kategori_barang_id' => $katMap[$m['kategori']] ?? null,
                    'jenis_peruntukan' => $m['jenis'],
                    'satuan' => $m['satuan'],
                    'supplier' => $m['supplier'],
                    'status' => 'diterima',
                ]);
        }
    }
    public function down(): void
    {
        DB::table('pembelian_barang')->update([
            'kategori_barang_id' => null, 'jenis_peruntukan' => 'bantuan',
            'satuan' => null, 'supplier' => null, 'status' => 'diterima',
        ]);
        DB::table('kategori_barangs')->where('kode', '!=', '__del__')->delete();
    }
};
