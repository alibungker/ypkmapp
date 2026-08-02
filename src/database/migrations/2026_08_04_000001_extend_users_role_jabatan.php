<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(30) NOT NULL DEFAULT 'relawan'");
        DB::table('users')->where('role', 'admin')->update(['role' => 'super_admin']);
    }

    public function down(): void
    {
        DB::table('users')->whereIn('role', ['super_admin','pengurus','bendahara','staff'])->update(['role' => 'relawan']);
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','relawan','ketua_kelompok') NOT NULL DEFAULT 'relawan'");
    }
};
