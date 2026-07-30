<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nik')) {
                $table->string('nik', 20)->nullable()->after('name');
                $table->string('tempat_lahir')->nullable()->after('nik');
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
                $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tanggal_lahir');
                $table->text('alamat_lengkap')->nullable()->after('wilayah_desa');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat_lengkap']);
        });
    }
};
