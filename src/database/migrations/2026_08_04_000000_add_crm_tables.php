<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom tambahan users: nip, jabatan, status_aktif
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nip')) {
                $table->string('nip', 20)->nullable()->after('nik');
            }
            if (!Schema::hasColumn('users', 'jabatan')) {
                $table->string('jabatan', 100)->nullable()->after('nip');
            }
            if (!Schema::hasColumn('users', 'status_aktif')) {
                $table->boolean('status_aktif')->default(true)->after('is_active');
            }
        });

        // Mitra kerja sama & donatur
        Schema::create('mitra', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instansi');
            $table->enum('kategori', ['csr_perusahaan', 'lembaga_donor', 'komunitas', 'perorangan'])->default('perorangan');
            $table->string('pic_nama')->nullable();
            $table->string('pic_email')->nullable();
            $table->string('pic_phone', 20)->nullable();
            $table->string('no_mou')->nullable();
            $table->enum('jenis_dukungan', ['finansial', 'barang', 'jasa'])->default('finansial');
            $table->decimal('total_kontribusi', 15, 2)->default(0);
            $table->text('alamat')->nullable();
            $table->string('kontak_person')->nullable();
            $table->timestamps();
        });

        // Relawan: tambah kolom detail
        Schema::table('relawans', function (Blueprint $table) {
            if (!Schema::hasColumn('relawans', 'nama_lengkap')) {
                $table->string('nama_lengkap')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('relawans', 'nik')) {
                $table->string('nik', 20)->nullable()->after('nama_lengkap');
            }
            if (!Schema::hasColumn('relawans', 'tempat_tanggal_lahir')) {
                $table->string('tempat_tanggal_lahir')->nullable()->after('nik');
            }
            if (!Schema::hasColumn('relawans', 'jenis_kelamin')) {
                $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tempat_tanggal_lahir');
            }
            if (!Schema::hasColumn('relawans', 'phone')) {
                $table->string('phone', 20)->nullable()->after('jenis_kelamin');
            }
            if (!Schema::hasColumn('relawans', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('relawans', 'keahlian_utama')) {
                $table->string('keahlian_utama', 100)->nullable()->after('email');
            }
            if (!Schema::hasColumn('relawans', 'status_ketersediaan')) {
                $table->enum('status_ketersediaan', ['siap_tanggap_bencana', 'akhir_pekan', 'nonaktif'])->default('siap_tanggap_bencana')->after('status');
            }
            if (!Schema::hasColumn('relawans', 'jam_kontribusi')) {
                $table->integer('jam_kontribusi')->default(0)->after('status_ketersediaan');
            }
            if (!Schema::hasColumn('relawans', 'domisili_kota')) {
                $table->string('domisili_kota', 100)->nullable()->after('jam_kontribusi');
            }
        });

        // Penerima bantuan: tambah kolom kerentanan
        Schema::table('penerimas', function (Blueprint $table) {
            if (!Schema::hasColumn('penerimas', 'kategori_kerentanan')) {
                $table->enum('kategori_kerentanan', ['lansia', 'yatim_piatu', 'korban_bencana', 'keluarga_miskin'])->nullable()->after('status');
            }
            if (!Schema::hasColumn('penerimas', 'tingkat_penghasilan')) {
                $table->enum('tingkat_penghasilan', ['rendah', 'menengah', 'tinggi', 'tidak_ada'])->nullable()->after('kategori_kerentanan');
            }
            if (!Schema::hasColumn('penerimas', 'status_kelayakan')) {
                $table->enum('status_kelayakan', ['layak', 'perlu_verifikasi', 'tidak_layak'])->default('perlu_verifikasi')->after('tingkat_penghasilan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penerimas', function (Blueprint $table) {
            $table->dropColumn(['kategori_kerentanan', 'tingkat_penghasilan', 'status_kelayakan']);
        });
        Schema::table('relawans', function (Blueprint $table) {
            $table->dropColumn(['nama_lengkap', 'nik', 'tempat_tanggal_lahir', 'jenis_kelamin', 'phone', 'email', 'keahlian_utama', 'status_ketersediaan', 'jam_kontribusi', 'domisili_kota']);
        });
        Schema::dropIfExists('mitra');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'jabatan', 'status_aktif']);
        });
    }
};
