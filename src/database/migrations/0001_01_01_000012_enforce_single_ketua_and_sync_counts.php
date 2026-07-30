<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu kelompok hanya boleh memiliki satu akun ketua.
        Schema::table('users', function (Blueprint $table) {
            $table->unique('kelompok_id', 'users_kelompok_id_unique');
        });

        // Sinkronisasi satu kali untuk kompatibilitas data lama.
        DB::statement('UPDATE kelompoks k SET jumlah_anggota = (SELECT COUNT(*) FROM penerimas p WHERE p.kelompok_id = k.id)');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_kelompok_id_unique');
        });
    }
};
