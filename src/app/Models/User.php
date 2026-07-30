<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'foto', 'is_active'];
    protected $hidden = ['password', 'remember_token'];

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isRelawan(): bool { return $this->role === 'relawan'; }
    public function isKetuaKelompok(): bool { return $this->role === 'ketua_kelompok'; }

    public function relawan() { return $this->hasOne(Relawan::class); }
    public function verifikasiPenerima() { return $this->hasMany(Penerima::class, 'verified_by'); }
}
