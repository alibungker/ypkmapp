<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel top-up anggaran dengan alur persetujuan
        Schema::create('topup_anggarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggaran_id')->nullable()->constrained('anggarans')->nullOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal');
            $table->string('sumber_dana', 150)->nullable();
            $table->string('nomor_referensi', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('bukti', 255)->nullable();
            $table->foreignId('diajukan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['draft','diajukan','disetujui','ditolak'])->default('draft');
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
        });

        // Tabel audit log aksi sensitif
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('aksi', 100);
            $table->string('model', 100)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('detail')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topup_anggarans');
        Schema::dropIfExists('audit_logs');
    }
};
