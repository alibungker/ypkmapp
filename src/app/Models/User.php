<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'kelompok_id', 'phone', 'foto', 'is_active',
        'wilayah_kabupaten', 'wilayah_kecamatan', 'wilayah_desa',
        'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat_lengkap'];
    protected $hidden = ['password', 'remember_token'];

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isRelawan(): bool { return $this->role === 'relawan'; }
    public function isKetuaKelompok(): bool { return $this->role === 'ketua_kelompok'; }
    public function kelompok() { return $this->belongsTo(Kelompok::class); }

    // Label wilayah kerja user (utk tampilan)
    public function wilayahLabel(): string
    {
        if ($this->wilayah_desa) return "Desa {$this->wilayah_desa}, Kec. {$this->wilayah_kecamatan}, {$this->wilayah_kabupaten}";
        if ($this->wilayah_kecamatan) return "Kec. {$this->wilayah_kecamatan}, {$this->wilayah_kabupaten}";
        if ($this->wilayah_kabupaten) return $this->wilayah_kabupaten;
        return 'Semua Wilayah';
    }

    // Scope query penerima sesuai kunci wilayah user
    public function scopePenerima($query)
    {
        if ($this->isAdmin()) return $query;
        if ($this->wilayah_kabupaten) $query->where('kabupaten', $this->wilayah_kabupaten);
        if ($this->wilayah_kecamatan) $query->where('kecamatan', $this->wilayah_kecamatan);
        if ($this->wilayah_desa) $query->where('desa', $this->wilayah_desa);
        return $query;
    }
}
