<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name','email','password','role','saldo_topup','kelompok_id','phone','foto','is_active','wilayah_kabupaten','wilayah_kecamatan','wilayah_desa','nik','nip','jabatan','status_aktif','tempat_lahir','tanggal_lahir','jenis_kelamin','alamat_lengkap','kode_keanggotaan','nama_bank','nomor_rekening','atas_nama_rekening'];
    protected $hidden = ['password','remember_token'];

    protected static function booted(): void
    {
        static::created(function (self $u) {
            if (!$u->kode_keanggotaan) {
                $u->kode_keanggotaan = $u->generateKodeKeanggotaan();
                $u->saveQuietly();
            }
        });
    }

    public function generateKodeKeanggotaan(): string
    {
        $prefix = match ($this->role) {
            'super_admin', 'pengurus' => 'PGR',
            'bendahara', 'staff', 'staff_keuangan' => 'STF',
            default => 'RLW',
        };
        $yy = date('y');
        $digits = $prefix === 'RLW' ? 4 : 3;
        $base = "YPKM-{$prefix}-{$yy}-";
        $last = static::where('kode_keanggotaan', 'like', $base.'%')
            ->orderByDesc('kode_keanggotaan')->value('kode_keanggotaan');
        $next = $last ? ((int) substr($last, -$digits)) + 1 : 1;
        return $base.str_pad((string) $next, $digits, '0', STR_PAD_LEFT);
    }

    public static function roleFromJabatan(?string $jabatan): string
    {
        $jabatan = strtolower(trim((string) $jabatan));

        if (str_contains($jabatan, 'bendahara')) return 'bendahara';
        if (str_contains($jabatan, 'keuangan')) return 'staff_keuangan';
        if (str_contains($jabatan, 'staf') || str_contains($jabatan, 'staff')) return 'staff';

        return 'pengurus';
    }

    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isAdmin(): bool { return in_array($this->role, ['super_admin','pengurus']); }
    public function isBendahara(): bool { return $this->role === 'bendahara'; }
    public function isStaffKeuangan(): bool { return $this->role === 'staff_keuangan'; }
    public function isStaff(): bool { return in_array($this->role, ['staff','staff_keuangan']); }
    public function isRelawan(): bool { return $this->role === 'relawan'; }
    public function isKetuaKelompok(): bool { return $this->role === 'ketua_kelompok'; }
    public function isPengurus(): bool { return in_array($this->role, ['super_admin','pengurus']); }
    public function canTopUp(): bool { return in_array($this->role, ['super_admin','pengurus','bendahara']); }
    public function canApproveTopUp(): bool { return in_array($this->role, ['super_admin','pengurus']); }
    public function canManageUser(): bool { return $this->role === 'super_admin'; }
    public function canFinalizeLaporan(): bool { return in_array($this->role, ['super_admin','pengurus']); }
    public function canCreateLaporan(): bool { return in_array($this->role, ['super_admin','pengurus','bendahara','staff_keuangan']); }
    public function canViewKeuangan(): bool { return in_array($this->role, ['super_admin','pengurus','bendahara','staff_keuangan']); }
    public function canEditKeuangan(): bool { return in_array($this->role, ['super_admin','pengurus','bendahara']); }
    public function canDeleteKeuangan(): bool { return $this->role === 'super_admin'; }

    public function hasPermission(string $permission): bool
    {
        return match ($permission) {
            'anggaran.topup' => $this->canTopUp(),
            'anggaran.topup.approve' => $this->canApproveTopUp(),
            'user.manage' => $this->canManageUser(),
            'laporan.finalize' => $this->canFinalizeLaporan(),
            'laporan.create' => $this->canCreateLaporan(),
            'keuangan.view' => $this->canViewKeuangan(),
            'keuangan.edit' => $this->canEditKeuangan(),
            'keuangan.delete' => $this->canDeleteKeuangan(),
            'profile.update-own' => true,
            default => false,
        };
    }

    public function kelompok() { return $this->belongsTo(Kelompok::class); }

    public function wilayahLabel(): string
    {
        if ($this->wilayah_desa) return "Desa {$this->wilayah_desa}, Kec. {$this->wilayah_kecamatan}, {$this->wilayah_kabupaten}";
        if ($this->wilayah_kecamatan) return "Kec. {$this->wilayah_kecamatan}, {$this->wilayah_kabupaten}";
        return $this->wilayah_kabupaten ?: 'Semua Wilayah';
    }

    public function scopePenerima($query)
    {
        if ($this->isAdmin()) return $query;
        if ($this->isKetuaKelompok()) return $this->kelompok_id ? $query->where('kelompok_id', $this->kelompok_id) : $query->whereRaw('1 = 0');
        if ($this->wilayah_kabupaten) $query->where('kabupaten', $this->wilayah_kabupaten);
        if ($this->wilayah_kecamatan) $query->where('kecamatan', $this->wilayah_kecamatan);
        if ($this->wilayah_desa) $query->where('desa', $this->wilayah_desa);
        return $query;
    }
}
