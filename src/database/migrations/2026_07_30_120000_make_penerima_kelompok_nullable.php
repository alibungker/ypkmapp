<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penerimas', function (Blueprint $table) {
            $table->foreignId('kelompok_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::table('penerimas')->whereNull('kelompok_id')->exists()) {
            throw new RuntimeException(
                'Rollback dibatalkan: masih ada penerima publik yang belum ditetapkan ke kelompok.'
            );
        }

        Schema::table('penerimas', function (Blueprint $table) {
            $table->foreignId('kelompok_id')->nullable(false)->change();
        });
    }
};
