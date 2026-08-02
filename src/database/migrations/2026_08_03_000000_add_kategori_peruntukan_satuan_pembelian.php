<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel master kategori barang
        Schema::create('kategori_barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('jenis_default')->default('bantuan'); // bantuan|operasional|aset
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // Tambah kolom ke pembelian_barang
        Schema::table('pembelian_barang', function (Blueprint $table) {
            if (!Schema::hasColumn('pembelian_barang', 'kategori_barang_id')) {
                $table->foreignId('kategori_barang_id')->nullable()->after('id')->constrained('kategori_barangs')->nullOnDelete();
            }
            if (!Schema::hasColumn('pembelian_barang', 'jenis_peruntukan')) {
                $table->string('jenis_peruntukan')->default('bantuan')->after('kategori_barang_id'); // bantuan|operasional|aset
            }
            if (!Schema::hasColumn('pembelian_barang', 'satuan')) {
                $table->string('satuan')->nullable()->after('jenis_peruntukan');
            }
            if (!Schema::hasColumn('pembelian_barang', 'tanggal_pembelian')) {
                $table->date('tanggal_pembelian')->nullable()->after('satuan');
            }
            if (!Schema::hasColumn('pembelian_barang', 'supplier')) {
                $table->string('supplier')->nullable()->after('tanggal_pembelian');
            }
            if (!Schema::hasColumn('pembelian_barang', 'nomor_invoice')) {
                $table->string('nomor_invoice')->nullable()->after('supplier');
            }
            if (!Schema::hasColumn('pembelian_barang', 'metode_pembayaran')) {
                $table->string('metode_pembayaran')->nullable()->after('nomor_invoice'); // tunai|transfer|lainnya
            }
            if (!Schema::hasColumn('pembelian_barang', 'bukti_pembelian')) {
                $table->string('bukti_pembelian')->nullable()->after('metode_pembayaran');
            }
            if (!Schema::hasColumn('pembelian_barang', 'catatan')) {
                $table->text('catatan')->nullable()->after('bukti_pembelian');
            }
            if (!Schema::hasColumn('pembelian_barang', 'status')) {
                $table->string('status')->default('diterima')->after('catatan'); // rencana|dipesan|diterima|batal
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembelian_barang', function (Blueprint $table) {
            $table->dropColumn([
                'kategori_barang_id','jenis_peruntukan','satuan','tanggal_pembelian',
                'supplier','nomor_invoice','metode_pembayaran','bukti_pembelian',
                'catatan','status'
            ]);
        });
        Schema::dropIfExists('kategori_barangs');
    }
};
