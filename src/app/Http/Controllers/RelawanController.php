<?php
namespace App\Http\Controllers;

use App\Models\Penerima;
use Illuminate\Http\Request;

class RelawanController extends Controller
{
    private function scopeWilayah($query)
    {
        $u = auth()->user();
        if ($u->wilayah_kabupaten) $query->where('kabupaten', $u->wilayah_kabupaten);
        if ($u->wilayah_kecamatan) $query->where('kecamatan', $u->wilayah_kecamatan);
        if ($u->wilayah_desa) $query->where('desa', $u->wilayah_desa);
        return $query;
    }

    public function verifikasi()
    {
        // Data PENDING (butuh verifikasi)
        $pending = clone $query = Penerima::with('kelompok')->where('status', 'pending');
        $this->scopeWilayah($pending);
        $pending = $pending->orderBy('created_at', 'desc')->get();

        // Data TERVERIFIKASI (butuh checklist terima bantuan)
        $terverifikasi = clone $query = Penerima::with('kelompok', 'verifikator')->where('status', 'terverifikasi');
        $this->scopeWilayah($terverifikasi);
        $terverifikasi = $terverifikasi->orderBy('verified_at', 'desc')->get();

        return view('relawan.verifikasi', compact('pending', 'terverifikasi'));
    }
}
