<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('album_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->foreignId('anggaran_id')->nullable()->constrained('anggarans')->nullOnDelete();
            $table->foreignId('distribusi_id')->nullable()->constrained('distribusis')->nullOnDelete();
            $table->string('audio_path')->nullable();
            $table->string('audio_name')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('album_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_kegiatan_id')->constrained('album_kegiatans')->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('album_kegiatans', function (Blueprint $table) {
            $table->foreignId('cover_photo_id')->nullable()->after('distribusi_id')
                ->constrained('album_photos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('album_kegiatans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cover_photo_id');
        });
        Schema::dropIfExists('album_photos');
        Schema::dropIfExists('album_kegiatans');
    }
};
