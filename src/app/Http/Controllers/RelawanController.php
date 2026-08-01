<?php
namespace App\Http\Controllers;

use App\Models\Penerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kabupaten')) {
            $query->where('kabupaten', $request->kabupaten);
        }
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }
        if ($request->filled('desa')) {
            $query->where('desa', $request->desa);
        }
        if ($request->filled('kelompok_id')) {
            $query->where('kelompok_id', $request->integer('kelompok_id'));
        }
        if ($request->filled('sumber_data')) {
            $query->where('sumber_data', $request->sumber_data);
        }

        $perPage = (int) $request->query('per_page', 30);
        $perPage = in_array($perPage, [30, 50, 100]) ? $perPage : 30;

        // Data PENDING (butuh verifikasi)
        $pending = clone $query;
        $this->scopeWilayah($pending);
        $pending = $pending->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'pending_page')
            ->withQueryString();

        // Data TERVERIFIKASI (butuh checklist terima bantuan)
        $terverifikasi = clone $query;
        $this->scopeWilayah($terverifikasi);
        $terverifikasi = $terverifikasi->where('status', 'terverifikasi')
            ->where('terima_bantuan', false)
            ->orderBy('verified_at', 'desc')
            ->paginate($perPage, ['*'], 'verified_page')
            ->withQueryString();

        // Filter options for relawan's wilayah
        $filterKabupaten = \Illuminate\Support\Facades\DB::table('penerimas')
            ->whereNotNull('kabupaten')
            ->where('kabupaten', '!=', '-')
            ->distinct()
            ->orderBy('kabupaten')
            ->pluck('kabupaten');

        $filterKecamatan = collect();
        $filterDesa = collect();
        if ($request->filled('kabupaten')) {
            $filterKecamatan = \Illuminate\Support\Facades\DB::table('penerimas')
                ->where('kabupaten', $request->kabupaten)
                ->whereNotNull('kecamatan')
                ->where('kecamatan', '!=', '-')
                ->distinct()
                ->orderBy('kecamatan')
                ->pluck('kecamatan');
        }
        if ($request->filled('kecamatan')) {
            $filterDesa = \Illuminate\Support\Facades\DB::table('penerimas')
                ->where('kabupaten', $request->kabupaten)
                ->where('kecamatan', $request->kecamatan)
                ->whereNotNull('desa')
                ->where('desa', '!=', '-')
                ->distinct()
                ->orderBy('desa')
                ->pluck('desa');
        }

        $filterKelompok = \App\Models\Kelompok::query();
        $user = auth()->user();
        if ($user->isKetuaKelompok()) {
            $filterKelompok->whereKey($user->kelompok_id);
        } elseif ($user->isRelawan()) {
            if ($user->wilayah_kabupaten) $filterKelompok->where('daerah', $user->wilayah_kabupaten);
            if ($user->wilayah_kecamatan) $filterKelompok->where('kecamatan', $user->wilayah_kecamatan);
            if ($user->wilayah_desa) $filterKelompok->where('desa', $user->wilayah_desa);
        }
        $filterKelompok = $filterKelompok->orderBy('nama')->get();

        return view('relawan.verifikasi', compact(
            'pending', 'terverifikasi', 'perPage',
            'filterKabupaten', 'filterKecamatan', 'filterDesa', 'filterKelompok',
            'request'
        ));
    }

    public function bulkVerify(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array|min:1|max:500',
            'ids.*' => 'integer|distinct|exists:penerimas,id',
            'status' => 'required|in:terverifikasi,ditolak',
        ]);

        $query = Penerima::query()
            ->whereIn('id', $data['ids'])
            ->where('status', 'pending');
        $this->scopeWilayah($query);

        if ($data['status'] === 'terverifikasi') {
            $query->whereNotNull('kelompok_id');
        }

        $processed = DB::transaction(fn () => $query->update([
            'status' => $data['status'],
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]));

        $skipped = count($data['ids']) - $processed;
        $label = $data['status'] === 'terverifikasi' ? 'disetujui' : 'ditolak';
        $message = "{$processed} penerima berhasil {$label}.";
        if ($skipped > 0) {
            $message .= " {$skipped} dilewati karena status, wilayah, atau kelompok tidak valid.";
        }

        return back()->with('success', $message);
    }

    public function bulkTerima(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array|min:1|max:500',
            'ids.*' => 'integer|distinct|exists:penerimas,id',
        ]);

        $query = Penerima::query()
            ->whereIn('id', $data['ids'])
            ->where('status', 'terverifikasi')
            ->where('terima_bantuan', false);
        $this->scopeWilayah($query);

        $processed = DB::transaction(fn () => $query->update([
            'terima_bantuan' => true,
            'terima_by' => auth()->id(),
            'terima_at' => now(),
        ]));

        $skipped = count($data['ids']) - $processed;
        $message = "{$processed} penerima ditandai menerima bantuan.";
        if ($skipped > 0) {
            $message .= " {$skipped} dilewati karena status atau wilayah tidak valid.";
        }

        return back()->with('success', $message);
    }
}
