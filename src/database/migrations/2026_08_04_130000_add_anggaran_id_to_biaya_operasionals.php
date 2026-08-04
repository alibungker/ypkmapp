<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_operasionals', function (Blueprint $table) {
            $table->foreignId('anggaran_id')->nullable()->after('distribusi_id')->constrained('anggarans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('biaya_operasionals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('anggaran_id');
        });
    }
};
