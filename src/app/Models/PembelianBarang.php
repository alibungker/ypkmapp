<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianBarang extends Model
{
    protected $table = 'pembelian_barang';
    protected $fillable = ['nama_barang', 'batch', 'qty_rencana', 'qty_terbeli', 'qty_belum',
        'harga_satuan', 'anggaran', 'realisasi', 'sisa', 'persen_real'];
}
