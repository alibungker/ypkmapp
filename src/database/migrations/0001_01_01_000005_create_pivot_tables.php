<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerima_distribusi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penerima_id')->constrained()->cascadeOnDelete();
            $table->foreignId('distribusi_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['terjadwal', 'terkirim', 'diterima'])->default('terjadwal');
            $table->boolean('tanda_terima')->default(false);
            $table->string('foto_bukti')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });

        Schema::create('distribusi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribusi_id')->constrained()->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barang_bantuans')->cascadeOnDelete();
            $table->decimal('jumlah_per_paket', 12, 2);
            $table->integer('jumlah_paket_distribusi')->default(0);
            $table->decimal('subtotal_nilai', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribusi_items');
        Schema::dropIfExists('penerima_distribusi');
    }
};
