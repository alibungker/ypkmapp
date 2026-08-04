<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biaya_buktis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_id')->constrained('biaya_operasionals')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('tipe')->default('gambar'); // gambar | dokumen
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biaya_buktis');
    }
};
