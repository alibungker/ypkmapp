<?php
namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\KategoriBarang;
use App\Models\PembelianBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $anggarans = Anggaran::orderBy('id')->get();
        $kategoriBarangs = KategoriBarang::aktif()->get();

        $pembelianQuery = PembelianBarang::with('kategori')->orderBy('id');
        if ($request->filled('kategori')) {
            $pembelianQuery->where('kategori_barang_id', $request->integer('kategori'));
        }
        if ($request->filled('jenis')) {
            $pembelianQuery->where('jenis_peruntukan', $request->string('jenis'));
        }
        if ($request->filled('status')) {
            $pembelianQuery->where('status', $request->string('status'));
        }
        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            $pembelianQuery->where(function ($query) use ($search) {
                $query->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('supplier', 'like', "%{$search}%")
                    ->orWhere('nomor_invoice', 'like', "%{$search}%")
                    ->orWhere('batch', 'like', "%{$search}%");
            });
        }
        $pembelian = $pembelianQuery->get();
        $keKegiatan = DB::table('kegiatan_barang')
            ->selectRaw('pembelian_barang_id, SUM(jumlah) as total')
            ->groupBy('pembelian_barang_id')->pluck('total', 'pembelian_barang_id');
        $keDistribusi = DB::table('distribusi_pembelian_barang')
            ->selectRaw('pembelian_barang_id, SUM(jumlah) as total')
            ->groupBy('pembelian_barang_id')->pluck('total', 'pembelian_barang_id');
        $pembelian->each(function ($item) use ($keKegiatan, $keDistribusi) {
            $item->qty_kegiatan = (int) ($keKegiatan[$item->id] ?? 0);
            $item->qty_distribusi = (int) ($keDistribusi[$item->id] ?? 0);
            // Distribusi diambil dari alokasi kegiatan.
            $item->sisa_kegiatan = max(0, $item->qty_kegiatan - $item->qty_distribusi);
            $item->stok_bebas = max(0, $item->qty_terbeli - $item->qty_kegiatan);
            // Stok untuk dialokasikan ke kegiatan berasal dari stok bebas.
            // Barang baru yang belum pernah dialokasikan tetap harus tersedia.
            $item->stok_tersedia = $item->stok_bebas;
        });
        return view('barang.index', compact('anggarans', 'pembelian', 'keKegiatan', 'keDistribusi', 'kategoriBarangs'));
    }

    // === KEGIATAN ===
    public function storeKegiatan(Request $request)
    {
        $data = $request->validate([
            'nama_anggaran' => 'required',
            'kategori' => 'required',
            'target_paket' => 'nullable|integer',
            'satuan' => 'nullable',
            'anggaran' => 'required|numeric',
            'realisasi' => 'required|numeric',
            'catatan' => 'nullable',
            'barang' => 'nullable|array',
            'barang.*.pembelian_barang_id' => 'required_with:barang|integer|distinct|exists:pembelian_barang,id',
            'barang.*.jumlah' => 'required_with:barang|integer|min:1',
        ]);
        $alokasi = $data['barang'] ?? [];
        unset($data['barang']);

        // Nilai kegiatan wajib bersumber dari harga pembelian, bukan input browser.
        $totalOtomatis = collect($alokasi)->sum(function ($baris) {
            $harga = (float) PembelianBarang::whereKey($baris['pembelian_barang_id'])->value('harga_satuan');
            return $harga * (int) $baris['jumlah'];
        });
        $data['anggaran'] = $totalOtomatis;
        $data['realisasi'] = $totalOtomatis;

        DB::transaction(function () use ($data, $alokasi) {
            $kegiatan = Anggaran::create($data);
            foreach ($alokasi as $baris) {
                $barang = PembelianBarang::lockForUpdate()->findOrFail($baris['pembelian_barang_id']);
                $sudahKegiatan = (int) DB::table('kegiatan_barang')
                    ->where('pembelian_barang_id', $barang->id)->sum('jumlah');
                $stokTersedia = max(0, $barang->qty_terbeli - $sudahKegiatan);
                if ($baris['jumlah'] > $stokTersedia) {
                    throw ValidationException::withMessages([
                        'barang' => "Stok {$barang->nama_barang} hanya tersedia {$stokTersedia}.",
                    ]);
                }
                DB::table('kegiatan_barang')->insert([
                    'anggaran_id' => $kegiatan->id,
                    'pembelian_barang_id' => $barang->id,
                    'jumlah' => $baris['jumlah'],
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        });
        return redirect()->route('barang.index', ['tab' => 'kegiatan'])->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function updateKegiatan(Request $request, Anggaran $anggaran)
    {
        $data = $request->validate([
            'nama_anggaran' => 'required',
            'kategori' => 'required',
            'target_paket' => 'nullable|integer',
            'satuan' => 'nullable',
            'catatan' => 'nullable',
        ]);

        // Harga tetap bersumber dari pembelian dan alokasi barang kegiatan.
        $totalOtomatis = (float) DB::table('kegiatan_barang as kb')
            ->join('pembelian_barang as pb', 'pb.id', '=', 'kb.pembelian_barang_id')
            ->where('kb.anggaran_id', $anggaran->id)
            ->sum(DB::raw('kb.jumlah * pb.harga_satuan'));
        $data['anggaran'] = $totalOtomatis;
        $data['realisasi'] = $totalOtomatis;
        $anggaran->update($data);
        return redirect()->route('barang.index')->with('success', 'Kegiatan diupdate.');
    }

    public function destroyKegiatan(Anggaran $anggaran)
    {
        $anggaran->delete();
        return redirect()->route('barang.index')->with('success', 'Kegiatan dihapus.');
    }

    // === PEMBELIAN BARANG ===
    public function storePembelian(Request $request)
    {
        $data = $request->validate([
            'nama_barang' => 'required',
            'kategori_barang_id' => 'nullable|integer|exists:kategori_barangs,id',
            'jenis_peruntukan' => 'nullable|in:bantuan,operasional,aset',
            'satuan' => 'nullable|max:50',
            'batch' => 'nullable',
            'qty_rencana' => 'required|integer',
            'qty_terbeli' => 'nullable|integer',
            'harga_satuan' => 'required|numeric|min:0',
            'anggaran' => 'nullable|numeric|min:0',
            'realisasi' => 'nullable|numeric|min:0',
            'tanggal_pembelian' => 'nullable|date',
            'supplier' => 'nullable|max:255',
            'nomor_invoice' => 'nullable|max:255',
            'metode_pembayaran' => 'nullable|in:tunai,transfer,lainnya',
            'status' => 'nullable|in:rencana,dipesan,diterima,batal',
            'catatan' => 'nullable',
            'bukti_pembelian' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        $data['qty_terbeli'] = $data['qty_terbeli'] ?? 0;
        if (!empty($data['kategori_barang_id'])) {
            $default = KategoriBarang::whereKey($data['kategori_barang_id'])->value('jenis_default');
            if (!empty($default)) {
                $data['jenis_peruntukan'] = $data['jenis_peruntukan'] ?? $default;
            }
        }
        $data['status'] = $data['status'] ?? 'diterima';
        if ($request->hasFile('bukti_pembelian')) {
            $data['bukti_pembelian'] = $request->file('bukti_pembelian')->store('bukti-pembelian', 'public');
        }
        $data = $this->hitungPembelian($data);
        PembelianBarang::create($data);
        return redirect()->route('barang.index', ['tab' => 'pembelian'])->with('success', 'Item barang berhasil ditambahkan.');
    }

    public function updatePembelian(Request $request, PembelianBarang $pembelian)
    {
        $data = $request->validate([
            'nama_barang' => 'required',
            'kategori_barang_id' => 'nullable|integer|exists:kategori_barangs,id',
            'jenis_peruntukan' => 'nullable|in:bantuan,operasional,aset',
            'satuan' => 'nullable|max:50',
            'batch' => 'nullable',
            'qty_rencana' => 'required|integer',
            'qty_terbeli' => 'required|integer',
            'harga_satuan' => 'required|numeric|min:0',
            'anggaran' => 'nullable|numeric|min:0',
            'realisasi' => 'nullable|numeric|min:0',
            'tanggal_pembelian' => 'nullable|date',
            'supplier' => 'nullable|max:255',
            'nomor_invoice' => 'nullable|max:255',
            'metode_pembayaran' => 'nullable|in:tunai,transfer,lainnya',
            'status' => 'nullable|in:rencana,dipesan,diterima,batal',
            'catatan' => 'nullable',
            'bukti_pembelian' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        if (!empty($data['kategori_barang_id'])) {
            $default = KategoriBarang::whereKey($data['kategori_barang_id'])->value('jenis_default');
            if (!empty($default)) {
                $data['jenis_peruntukan'] = $data['jenis_peruntukan'] ?? $default;
            }
        }
        $data['status'] = $data['status'] ?? 'diterima';
        if ($request->hasFile('bukti_pembelian')) {
            if ($pembelian->bukti_pembelian) {
                Storage::disk('public')->delete($pembelian->bukti_pembelian);
            }
            $data['bukti_pembelian'] = $request->file('bukti_pembelian')->store('bukti-pembelian', 'public');
        }
        $data = $this->hitungPembelian($data);
        $pembelian->update($data);
        return redirect()->route('barang.index', ['tab' => 'pembelian'])->with('success', 'Item barang diupdate.');
    }

    public function destroyPembelian(PembelianBarang $pembelian)
    {
        $dipakaiDistribusi = DB::table('distribusi_pembelian_barang')
            ->where('pembelian_barang_id', $pembelian->id)->exists();
        if ($dipakaiDistribusi) {
            return back()->withErrors([
                'barang' => "Barang {$pembelian->nama_barang} sudah dipakai pada distribusi. Tidak bisa dihapus.",
            ])->withInput();
        }
        $pembelian->delete();
        return redirect()->route('barang.index')->with('success', 'Item barang dihapus.');
    }

    private function hitungPembelian(array $data): array
    {
        $rencana = max(0, (int) $data['qty_rencana']);
        $terbeli = max(0, (int) ($data['qty_terbeli'] ?? 0));
        $harga = max(0, (float) $data['harga_satuan']);

        $data['qty_belum'] = max(0, $rencana - $terbeli);
        $data['anggaran'] = $rencana * $harga;
        $data['realisasi'] = $terbeli * $harga;
        $data['sisa'] = max(0, $data['anggaran'] - $data['realisasi']);
        $data['persen_real'] = $data['anggaran'] > 0
            ? round(($data['realisasi'] / $data['anggaran']) * 100, 1)
            : 0;

        return $data;
    }
}
