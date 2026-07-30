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

        // Sinkronisasi satu kali untuk kompatibilitas data lama (portabel MySQL/SQLite).
        DB::table('kelompoks')->orderBy('id')->eachById(function ($kelompok) {
            DB::table('kelompoks')->where('id', $kelompok->id)->update([
                'jumlah_anggota' => DB::table('penerimas')->where('kelompok_id', $kelompok->id)->count(),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_kelompok_id_unique');
        });
    }
};
