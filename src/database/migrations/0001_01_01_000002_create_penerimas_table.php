<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerimas', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20)->unique();
            $table->string('no_kk', 20)->nullable();
            $table->string('nama');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->text('alamat');
            $table->string('provinsi')->nullable();
            $table->string('kabupaten');
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('rt_rw', 20)->nullable();
            $table->string('phone', 20);
            $table->integer('jumlah_keluarga')->default(1);
            $table->string('pekerjaan')->nullable();
            $table->decimal('penghasilan', 12, 2)->nullable();
            $table->string('titik_koordinat')->nullable();
            $table->string('foto_ktp')->nullable();
            $table->string('foto_kk')->nullable();
            $table->string('foto_rumah')->nullable();
            $table->enum('sumber_data', ['mandiri', 'relawan', 'ketua_kelompok']);
            $table->enum('status', ['pending', 'terverifikasi', 'ditolak'])->default('pending');
            $table->text('catatan_verifikasi')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('kelompok_id')->constrained('kelompoks');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerimas');
    }
};
