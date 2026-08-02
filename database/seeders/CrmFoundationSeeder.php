<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CrmFoundationSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $kelompokId = DB::table('kelompoks')->orderBy('id')->value('id');
        if (!$kelompokId) {
            throw new \RuntimeException('Seeder CRM butuh minimal satu kelompok penerima.');
        }

        // 1. Pengurus & Staf. Role dipetakan ke RBAC existing: admin/relawan.
        $staff = [
            ['name'=>'M. Ali Murtaza, S.Kom., M.M.','nip'=>'198407102025102038','jabatan'=>'Super Admin','email'=>'admin@ypkm.info','phone'=>'081360001001','role'=>'admin'],
            ['name'=>'Siti Rahmah','nip'=>'YPKM-2026-001','jabatan'=>'Koordinator Lapangan','email'=>'koordinator@ypkm.info','phone'=>'081360001002','role'=>'relawan'],
            ['name'=>'Cut Nuraini','nip'=>'YPKM-2026-002','jabatan'=>'Bendahara','email'=>'bendahara@ypkm.info','phone'=>'081360001003','role'=>'admin'],
            ['name'=>'Fauzan Akbar','nip'=>'YPKM-2026-003','jabatan'=>'Koordinator Logistik','email'=>'logistik@ypkm.info','phone'=>'081360001004','role'=>'relawan'],
            ['name'=>'Nadia Safitri','nip'=>'YPKM-2026-004','jabatan'=>'Staf Data & Dokumentasi','email'=>'data@ypkm.info','phone'=>'081360001005','role'=>'relawan'],
        ];
        foreach ($staff as $s) {
            $existingPassword = User::where('email', $s['email'])->value('password');
            User::updateOrCreate(['email'=>$s['email']], [
                ...$s,
                'password'=>$existingPassword ?: Hash::make(Str::random(32)),
                'is_active'=>true,
                'status_aktif'=>true,
                'wilayah_kabupaten'=>'Aceh',
            ]);
        }

        // 2. Mitra Kerja Sama & Donatur
        $mitra = [
            ['nama_instansi'=>'PT Pupuk Iskandar Muda','kategori'=>'csr_perusahaan','pic_nama'=>'Ahmad Rizal','pic_email'=>'csr@pim.co.id','pic_phone'=>'081260010001','no_mou'=>'MOU/YPKM/PIM/001/2026','jenis_dukungan'=>'finansial','total_kontribusi'=>250000000],
            ['nama_instansi'=>'Bank Syariah Indonesia Region Aceh','kategori'=>'csr_perusahaan','pic_nama'=>'Nurul Hidayah','pic_email'=>'csr.aceh@bankbsi.co.id','pic_phone'=>'081260010002','no_mou'=>'MOU/YPKM/BSI/002/2026','jenis_dukungan'=>'finansial','total_kontribusi'=>175000000],
            ['nama_instansi'=>'Hong Kong SWAB','kategori'=>'lembaga_donor','pic_nama'=>'Michael Wong','pic_email'=>'program@hkswab.org','pic_phone'=>'+85260010003','no_mou'=>'MOU/YPKM/HKSWAB/003/2026','jenis_dukungan'=>'finansial','total_kontribusi'=>500000000],
            ['nama_instansi'=>'Dompet Kemanusiaan Aceh','kategori'=>'lembaga_donor','pic_nama'=>'Rasyidin','pic_email'=>'mitra@dka.or.id','pic_phone'=>'081260010004','no_mou'=>'MOU/YPKM/DKA/004/2026','jenis_dukungan'=>'barang','total_kontribusi'=>120000000],
            ['nama_instansi'=>'Komunitas Muda Peduli Aceh','kategori'=>'komunitas','pic_nama'=>'Faisal Ardi','pic_email'=>'halo@kmpa.id','pic_phone'=>'081260010005','no_mou'=>'PKS/YPKM/KMPA/005/2026','jenis_dukungan'=>'jasa','total_kontribusi'=>45000000],
            ['nama_instansi'=>'Ikatan Dokter Indonesia Cabang Banda Aceh','kategori'=>'komunitas','pic_nama'=>'dr. Sarah Maulida','pic_email'=>'idi.bandaaceh@example.org','pic_phone'=>'081260010006','no_mou'=>'PKS/YPKM/IDI/006/2026','jenis_dukungan'=>'jasa','total_kontribusi'=>85000000],
            ['nama_instansi'=>'H. Abdullah Karim','kategori'=>'perorangan','pic_nama'=>'H. Abdullah Karim','pic_email'=>'abdullah.karim@example.com','pic_phone'=>'081260010007','no_mou'=>null,'jenis_dukungan'=>'finansial','total_kontribusi'=>75000000],
            ['nama_instansi'=>'Nurhayati Hasan','kategori'=>'perorangan','pic_nama'=>'Nurhayati Hasan','pic_email'=>'nurhayati.hasan@example.com','pic_phone'=>'081260010008','no_mou'=>null,'jenis_dukungan'=>'barang','total_kontribusi'=>30000000],
        ];
        foreach ($mitra as $m) {
            DB::table('mitra')->updateOrInsert(['nama_instansi'=>$m['nama_instansi']], $m + ['updated_at'=>$now,'created_at'=>$now]);
        }

        // 3. Relawan: buat akun non-login + profil relawan detail.
        $relawan = [
            ['nama_lengkap'=>'dr. Muhammad Reza','nik'=>'1171010101900001','tempat_tanggal_lahir'=>'Banda Aceh, 1 Januari 1990','jenis_kelamin'=>'L','phone'=>'081370020001','email'=>'reza.medis@relawan.ypkm.info','keahlian_utama'=>'Medis','status_ketersediaan'=>'siap_tanggap_bencana','jam_kontribusi'=>240,'domisili_kota'=>'Banda Aceh'],
            ['nama_lengkap'=>'Nurul Aini, S.Kep.','nik'=>'1171024202920002','tempat_tanggal_lahir'=>'Sigli, 2 Februari 1992','jenis_kelamin'=>'P','phone'=>'081370020002','email'=>'nurul.medis@relawan.ypkm.info','keahlian_utama'=>'Medis','status_ketersediaan'=>'akhir_pekan','jam_kontribusi'=>180,'domisili_kota'=>'Pidie'],
            ['nama_lengkap'=>'Fadli Ramadhan','nik'=>'1171030303930003','tempat_tanggal_lahir'=>'Lhokseumawe, 3 Maret 1993','jenis_kelamin'=>'L','phone'=>'081370020003','email'=>'fadli.logistik@relawan.ypkm.info','keahlian_utama'=>'Logistik','status_ketersediaan'=>'siap_tanggap_bencana','jam_kontribusi'=>320,'domisili_kota'=>'Lhokseumawe'],
            ['nama_lengkap'=>'Rizki Maulana','nik'=>'1171040404940004','tempat_tanggal_lahir'=>'Langsa, 4 April 1994','jenis_kelamin'=>'L','phone'=>'081370020004','email'=>'rizki.sar@relawan.ypkm.info','keahlian_utama'=>'SAR/Evakuasi','status_ketersediaan'=>'siap_tanggap_bencana','jam_kontribusi'=>410,'domisili_kota'=>'Langsa'],
            ['nama_lengkap'=>'Maya Fitriani','nik'=>'1171054505950005','tempat_tanggal_lahir'=>'Bireuen, 5 Mei 1995','jenis_kelamin'=>'P','phone'=>'081370020005','email'=>'maya.it@relawan.ypkm.info','keahlian_utama'=>'IT/Dokumentasi','status_ketersediaan'=>'akhir_pekan','jam_kontribusi'=>155,'domisili_kota'=>'Bireuen'],
            ['nama_lengkap'=>'Taufik Hidayat','nik'=>'1171060606960006','tempat_tanggal_lahir'=>'Takengon, 6 Juni 1996','jenis_kelamin'=>'L','phone'=>'081370020006','email'=>'taufik.pengajar@relawan.ypkm.info','keahlian_utama'=>'Pengajar','status_ketersediaan'=>'akhir_pekan','jam_kontribusi'=>120,'domisili_kota'=>'Aceh Tengah'],
            ['nama_lengkap'=>'Rahmawati','nik'=>'1171074707970007','tempat_tanggal_lahir'=>'Kutacane, 7 Juli 1997','jenis_kelamin'=>'P','phone'=>'081370020007','email'=>'rahma.logistik@relawan.ypkm.info','keahlian_utama'=>'Logistik','status_ketersediaan'=>'siap_tanggap_bencana','jam_kontribusi'=>275,'domisili_kota'=>'Aceh Tenggara'],
            ['nama_lengkap'=>'Iqbal Maulana','nik'=>'1171080808980008','tempat_tanggal_lahir'=>'Meulaboh, 8 Agustus 1998','jenis_kelamin'=>'L','phone'=>'081370020008','email'=>'iqbal.dokumentasi@relawan.ypkm.info','keahlian_utama'=>'IT/Dokumentasi','status_ketersediaan'=>'siap_tanggap_bencana','jam_kontribusi'=>200,'domisili_kota'=>'Aceh Barat'],
        ];
        foreach ($relawan as $r) {
            $existingPassword = User::where('email', $r['email'])->value('password');
            $user = User::updateOrCreate(['email'=>$r['email']], [
                'name'=>$r['nama_lengkap'], 'nik'=>$r['nik'], 'email'=>$r['email'], 'phone'=>$r['phone'],
                'password'=>$existingPassword ?: Hash::make(Str::random(32)), 'role'=>'relawan', 'is_active'=>true, 'status_aktif'=>true,
                'wilayah_kabupaten'=>$r['domisili_kota'],
            ]);
            DB::table('relawans')->updateOrInsert(['user_id'=>$user->id], [
                'nama_lengkap'=>$r['nama_lengkap'], 'nik'=>$r['nik'], 'tempat_tanggal_lahir'=>$r['tempat_tanggal_lahir'],
                'jenis_kelamin'=>$r['jenis_kelamin'], 'phone'=>$r['phone'], 'email'=>$r['email'],
                'keahlian_utama'=>$r['keahlian_utama'], 'keahlian'=>$r['keahlian_utama'],
                'status_ketersediaan'=>$r['status_ketersediaan'], 'jam_kontribusi'=>$r['jam_kontribusi'],
                'domisili_kota'=>$r['domisili_kota'], 'daerah_tugas'=>$r['domisili_kota'], 'status'=>'aktif',
                'created_at'=>$now, 'updated_at'=>$now,
            ]);
        }

        // 4. Penerima Bantuan. Identitas sintetis untuk data awal, bukan data kependudukan nyata.
        $penerima = [
            ['nama'=>'Abdul Rahman','nik'=>'1171010101709001','no_kk'=>'1171010101269001','jumlah_keluarga'=>5,'alamat'=>'Gampong Dayah Husein, Kec. Mila','kabupaten'=>'Pidie','kecamatan'=>'Mila','desa'=>'Dayah Husein','titik_koordinat'=>'5.130112,95.921225','kategori_kerentanan'=>'keluarga_miskin','penghasilan'=>900000,'tingkat_penghasilan'=>'rendah','status_kelayakan'=>'layak'],
            ['nama'=>'Nur Aisyah','nik'=>'1171024202759002','no_kk'=>'1171024202269002','jumlah_keluarga'=>3,'alamat'=>'Gampong Lancok, Kec. Syamtalira Bayu','kabupaten'=>'Aceh Utara','kecamatan'=>'Syamtalira Bayu','desa'=>'Lancok','titik_koordinat'=>'5.123412,97.197381','kategori_kerentanan'=>'lansia','penghasilan'=>0,'tingkat_penghasilan'=>'tidak_ada','status_kelayakan'=>'layak'],
            ['nama'=>'Zainab','nik'=>'1171034302859003','no_kk'=>'1171034302269003','jumlah_keluarga'=>4,'alamat'=>'Gampong Rantau Panjang, Kec. Rantau Selamat','kabupaten'=>'Aceh Timur','kecamatan'=>'Rantau Selamat','desa'=>'Rantau Panjang','titik_koordinat'=>'4.936819,97.672104','kategori_kerentanan'=>'yatim_piatu','penghasilan'=>650000,'tingkat_penghasilan'=>'rendah','status_kelayakan'=>'layak'],
            ['nama'=>'Mahmud Hasan','nik'=>'1171040401689004','no_kk'=>'1171040401269004','jumlah_keluarga'=>6,'alamat'=>'Kampung Jawa Lama, Banda Sakti','kabupaten'=>'Lhokseumawe','kecamatan'=>'Banda Sakti','desa'=>'Kampung Jawa Lama','titik_koordinat'=>'5.180992,97.146132','kategori_kerentanan'=>'korban_bencana','penghasilan'=>1200000,'tingkat_penghasilan'=>'rendah','status_kelayakan'=>'layak'],
            ['nama'=>'Salmiah','nik'=>'1171054501809005','no_kk'=>'1171054501269005','jumlah_keluarga'=>2,'alamat'=>'Gampong Meunasah Reuleut, Kec. Kota Juang','kabupaten'=>'Bireuen','kecamatan'=>'Kota Juang','desa'=>'Meunasah Reuleut','titik_koordinat'=>'5.205617,96.702148','kategori_kerentanan'=>'lansia','penghasilan'=>400000,'tingkat_penghasilan'=>'rendah','status_kelayakan'=>'layak'],
            ['nama'=>'Jamaliah','nik'=>'1171064602909006','no_kk'=>'1171064602269006','jumlah_keluarga'=>5,'alamat'=>'Kampung Kebayakan, Kec. Kebayakan','kabupaten'=>'Aceh Tengah','kecamatan'=>'Kebayakan','desa'=>'Kebayakan','titik_koordinat'=>'4.632371,96.861553','kategori_kerentanan'=>'keluarga_miskin','penghasilan'=>800000,'tingkat_penghasilan'=>'rendah','status_kelayakan'=>'perlu_verifikasi'],
            ['nama'=>'Syamsuddin','nik'=>'1171070701759007','no_kk'=>'1171070701269007','jumlah_keluarga'=>7,'alamat'=>'Desa Kuta Buluh, Kec. Lawe Bulan','kabupaten'=>'Aceh Tenggara','kecamatan'=>'Lawe Bulan','desa'=>'Kuta Buluh','titik_koordinat'=>'3.482730,97.804220','kategori_kerentanan'=>'korban_bencana','penghasilan'=>1000000,'tingkat_penghasilan'=>'rendah','status_kelayakan'=>'layak'],
            ['nama'=>'Maryam','nik'=>'1171084801889008','no_kk'=>'1171084801269008','jumlah_keluarga'=>4,'alamat'=>'Gampong Alue Dua, Kec. Langsa Baro','kabupaten'=>'Langsa','kecamatan'=>'Langsa Baro','desa'=>'Alue Dua','titik_koordinat'=>'4.465237,97.968239','kategori_kerentanan'=>'keluarga_miskin','penghasilan'=>1100000,'tingkat_penghasilan'=>'rendah','status_kelayakan'=>'perlu_verifikasi'],
            ['nama'=>'Ridwan','nik'=>'1171090901839009','no_kk'=>'1171090901269009','jumlah_keluarga'=>3,'alamat'=>'Gampong Ujong Kalak, Kec. Johan Pahlawan','kabupaten'=>'Aceh Barat','kecamatan'=>'Johan Pahlawan','desa'=>'Ujong Kalak','titik_koordinat'=>'4.143622,96.124881','kategori_kerentanan'=>'korban_bencana','penghasilan'=>950000,'tingkat_penghasilan'=>'rendah','status_kelayakan'=>'layak'],
            ['nama'=>'Fatimah Zahra','nik'=>'1171105001999010','no_kk'=>'1171105002269010','jumlah_keluarga'=>2,'alamat'=>'Gampong Cot Girek, Kec. Cot Girek','kabupaten'=>'Aceh Utara','kecamatan'=>'Cot Girek','desa'=>'Cot Girek','titik_koordinat'=>'4.998192,97.291131','kategori_kerentanan'=>'yatim_piatu','penghasilan'=>0,'tingkat_penghasilan'=>'tidak_ada','status_kelayakan'=>'perlu_verifikasi'],
        ];
        foreach ($penerima as $p) {
            DB::table('penerimas')->updateOrInsert(['nik'=>$p['nik']], $p + [
                'tempat_lahir'=>'Aceh','tanggal_lahir'=>'1980-01-01','jenis_kelamin'=>'L','provinsi'=>'Aceh',
                'rt_rw'=>'001/001','phone'=>'08000000'.substr($p['nik'], -2),'pekerjaan'=>'Pekerja Harian',
                'sumber_data'=>'relawan','status'=>$p['status_kelayakan']==='layak'?'terverifikasi':'pending',
                'kelompok_id'=>$kelompokId,'created_at'=>$now,'updated_at'=>$now,
            ]);
        }
    }
}
