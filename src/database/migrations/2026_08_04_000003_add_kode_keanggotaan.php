<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'kode_keanggotaan')) {
                $table->string('kode_keanggotaan', 30)->nullable()->unique()->after('nik');
            }
        });

        Schema::table('relawans', function (Blueprint $table) {
            if (!Schema::hasColumn('relawans', 'kode_keanggotaan')) {
                $table->string('kode_keanggotaan', 30)->nullable()->unique()->after('nik');
            }
        });
    }

    public function down(): void
    {
        Schema::table('relawans', function (Blueprint $table) {
            $table->dropColumn('kode_keanggotaan');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kode_keanggotaan');
        });
    }
};
