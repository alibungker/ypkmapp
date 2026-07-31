<?php
namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\PembelianBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BarangController extends Controller
{
    public function index()
    {
        $anggarans = Anggaran::orderBy('id')->get();
        $pembelian = PembelianBarang::orderBy('id')->get();
        $tersalurkan = DB::table('kegiatan_barang')
            ->selectRaw('pembelian_barang_id, SUM(jumlah) as total')
            ->groupBy('pembelian_barang_id')->pluck('total', 'pembelian_barang_id');
        $pembelian->each(function ($item) use ($tersalurkan) {
            $item->stok_tersedia = max(0, $item->qty_terbeli - (int) ($tersalurkan[$item->id] ?? 0));
        });
        return view('barang.index', compact('anggarans', 'pembelian'));
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

        DB::transaction(function () use ($data, $alokasi) {
            $kegiatan = Anggaran::create($data);
            foreach ($alokasi as $baris) {
                $barang = PembelianBarang::lockForUpdate()->findOrFail($baris['pembelian_barang_id']);
                $sudahTersalurkan = (int) DB::table('kegiatan_barang')
                    ->where('pembelian_barang_id', $barang->id)->sum('jumlah');
                $stokTersedia = max(0, $barang->qty_terbeli - $sudahTersalurkan);
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
            'anggaran' => 'required|numeric',
            'realisasi' => 'required|numeric',
            'catatan' => 'nullable',
        ]);
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
            'batch' => 'nullable',
            'qty_rencana' => 'required|integer',
            'qty_terbeli' => 'nullable|integer',
            'harga_satuan' => 'required|numeric|min:0',
            'anggaran' => 'nullable|numeric|min:0',
            'realisasi' => 'nullable|numeric|min:0',
        ]);
        $data['qty_terbeli'] = $data['qty_terbeli'] ?? 0;
        $data = $this->hitungPembelian($data);
        PembelianBarang::create($data);
        return redirect()->route('barang.index', ['tab' => 'pembelian'])->with('success', 'Item barang berhasil ditambahkan.');
    }

    public function updatePembelian(Request $request, PembelianBarang $pembelian)
    {
        $data = $request->validate([
            'nama_barang' => 'required',
            'batch' => 'nullable',
            'qty_rencana' => 'required|integer',
            'qty_terbeli' => 'required|integer',
            'harga_satuan' => 'required|numeric|min:0',
            'anggaran' => 'nullable|numeric|min:0',
            'realisasi' => 'nullable|numeric|min:0',
        ]);
        $data = $this->hitungPembelian($data);
        $pembelian->update($data);
        return redirect()->route('barang.index', ['tab' => 'pembelian'])->with('success', 'Item barang diupdate.');
    }

    public function destroyPembelian(PembelianBarang $pembelian)
    {
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
