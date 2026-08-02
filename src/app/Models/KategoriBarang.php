<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    protected $table = 'kategori_barangs';
    protected $fillable = ['kode', 'nama', 'jenis_default', 'aktif', 'urutan'];

    protected $casts = ['aktif' => 'boolean'];

    public function pembelian()
    {
        return $this->hasMany(PembelianBarang::class, 'kategori_barang_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true)->orderBy('urutan');
    }
}
