<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelompoks', function (Blueprint $table) {
            if (!Schema::hasColumn('kelompoks', 'desa')) {
                $table->string('desa')->nullable()->after('kecamatan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kelompoks', function (Blueprint $table) {
            $table->dropColumn('desa');
        });
    }
};
