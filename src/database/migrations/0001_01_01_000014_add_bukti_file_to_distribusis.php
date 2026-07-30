<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('distribusis', 'bukti_file')) {
            Schema::table('distribusis', function (Blueprint $table) {
                $table->string('bukti_file')->nullable()->after('catatan');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('distribusis', 'bukti_file')) {
            Schema::table('distribusis', fn (Blueprint $table) => $table->dropColumn('bukti_file'));
        }
    }
};
