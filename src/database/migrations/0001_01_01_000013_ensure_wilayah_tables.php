<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('wilayah')) {
            Schema::create('wilayah', function (Blueprint $table) {
                $table->string('kode', 20)->primary();
                $table->string('nama', 150)->index();
            });
        }

        if (!Schema::hasTable('wilayah_boundaries')) {
            Schema::create('wilayah_boundaries', function (Blueprint $table) {
                $table->string('kode', 20)->primary();
                $table->string('nama', 150)->index();
                $table->decimal('lat', 12, 9)->nullable();
                $table->decimal('lng', 12, 9)->nullable();
                $table->longText('path')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Tidak menghapus master wilayah agar rollback kode tidak menghilangkan data referensi produksi.
    }
};
