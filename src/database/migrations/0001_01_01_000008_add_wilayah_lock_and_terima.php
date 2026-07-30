<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kunci wilayah kerja user (ketua kelompok & relawan)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'wilayah_kabupaten')) {
                $table->string('wilayah_kabupaten')->nullable()->after('role');
                $table->string('wilayah_kecamatan')->nullable()->after('wilayah_kabupaten');
                $table->string('wilayah_desa')->nullable()->after('wilayah_kecamatan');
            }
        });

        // Checklist terima bantuan oleh relawan
        Schema::table('penerimas', function (Blueprint $table) {
            if (!Schema::hasColumn('penerimas', 'terima_bantuan')) {
                $table->boolean('terima_bantuan')->default(false)->after('verified_at');
                $table->unsignedBigInteger('terima_by')->nullable()->after('terima_bantuan');
                $table->timestamp('terima_at')->nullable()->after('terima_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['wilayah_kabupaten', 'wilayah_kecamatan', 'wilayah_desa']);
        });
        Schema::table('penerimas', function (Blueprint $table) {
            $table->dropColumn(['terima_bantuan', 'terima_by', 'terima_at']);
        });
    }
};
