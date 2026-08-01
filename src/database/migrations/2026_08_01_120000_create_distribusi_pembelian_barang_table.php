<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribusi_pembelian_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribusi_id')->constrained('distribusis')->cascadeOnDelete();
            $table->foreignId('pembelian_barang_id')->constrained('pembelian_barang')->restrictOnDelete();
            $table->unsignedInteger('jumlah');
            $table->timestamps();
            $table->index(['distribusi_id', 'pembelian_barang_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribusi_pembelian_barang');
    }
};
