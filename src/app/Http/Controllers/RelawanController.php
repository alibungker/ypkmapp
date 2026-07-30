<?php
namespace App\Http\Controllers;

use App\Models\Penerima;
use Illuminate\Http\Request;

class RelawanController extends Controller
{
    // Filter by wilayah lock
    private function scopeWilayah($query)
    {
        $u = auth()->user();
        if ($u->wilayah_kabupaten) $query->where('kabupaten', $u->wilayah_kabupaten);
        if ($u->wilayah_kecamatan) $query->where('kecamatan', $u->wilayah_kecamatan);
        if ($u->wilayah_desa) $query->where('desa', $u->wilayah_desa);
        return $query;
    }

    // 🔍 Verifikasi: daftar penerima PENDING yang diajukan ketua kelompok
    public function verifikasi()
    {
        $query = Penerima::with('kelompok')->where('status', 'pending');
        $this->scopeWilayah($query);
        $penerima = $query->orderBy('created_at', 'desc')->get();
        return view('relawan.verifikasi', compact('penerima'));
    }

    // ✅ Validasi: daftar penerima TERVERIFIKASI yang butuh checklist terima bantuan
    public function validasi()
    {
        $query = Penerima::with('kelompok', 'verifikator')->where('status', 'terverifikasi');
        $this->scopeWilayah($query);
        $penerima = $query->orderBy('verified_at', 'desc')->get();
        return view('relawan.validasi', compact('penerima'));
    }
}
