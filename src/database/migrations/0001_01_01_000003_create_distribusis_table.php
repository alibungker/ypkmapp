<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribusis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_distribusi')->unique();
            $table->string('nama_kegiatan');
            $table->date('tanggal');
            $table->string('lokasi');
            $table->string('titik_koordinat')->nullable();
            $table->foreignId('kelompok_id')->constrained('kelompoks');
            $table->string('jenis_bantuan');
            $table->integer('jumlah_paket')->default(0);
            $table->decimal('estimasi_nilai_total', 15, 2)->default(0);
            $table->string('sumber_dana')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['direncanakan', 'berlangsung', 'selesai', 'dibatalkan'])->default('direncanakan');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribusis');
    }
};
