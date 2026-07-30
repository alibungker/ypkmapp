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

    public function verifikasi(Request $request)
    {
        $query = Penerima::with('kelompok');

        // Search by NIK or Nama
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('nik', 'like', "%{$request->search}%");
            });
        }

        // Data PENDING (butuh verifikasi)
        $pending = clone $query;
        $this->scopeWilayah($pending);
        $pending = $pending->where('status', 'pending')->orderBy('created_at', 'desc')->get();

        // Data TERVERIFIKASI (butuh checklist terima bantuan)
        $terverifikasi = clone $query;
        $this->scopeWilayah($terverifikasi);
        $terverifikasi = $terverifikasi->where('status', 'terverifikasi')->orderBy('verified_at', 'desc')->get();

        return view('relawan.verifikasi', compact('pending', 'terverifikasi'));
    }
}
