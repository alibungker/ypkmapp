<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relawans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('daerah_tugas')->nullable();
            $table->text('keahlian')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        Schema::create('stok_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang_bantuans')->cascadeOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->string('sumber')->nullable();
            $table->decimal('nilai_total', 15, 2)->default(0);
            $table->date('tanggal_masuk');
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('anggarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribusi_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('kategori', ['barang_bantuan', 'transportasi', 'konsumsi', 'sewa', 'atk', 'cadangan']);
            $table->decimal('anggaran', 15, 2)->default(0);
            $table->decimal('realisasi', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('action');
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs');
        Schema::dropIfExists('anggarans');
        Schema::dropIfExists('stok_barangs');
        Schema::dropIfExists('relawans');
    }
};
