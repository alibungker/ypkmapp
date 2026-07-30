<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Perluas tabel anggarans untuk kegiatan
        Schema::table('anggarans', function (Blueprint $table) {
            if (!Schema::hasColumn('anggarans', 'nama_anggaran')) {
                $table->string('nama_anggaran')->nullable()->after('id');
                $table->integer('target_paket')->nullable()->after('kategori');
                $table->string('satuan')->nullable()->after('realisasi');
            }
        });

        // Tabel baru untuk pembelian barang (Batch 2)
        if (!Schema::hasTable('pembelian_barang')) {
            Schema::create('pembelian_barang', function (Blueprint $table) {
                $table->id();
                $table->string('nama_barang');
                $table->string('batch')->nullable();
                $table->integer('qty_rencana')->default(0);
                $table->integer('qty_terbeli')->default(0);
                $table->integer('qty_belum')->default(0);
                $table->decimal('harga_satuan', 15, 2)->default(0);
                $table->decimal('anggaran', 15, 2)->default(0);
                $table->decimal('realisasi', 15, 2)->default(0);
                $table->decimal('sisa', 15, 2)->default(0);
                $table->decimal('persen_real', 5, 1)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::table('anggarans', function (Blueprint $table) {
            $table->dropColumn(['nama_anggaran', 'target_paket', 'satuan']);
        });
        Schema::dropIfExists('pembelian_barang');
    }
};
