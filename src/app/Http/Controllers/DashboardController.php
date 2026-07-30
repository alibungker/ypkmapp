<?php
namespace App\Http\Controllers;

use App\Models\Penerima;
use App\Models\Kelompok;
use App\Models\Distribusi;
use App\Models\DanaDonatur;
use App\Models\BiayaOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'penerima' => Penerima::count(),
            'penerima_terverifikasi' => Penerima::where('status', 'terverifikasi')->count(),
            'penerima_pending' => Penerima::where('status', 'pending')->count(),
            'kelompok' => Kelompok::count(),
            'distribusi' => Distribusi::count(),
            'distribusi_selesai' => Distribusi::where('status', 'selesai')->count(),
            'distribusi_berlangsung' => Distribusi::where('status', 'berlangsung')->count(),
            'total_dana_masuk' => DanaDonatur::sum('jumlah'),
            'total_biaya' => BiayaOperasional::sum('jumlah'),
            'total_nilai_bantuan' => Distribusi::sum('estimasi_nilai_total'),
        ];

        $distribusi_terbaru = Distribusi::with('kelompok')
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact('stats', 'distribusi_terbaru'));
    }
}
