<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topup_anggarans', function (Blueprint $table) {
            if (!Schema::hasColumn('topup_anggarans', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('anggaran_id')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('topup_anggarans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
