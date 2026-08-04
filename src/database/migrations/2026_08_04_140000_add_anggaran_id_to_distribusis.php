<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribusis', function (Blueprint $table) {
            if (!Schema::hasColumn('distribusis', 'anggaran_id')) {
                $table->foreignId('anggaran_id')->nullable()->after('nama_kegiatan')->constrained('anggarans')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('distribusis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('anggaran_id');
        });
    }
};
