<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianBarang extends Model
{
    protected $table = 'pembelian_barang';
    protected $fillable = [
        'nama_barang', 'kategori_barang_id', 'jenis_peruntukan', 'satuan',
        'batch', 'qty_rencana', 'qty_terbeli', 'qty_belum',
        'harga_satuan', 'anggaran', 'realisasi', 'sisa', 'persen_real',
        'tanggal_pembelian', 'supplier', 'nomor_invoice', 'metode_pembayaran',
        'bukti_pembelian', 'catatan', 'status',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'anggaran' => 'decimal:2',
        'realisasi' => 'decimal:2',
        'sisa' => 'decimal:2',
        'persen_real' => 'decimal:1',
        'tanggal_pembelian' => 'date',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id');
    }
}
