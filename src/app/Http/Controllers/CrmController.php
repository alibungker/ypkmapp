<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrmController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'pengurus');
        $pengurus = User::whereNotNull('jabatan')->orderBy('jabatan')->get();
        $mitra = DB::table('mitra')->orderByDesc('total_kontribusi')->get();
        $relawan = DB::table('relawans')->whereNotNull('nama_lengkap')->orderBy('nama_lengkap')->get();
        $penerima = DB::table('penerimas')->whereNotNull('kategori_kerentanan')->orderBy('nama')->get();

        $stats = [
            'pengurus' => $pengurus->count(),
            'mitra' => $mitra->count(),
            'relawan' => $relawan->count(),
            'penerima' => $penerima->count(),
            'kontribusi' => $mitra->sum('total_kontribusi'),
            'jam_relawan' => $relawan->sum('jam_kontribusi'),
        ];

        return view('crm.index', compact('tab', 'pengurus', 'mitra', 'relawan', 'penerima', 'stats'));
    }
}
