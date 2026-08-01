<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerima extends Model
{
    protected $fillable = [
        'nik', 'no_kk', 'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
        'alamat', 'provinsi', 'kabupaten', 'kecamatan', 'desa', 'rt_rw',
        'phone', 'jumlah_keluarga', 'pekerjaan', 'penghasilan', 'titik_koordinat',
        'foto_ktp', 'foto_kk', 'foto_rumah', 'sumber_data', 'status',
        'catatan_verifikasi', 'verified_by', 'verified_at', 'kelompok_id',
        'terima_bantuan', 'terima_by', 'terima_at'
    ];

    public function kelompok() { return $this->belongsTo(Kelompok::class); }
    public function verifikator() { return $this->belongsTo(User::class, 'verified_by'); }
    public function penerimaTerima() { return $this->belongsTo(User::class, 'terima_by'); }
    public function penerimaDistribusi() { return $this->hasMany(PenerimaDistribusi::class); }
}

class Kelompok extends Model
{
    protected $fillable = ['nama', 'kode', 'daerah', 'kecamatan', 'desa', 'ketua_id', 'jumlah_anggota', 'description'];

    // Ketua operasional bersumber dari akun User ber-role ketua_kelompok.
    // Relasi legacy ketua_id (ke penerima) dipertahankan sementara untuk kompatibilitas data lama.
    public function ketua() { return $this->belongsTo(Penerima::class, 'ketua_id'); }
    public function ketuaUser() { return $this->hasOne(User::class, 'kelompok_id')->where('role', 'ketua_kelompok'); }
    public function penerima() { return $this->hasMany(Penerima::class); }
    public function distribusi() { return $this->hasMany(Distribusi::class); }
}

class Distribusi extends Model
{
    protected $fillable = [
        'kode_distribusi', 'nama_kegiatan', 'tanggal', 'lokasi', 'titik_koordinat',
        'kelompok_id', 'jenis_bantuan', 'jumlah_paket', 'estimasi_nilai_total',
        'sumber_dana', 'catatan', 'bukti_file', 'status', 'created_by'
    ];

    public function kelompok() { return $this->belongsTo(Kelompok::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function penerimaDistribusi() { return $this->hasMany(PenerimaDistribusi::class); }
    public function items() { return $this->hasMany(DistribusiItem::class); }
    public function biayaOperasional() { return $this->hasMany(BiayaOperasional::class); }
    public function lampiran() { return $this->hasMany(DistribusiLampiran::class); }
    public function pembelianBarang() { return $this->belongsToMany(PembelianBarang::class, 'distribusi_pembelian_barang')->withPivot('jumlah')->withTimestamps(); }
}

class DistribusiLampiran extends Model
{
    protected $table = 'distribusi_lampirans';
    protected $fillable = ['path', 'nama_asli', 'mime_type', 'ukuran', 'jenis', 'created_by'];

    public function distribusi() { return $this->belongsTo(Distribusi::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}

class BarangBantuan extends Model
{
    protected $table = 'barang_bantuans';
    protected $fillable = ['nama', 'kategori', 'satuan', 'harga_perkiraan', 'deskripsi'];
    public function stokBarang() { return $this->hasMany(StokBarang::class); }
}

class DanaDonatur extends Model
{
    protected $table = 'dana_donaturs';
    protected $fillable = ['donatur', 'tanggal_masuk', 'jumlah', 'jenis', 'keterangan', 'bukti_transfer', 'dicatat_oleh'];
    public function pencatat() { return $this->belongsTo(User::class, 'dicatat_oleh'); }
}

class BiayaOperasional extends Model
{
    protected $table = 'biaya_operasionals';
    protected $fillable = ['distribusi_id', 'kategori', 'deskripsi', 'jumlah', 'tanggal', 'bukti_foto', 'dicatat_oleh'];
    public function distribusi() { return $this->belongsTo(Distribusi::class); }
    public function pencatat() { return $this->belongsTo(User::class, 'dicatat_oleh'); }
}

class PenerimaDistribusi extends Model
{
    protected $table = 'penerima_distribusi';
    protected $fillable = ['penerima_id', 'distribusi_id', 'status', 'tanda_terima', 'foto_bukti', 'catatan', 'received_by', 'received_at'];
    public function penerima() { return $this->belongsTo(Penerima::class); }
    public function distribusi() { return $this->belongsTo(Distribusi::class); }
}

class Relawan extends Model
{
    protected $fillable = ['user_id', 'daerah_tugas', 'keahlian', 'status'];
    public function user() { return $this->belongsTo(User::class); }
}

class StokBarang extends Model
{
    protected $table = 'stok_barangs';
    protected $fillable = ['barang_id', 'jumlah', 'sumber', 'nilai_total', 'tanggal_masuk', 'tanggal_kadaluarsa', 'catatan'];
    public function barang() { return $this->belongsTo(BarangBantuan::class); }
}

class DistribusiItem extends Model
{
    protected $table = 'distribusi_items';
    protected $fillable = ['distribusi_id', 'barang_id', 'jumlah_per_paket', 'jumlah_paket_distribusi', 'subtotal_nilai'];
    public function distribusi() { return $this->belongsTo(Distribusi::class); }
    public function barang() { return $this->belongsTo(BarangBantuan::class); }
}

class Anggaran extends Model
{
    protected $fillable = ['distribusi_id', 'nama_anggaran', 'kategori', 'target_paket', 'satuan', 'anggaran', 'realisasi', 'catatan'];
    public function distribusi() { return $this->belongsTo(Distribusi::class); }
}

class Log extends Model
{
    protected $fillable = ['user_id', 'action', 'description', 'ip_address', 'user_agent'];
    public function user() { return $this->belongsTo(User::class); }
}
