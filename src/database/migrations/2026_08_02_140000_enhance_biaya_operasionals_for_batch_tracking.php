<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_operasionals', function (Blueprint $table) {
            $table->string('batch_kegiatan', 100)->nullable()->after('distribusi_id')->index();
            $table->string('pihak_penerima', 150)->nullable()->after('batch_kegiatan')->index();
        });

        DB::statement("ALTER TABLE biaya_operasionals MODIFY kategori VARCHAR(100) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE biaya_operasionals MODIFY kategori ENUM('transportasi','konsumsi','sewa','atk','komunikasi','lainnya') NOT NULL");
        Schema::table('biaya_operasionals', function (Blueprint $table) {
            $table->dropIndex(['batch_kegiatan']);
            $table->dropIndex(['pihak_penerima']);
            $table->dropColumn(['batch_kegiatan', 'pihak_penerima']);
        });
    }
};
