<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_bantuans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('kategori', ['sembako', 'pakaian', 'alat_sekolah', 'obat', 'uang_tunai', 'lainnya']);
            $table->enum('satuan', ['kg', 'liter', 'pcs', 'paket', 'karton']);
            $table->decimal('harga_perkiraan', 12, 2)->default(0);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('dana_donaturs', function (Blueprint $table) {
            $table->id();
            $table->string('donatur');
            $table->date('tanggal_masuk');
            $table->decimal('jumlah', 15, 2);
            $table->enum('jenis', ['uang_tunai', 'transfer', 'barang']);
            $table->text('keterangan')->nullable();
            $table->string('bukti_transfer')->nullable();
            $table->foreignId('dicatat_oleh')->constrained('users');
            $table->timestamps();
        });

        Schema::create('biaya_operasionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribusi_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('kategori', ['transportasi', 'konsumsi', 'sewa', 'atk', 'komunikasi', 'lainnya']);
            $table->text('deskripsi');
            $table->decimal('jumlah', 12, 2);
            $table->date('tanggal');
            $table->string('bukti_foto')->nullable();
            $table->foreignId('dicatat_oleh')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biaya_operasionals');
        Schema::dropIfExists('dana_donaturs');
        Schema::dropIfExists('barang_bantuans');
    }
};
