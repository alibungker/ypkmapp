<?php
namespace App\Http\Controllers;

use App\Models\BiayaOperasional;
use App\Models\AlbumKegiatan;
use App\Models\DanaDonatur;
use App\Models\Distribusi;
use App\Models\Kelompok;
use App\Models\PenerimaDistribusi;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    private function filteredDistribusi(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'nullable|in:direncanakan,berlangsung,selesai,dibatalkan',
            'kelompok_id' => 'nullable|integer|exists:kelompoks,id',
            'kabupaten' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'desa' => 'nullable|string|max:100',
        ]);

        return Distribusi::query()
            ->with([
                'kelompok' => fn ($q) => $q->withCount([
                    'penerima',
                    'penerima as penerima_terverifikasi_count' => fn ($p) => $p->where('status', 'terverifikasi'),
                    'penerima as penerima_menerima_count' => fn ($p) => $p->where('terima_bantuan', true),
                ])->with('ketuaUser'),
            ])
            ->withCount([
                'penerimaDistribusi as tanda_terima_count' => fn ($q) => $q->where('status', 'diterima'),
            ])
            ->when($request->tanggal_mulai, fn ($q, $value) => $q->whereDate('tanggal', '>=', $value))
            ->when($request->tanggal_selesai, fn ($q, $value) => $q->whereDate('tanggal', '<=', $value))
            ->when($request->status, fn ($q, $value) => $q->where('status', $value))
            ->when($request->kelompok_id, fn ($q, $value) => $q->where('kelompok_id', $value))
            ->when($request->kabupaten, fn ($q, $value) => $q->whereHas('kelompok', fn ($k) => $k->where('daerah', $value)))
            ->when($request->kecamatan, fn ($q, $value) => $q->whereHas('kelompok', fn ($k) => $k->where('kecamatan', $value)))
            ->when($request->desa, fn ($q, $value) => $q->whereHas('kelompok', fn ($k) => $k->where('desa', $value)))
            ->orderByDesc('tanggal');
    }

    private function data(Request $request): array
    {
        $distribusi = $this->filteredDistribusi($request)->get();
        $ids = $distribusi->pluck('id');

        $biayaOperasional = $ids->isEmpty()
            ? 0
            : (float) BiayaOperasional::whereIn('distribusi_id', $ids)->sum('jumlah');

        $danaQuery = DanaDonatur::query();
        if ($request->tanggal_mulai) $danaQuery->whereDate('tanggal_masuk', '>=', $request->tanggal_mulai);
        if ($request->tanggal_selesai) $danaQuery->whereDate('tanggal_masuk', '<=', $request->tanggal_selesai);
        $danaMasuk = (float) $danaQuery->sum('jumlah');
        $nilaiBantuan = (float) $distribusi->sum('estimasi_nilai_total');

        $perDaerah = $distribusi->groupBy(fn ($d) => optional($d->kelompok)->daerah ?: 'Tanpa wilayah')
            ->map(function ($rows, $daerah) {
                return [
                    'daerah' => $daerah,
                    'kegiatan' => $rows->count(),
                    'paket' => $rows->sum('jumlah_paket'),
                    'nilai' => $rows->sum('estimasi_nilai_total'),
                    'penerima' => $rows->sum(fn ($d) => optional($d->kelompok)->penerima_count ?? 0),
                    'terverifikasi' => $rows->sum(fn ($d) => optional($d->kelompok)->penerima_terverifikasi_count ?? 0),
                    'menerima' => $rows->sum(fn ($d) => $d->tanda_terima_count ?: (optional($d->kelompok)->penerima_menerima_count ?? 0)),
                ];
            })->sortByDesc('paket')->values();

        return [
            'distribusi' => $distribusi,
            'perDaerah' => $perDaerah,
            'kelompoks' => Kelompok::orderBy('nama')->get(),
            'totals' => [
                'dana_masuk' => $danaMasuk,
                'nilai_bantuan' => $nilaiBantuan,
                'biaya_operasional' => $biayaOperasional,
                'sisa_dana' => $danaMasuk - $nilaiBantuan - $biayaOperasional,
                'paket' => $distribusi->sum('jumlah_paket'),
                'penerima' => $perDaerah->sum('penerima'),
                'terverifikasi' => $perDaerah->sum('terverifikasi'),
                'menerima' => $perDaerah->sum('menerima'),
            ],
        ];
    }

    public function index(Request $request)
    {
        return view('laporan.index', $this->data($request));
    }

    public function exportCsv(Request $request)
    {
        $data = $this->data($request);
        $filename = 'laporan-peduli-ypkm-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Tanggal', 'Kode', 'Kegiatan', 'Kabupaten/Kota', 'Kecamatan', 'Desa', 'Kelompok', 'Status', 'Paket', 'Nilai Bantuan', 'Target Penerima', 'Terverifikasi', 'Tanda Terima'], ';');
            foreach ($data['distribusi'] as $d) {
                fputcsv($out, [
                    optional($d->tanggal)->format('Y-m-d') ?: $d->tanggal,
                    $d->kode_distribusi,
                    $d->nama_kegiatan,
                    optional($d->kelompok)->daerah,
                    optional($d->kelompok)->kecamatan,
                    optional($d->kelompok)->desa,
                    optional($d->kelompok)->nama,
                    $d->status,
                    $d->jumlah_paket,
                    $d->estimasi_nilai_total,
                    optional($d->kelompok)->penerima_count ?? 0,
                    optional($d->kelompok)->penerima_terverifikasi_count ?? 0,
                    $d->tanda_terima_count ?: (optional($d->kelompok)->penerima_menerima_count ?? 0),
                ], ';');
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Cetak detail satu distribusi (PDF via window.print).
     */
    public function printDetail(Distribusi $distribusi)
    {
        $distribusi->load([
            'kelompok' => fn ($q) => $q->withCount([
                'penerima',
                'penerima as penerima_terverifikasi_count' => fn ($p) => $p->where('status', 'terverifikasi'),
                'penerima as penerima_menerima_count' => fn ($p) => $p->where('terima_bantuan', true),
            ])->with('ketuaUser'),
            'items.barang',
            'biayaOperasional',
            'anggaran',
            'creator',
            'pembelianBarang', // detail bantuan dari pivot distribusi_pembelian_barang
        ]);

        $distribusi->loadCount([
            'penerimaDistribusi as tanda_terima_count' => fn ($q) => $q->where('status', 'diterima'),
        ]);

        $penerimaList = PenerimaDistribusi::where('distribusi_id', $distribusi->id)
            ->with('penerima')
            ->orderBy('status')
            ->orderBy('penerima_id')
            ->get();

        $totalTerverifikasi = $penerimaList->filter(fn ($p) => optional($p->penerima)->status === 'terverifikasi')->count();
        $totalTerima = $penerimaList->filter(fn ($p) => $p->status === 'diterima')->count();

        // Koordinat peta: "lat,lng" -> [lat, lng]; null bila tidak valid
        $koordinat = null;
        if ($distribusi->titik_koordinat && str_contains($distribusi->titik_koordinat, ',')) {
            $parts = array_map('trim', explode(',', $distribusi->titik_koordinat));
            if (count($parts) >= 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                $koordinat = ['lat' => (float) $parts[0], 'lng' => (float) $parts[1]];
            }
        }

        // Media (maks 4 foto) dari album kegiatan yang terhubung ke distribusi ini
        $media = AlbumKegiatan::where('distribusi_id', $distribusi->id)
            ->with('photos')
            ->get()
            ->flatMap(fn ($album) => $album->photos)
            ->take(4);

        return view('laporan.print', compact('distribusi', 'penerimaList', 'totalTerverifikasi', 'totalTerima', 'koordinat', 'media'));
    }
}