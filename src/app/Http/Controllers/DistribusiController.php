<?php
namespace App\Http\Controllers;

use App\Models\Distribusi;
use App\Models\Kelompok;
use App\Models\BarangBantuan;
use App\Models\PembelianBarang;
use App\Models\BiayaOperasional;
use App\Models\Anggaran;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DistribusiController extends Controller
{
    private function bolehLihat(Distribusi $distribusi): bool
    {
        $user = auth()->user();
        if ($user->isAdmin()) return true;
        $distribusi->loadMissing('kelompok');
        if ($user->isKetuaKelompok()) {
            return (int) $user->kelompok_id === (int) $distribusi->kelompok_id;
        }
        $kelompok = $distribusi->kelompok;
        if (!$kelompok) return false;
        if ($user->wilayah_kabupaten && $kelompok->daerah !== $user->wilayah_kabupaten) return false;
        if ($user->wilayah_kecamatan && $kelompok->kecamatan !== $user->wilayah_kecamatan) return false;
        if ($user->wilayah_desa && $kelompok->desa !== $user->wilayah_desa) return false;
        return true;
    }

    public function index(Request $request)
    {
        $query = Distribusi::with([
            'kelompok' => fn ($q) => $q->withCount('penerima')->with('ketuaUser'),
            'creator',
            'pembelianBarang',
        ]);
        $user = auth()->user();
        if ($user->isKetuaKelompok()) {
            $query->where('kelompok_id', $user->kelompok_id);
        } elseif ($user->isRelawan()) {
            $query->whereHas('kelompok', function ($q) use ($user) {
                if ($user->wilayah_kabupaten) $q->where('daerah', $user->wilayah_kabupaten);
                if ($user->wilayah_kecamatan) $q->where('kecamatan', $user->wilayah_kecamatan);
                if ($user->wilayah_desa) $q->where('desa', $user->wilayah_desa);
            });
        }

        // Stats dashboard
        $stats = [
            'kelompok' => (clone $query)->distinct('kelompok_id')->count('kelompok_id'),
            'titik' => (clone $query)->whereNotNull('titik_koordinat')->count(),
            'paket' => (clone $query)->sum('jumlah_paket'),
            'anggaran' => (clone $query)->sum('estimasi_nilai_total'),
        ];

        if ($request->status) $query->where('status', $request->status);
        $distribusi = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();
        return view('distribusi.index', compact('distribusi', 'stats'));
    }

    public function create()
    {
        $kelompoks = Kelompok::withCount('penerima')->with('ketuaUser')->get();
        $barang = BarangBantuan::all();
        $kegiatans = Anggaran::with('barangItems')->whereHas('barangItems')->orderBy('nama_anggaran')->get();
        $pembelian = PembelianBarang::orderBy('nama_barang')->get()
            ->each(function ($item) {
                $untukKegiatan = (int) DB::table('kegiatan_barang')
                    ->where('pembelian_barang_id', $item->id)->sum('jumlah');
                $untukDistribusi = (int) DB::table('distribusi_pembelian_barang')
                    ->where('pembelian_barang_id', $item->id)->sum('jumlah');
                $item->stok_tersedia = max(0, $untukKegiatan - $untukDistribusi);
            });
        return view('distribusi.form', compact('kelompoks', 'barang', 'pembelian', 'kegiatans'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $legacyUpload = $data['bukti_file'] ?? null;
        $uploads = $data['lampiran'] ?? [];
        unset($data['bukti_file'], $data['lampiran'], $data['hapus_lampiran']);
        $newPaths = [];

        if ($legacyUpload) {
            $legacyPath = $legacyUpload->store('distribusi/bukti', 'public');
            $newPaths[] = $legacyPath;
            $data['bukti_file'] = $legacyPath;
        }
        $data['kode_distribusi'] = 'DST-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        $data['created_by'] = auth()->id();
        $pembelianDistribusi = $data['pembelian_barang'] ?? [];
        unset($data['pembelian_barang']);
        if ($request->input('kegiatan_id')) {
            $data = $this->isiRingkasanDariKegiatan($data, $request->input('kegiatan_id'), $pembelianDistribusi);
        }
        unset($data['kegiatan_id']);

        try {
            DB::transaction(function () use ($data, $legacyUpload, $uploads, &$newPaths, $pembelianDistribusi) {
                $distribusi = Distribusi::create($data);
                if ($legacyUpload && $distribusi->bukti_file) {
                    $this->recordLampiran($distribusi, $legacyUpload, $distribusi->bukti_file);
                }
                $this->storeLampiran($distribusi, $uploads, $newPaths);
                $this->syncStokDistribusi($distribusi, $pembelianDistribusi);
                $this->syncPenerima($distribusi);
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($newPaths);
            throw $e;
        }

        return redirect()->route('distribusi.index')->with('success', 'Distribusi tersimpan dan penerima terverifikasi telah dialokasikan.');
    }

    public function edit(Distribusi $distribusi)
    {
        $kelompoks = Kelompok::withCount('penerima')->with('ketuaUser')->get();
        $distribusi->load('lampiran', 'pembelianBarang');
        $pembelian = PembelianBarang::orderBy('nama_barang')->get()
            ->each(function ($item) use ($distribusi) {
                $untukKegiatan = (int) DB::table('kegiatan_barang')
                    ->where('pembelian_barang_id', $item->id)->sum('jumlah');
                $untukDistribusi = (int) DB::table('distribusi_pembelian_barang')
                    ->where('pembelian_barang_id', $item->id)
                    ->where('distribusi_id', '!=', $distribusi->id)
                    ->sum('jumlah');
                $item->stok_tersedia = max(0, $untukKegiatan - $untukDistribusi);
            });
        $barang = BarangBantuan::all();
        $kegiatans = Anggaran::with('barangItems')->whereHas('barangItems')->orderBy('nama_anggaran')->get();
        return view('distribusi.form', compact('distribusi', 'kelompoks', 'barang', 'pembelian', 'kegiatans'));
    }

    public function update(Request $request, Distribusi $distribusi)
    {
        $data = $this->validated($request);
        $legacyUpload = $data['bukti_file'] ?? null;
        $uploads = $data['lampiran'] ?? [];
        $deleteIds = $data['hapus_lampiran'] ?? [];
        unset($data['bukti_file'], $data['lampiran'], $data['hapus_lampiran']);
        $pembelianDistribusi = $data['pembelian_barang'] ?? [];
        unset($data['pembelian_barang']);
        if ($request->input('kegiatan_id')) {
            $data = $this->isiRingkasanDariKegiatan($data, $request->input('kegiatan_id'), $pembelianDistribusi);
        }
        unset($data['kegiatan_id']);

        $oldLegacyPath = $distribusi->bukti_file;
        $kelompokBerubah = (int) $distribusi->kelompok_id !== (int) $data['kelompok_id'];
        if ($kelompokBerubah && $distribusi->penerimaDistribusi()->where('status', 'diterima')->exists()) {
            return back()->withInput()->withErrors([
                'kelompok_id' => 'Kelompok tidak dapat diubah karena sudah ada tanda terima bantuan.',
            ]);
        }

        $newPaths = [];
        $pathsToDelete = [];
        try {
            DB::transaction(function () use ($distribusi, &$data, $legacyUpload, $oldLegacyPath, $uploads, $deleteIds, $kelompokBerubah, &$newPaths, &$pathsToDelete, $pembelianDistribusi) {
                if ($legacyUpload) {
                    $legacyPath = $legacyUpload->store('distribusi/bukti', 'public');
                    $newPaths[] = $legacyPath;
                    $data['bukti_file'] = $legacyPath;
                }

                $selected = $distribusi->lampiran()->whereIn('id', $deleteIds)->get();
                if ($legacyUpload && $oldLegacyPath) {
                    $selected = $selected->merge(
                        $distribusi->lampiran()->where('path', $oldLegacyPath)->get()
                    )->unique('id');
                }
                $pathsToDelete = $selected->pluck('path')->all();
                if (!$legacyUpload && $oldLegacyPath && $selected->contains('path', $oldLegacyPath)) {
                    $data['bukti_file'] = null;
                }
                if ($selected->isNotEmpty()) {
                    $distribusi->lampiran()->whereKey($selected->pluck('id'))->delete();
                }

                $distribusi->update($data);
                if ($legacyUpload && $distribusi->bukti_file) {
                    $this->recordLampiran($distribusi, $legacyUpload, $distribusi->bukti_file);
                }
                $this->storeLampiran($distribusi, $uploads, $newPaths);
                $this->syncStokDistribusi($distribusi, $pembelianDistribusi);
                if ($kelompokBerubah) {
                    $distribusi->penerimaDistribusi()->delete();
                    $this->syncPenerima($distribusi);
                }
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($newPaths);
            throw $e;
        }

        if ($legacyUpload && $oldLegacyPath) $pathsToDelete[] = $oldLegacyPath;
        Storage::disk('public')->delete(array_values(array_unique(array_filter($pathsToDelete))));
        return redirect()->route('distribusi.index')->with('success', 'Distribusi diupdate.');
    }

    public function destroy(Distribusi $distribusi)
    {
        $paths = $distribusi->lampiran()->pluck('path')->push($distribusi->bukti_file)
            ->filter()->unique()->values()->all();
        DB::transaction(function () use ($distribusi) {
            $distribusi->delete();
        });
        Storage::disk('public')->delete($paths);
        return redirect()->route('distribusi.index')->with('success', 'Distribusi dihapus.');
    }

    private function storeLampiran(Distribusi $distribusi, array $uploads, array &$storedPaths): void
    {
        foreach ($uploads as $upload) {
            $path = $upload->store('distribusi/lampiran', 'public');
            $storedPaths[] = $path;
            $this->recordLampiran($distribusi, $upload, $path);
        }
    }

    private function recordLampiran(Distribusi $distribusi, $upload, string $path): void
    {
        $mime = $upload->getMimeType();
        $distribusi->lampiran()->create([
            'path' => $path,
            'nama_asli' => $upload->getClientOriginalName(),
            'mime_type' => $mime,
            'ukuran' => $upload->getSize(),
            'jenis' => str_starts_with((string) $mime, 'image/') ? 'foto' : 'dokumen',
            'created_by' => auth()->id(),
        ]);
    }

    private function syncStokDistribusi(Distribusi $distribusi, array $alokasi): void
    {
        $sync = [];
        foreach ($alokasi as $baris) {
            $barang = PembelianBarang::lockForUpdate()->findOrFail($baris['pembelian_barang_id']);
            $untukKegiatan = (int) DB::table('kegiatan_barang')
                ->where('pembelian_barang_id', $barang->id)->sum('jumlah');
            $distribusiLain = (int) DB::table('distribusi_pembelian_barang')
                ->where('pembelian_barang_id', $barang->id)
                ->where('distribusi_id', '!=', $distribusi->id)
                ->sum('jumlah');
            $tersedia = max(0, $untukKegiatan - $distribusiLain);
            $jumlah = (int) $baris['jumlah'];
            if ($jumlah > $tersedia) {
                throw ValidationException::withMessages([
                    'pembelian_barang' => "Alokasi kegiatan {$barang->nama_barang} tersisa {$tersedia}.",
                ]);
            }
            $sync[$barang->id] = ['jumlah' => $jumlah];
        }
        $distribusi->pembelianBarang()->sync($sync);
    }

    private function syncPenerima(Distribusi $distribusi): void
    {
        $ids = Kelompok::findOrFail($distribusi->kelompok_id)
            ->penerima()
            ->where('status', 'terverifikasi')
            ->pluck('penerimas.id');

        foreach ($ids->chunk(500) as $chunk) {
            $distribusi->penerimaDistribusi()->createMany(
                $chunk->map(fn ($id) => [
                    'penerima_id' => $id,
                    'status' => 'terjadwal',
                    'tanda_terima' => false,
                ])->all()
            );
        }
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'titik_koordinat' => ['required', 'regex:/^-?\d{1,2}(?:\.\d+)?,\s*-?\d{1,3}(?:\.\d+)?$/'],
            'kelompok_id' => 'required|exists:kelompoks,id',
            'kegiatan_id' => 'nullable|exists:anggarans,id',
            'jumlah_paket' => 'required|integer|min:1|max:10000000',
            'sumber_dana' => 'nullable|string|max:255',
            'catatan' => 'nullable|string|max:5000',
            'bukti_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'lampiran' => 'nullable|array|max:10',
            'lampiran.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
            'hapus_lampiran' => 'nullable|array',
            'hapus_lampiran.*' => 'integer|distinct',
            'status' => 'required|in:direncanakan,berlangsung,selesai,dibatalkan',
            'pembelian_barang' => 'nullable|array',
            'pembelian_barang.*.pembelian_barang_id' => 'required_with:pembelian_barang|integer|distinct|exists:pembelian_barang,id',
            'pembelian_barang.*.jumlah' => 'required_with:pembelian_barang|integer|min:1',
        ], [
            'titik_koordinat.regex' => 'Format koordinat harus latitude,longitude, contoh: 4.2991424,97.8653578.',
            'lampiran.max' => 'Maksimal 10 file dapat diunggah dalam satu kali penyimpanan.',
            'lampiran.*.mimes' => 'Lampiran harus berupa JPG, JPEG, PNG, atau PDF.',
            'lampiran.*.max' => 'Ukuran setiap lampiran maksimal 5 MB.',
        ]);

        // Hindari NULL eksplisit pada kolom database yang mempunyai default.
        $data['estimasi_nilai_total'] = 0;
        $data['sumber_dana'] = $data['sumber_dana'] ?? '';

        return $data;
    }

    private function isiRingkasanDariKegiatan(array $data, $kegiatanId, array $alokasi): array
    {
        $kegiatan = Anggaran::with('barangItems')->find($kegiatanId);
        $barangTerkait = $kegiatan?->barangItems->pluck('id')->all() ?? [];
        $totalNilai = 0;
        $totalJumlah = 0;
        foreach ($alokasi as $b) {
            if (!in_array((int) $b['pembelian_barang_id'], $barangTerkait, true)) {
                throw ValidationException::withMessages([
                    'pembelian_barang' => 'Barang tidak termasuk dalam kegiatan terpilih.',
                ]);
            }
            $harga = (float) PembelianBarang::whereKey($b['pembelian_barang_id'])->value('harga_satuan');
            $totalNilai += $harga * (int) $b['jumlah'];
            $totalJumlah += (int) $b['jumlah'];
        }
        $data['estimasi_nilai_total'] = $totalNilai;
        $data['jenis_bantuan'] = ucwords(str_replace('_', ' ', $kegiatan->kategori ?: 'Lainnya'));
        $data['jumlah_paket'] = max(1, (int) ($data['jumlah_paket'] ?? $kegiatan->target_paket ?? $totalJumlah));
        return $data;
    }

    public function show(Distribusi $distribusi)
    {
        abort_unless($this->bolehLihat($distribusi), 403, 'Distribusi di luar wilayah atau penugasan Anda.');
        $distribusi->load([
            'kelompok' => fn ($q) => $q->withCount('penerima')->with('ketuaUser'),
            'creator', 'penerimaDistribusi.penerima', 'items.barang', 'biayaOperasional', 'lampiran', 'pembelianBarang'
        ]);
        return view('distribusi.show', compact('distribusi'));
    }

    public function terima(Distribusi $distribusi, $penerimaId)
    {
        abort_unless($this->bolehLihat($distribusi), 403, 'Distribusi di luar wilayah kerja Anda.');
        $updated = $distribusi->penerimaDistribusi()
            ->where('penerima_id', $penerimaId)
            ->update(['status' => 'diterima', 'tanda_terima' => true, 'received_by' => auth()->id(), 'received_at' => now()]);
        abort_if($updated === 0, 404, 'Penerima tidak terdaftar pada distribusi ini.');
        return back()->with('success', 'Tanda terima dicatat.');
    }

    public function selesai(Distribusi $distribusi)
    {
        abort_unless($this->bolehLihat($distribusi), 403, 'Distribusi di luar wilayah kerja Anda.');
        $distribusi->update(['status' => 'selesai']);
        return back()->with('success', 'Distribusi selesai.');
    }

    public function dataPeta()
    {
        $query = Distribusi::whereNotNull('titik_koordinat')
            ->with(['kelompok' => fn ($q) => $q->withCount('penerima')]);
        $user = auth()->user();
        if ($user->isKetuaKelompok()) {
            $query->where('kelompok_id', $user->kelompok_id);
        } elseif ($user->isRelawan()) {
            $query->whereHas('kelompok', function ($q) use ($user) {
                if ($user->wilayah_kabupaten) $q->where('daerah', $user->wilayah_kabupaten);
                if ($user->wilayah_kecamatan) $q->where('kecamatan', $user->wilayah_kecamatan);
                if ($user->wilayah_desa) $q->where('desa', $user->wilayah_desa);
            });
        }
        $distribusi = $query->get()
            ->map(function ($d) {
                $coord = explode(',', $d->titik_koordinat);
                return [
                    'name' => $d->nama_kegiatan,
                    'lat' => (float)($coord[0] ?? 0),
                    'lng' => (float)($coord[1] ?? 0),
                    'paket' => $d->jumlah_paket,
                    'nilai' => $d->estimasi_nilai_total,
                    'penerima' => $d->kelompok->penerima_count ?? 0,
                    'kelompok' => $d->kelompok->nama ?? '-',
                    'status' => $d->status,
                    'tgl' => is_object($d->tanggal) ? $d->tanggal->format('d M Y') : date('d M Y', strtotime($d->tanggal)),
                ];
            });
        return response()->json($distribusi);
    }
}
