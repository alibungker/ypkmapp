<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('distribusi_lampirans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribusi_id')->constrained('distribusis')->cascadeOnDelete();
            $table->string('path');
            $table->string('nama_asli');
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('ukuran')->default(0);
            $table->string('jenis', 20)->default('dokumen')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        if (Schema::hasColumn('distribusis', 'bukti_file')) {
            $now = now();
            $rows = DB::table('distribusis')
                ->whereNotNull('bukti_file')
                ->where('bukti_file', '!=', '')
                ->get(['id', 'bukti_file', 'created_by']);

            foreach ($rows as $row) {
                $extension = strtolower(pathinfo($row->bukti_file, PATHINFO_EXTENSION));
                DB::table('distribusi_lampirans')->insert([
                    'distribusi_id' => $row->id,
                    'path' => $row->bukti_file,
                    'nama_asli' => basename($row->bukti_file),
                    'mime_type' => null,
                    'ukuran' => 0,
                    'jenis' => in_array($extension, ['jpg', 'jpeg', 'png'], true) ? 'foto' : 'dokumen',
                    'created_by' => $row->created_by,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('distribusi_lampirans')) {
            return;
        }

        $hasMultiple = DB::table('distribusi_lampirans')
            ->select('distribusi_id')
            ->groupBy('distribusi_id')
            ->havingRaw('COUNT(*) > 1')
            ->exists();
        if ($hasMultiple) {
            throw new RuntimeException(
                'Rollback dibatalkan: ada Distribusi dengan beberapa lampiran yang tidak dapat dipindahkan ke kolom bukti_file tunggal.'
            );
        }

        $attachments = DB::table('distribusi_lampirans')->get(['distribusi_id', 'path']);
        if ($attachments->isNotEmpty() && !Schema::hasColumn('distribusis', 'bukti_file')) {
            throw new RuntimeException('Rollback dibatalkan: kolom bukti_file tidak tersedia untuk mempertahankan lampiran.');
        }
        foreach ($attachments as $attachment) {
            DB::table('distribusis')->where('id', $attachment->distribusi_id)->update([
                'bukti_file' => $attachment->path,
            ]);
        }

        Schema::drop('distribusi_lampirans');
    }
};
