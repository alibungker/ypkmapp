<?php
namespace App\Http\Controllers;

use App\Models\Penerima;
use App\Models\Kelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PenerimaController extends Controller
{
    // Terapkan kunci wilayah kerja user pada query penerima
    private function applyWilayahLock($query)
    {
        $u = auth()->user();
        if ($u && !$u->isAdmin()) {
            // Ketua Kelompok hanya boleh melihat anggota kelompok yang ditetapkan.
            if ($u->isKetuaKelompok()) {
                if ($u->kelompok_id) {
                    $query->where('kelompok_id', $u->kelompok_id);
                } else {
                    $query->whereRaw('1 = 0');
                }
                return $query;
            }

            if ($u->wilayah_kabupaten) $query->where('kabupaten', $u->wilayah_kabupaten);
            if ($u->wilayah_kecamatan) $query->where('kecamatan', $u->wilayah_kecamatan);
            if ($u->wilayah_desa) $query->where('desa', $u->wilayah_desa);
        }
        return $query;
    }

    // Cek apakah user boleh akses data penerima ini (sesuai kunci wilayah)
    private function cekAksesWilayah(Penerima $penerima)
    {
        $u = auth()->user();
        if (!$u || $u->isAdmin()) return true;
        if ($u->isKetuaKelompok()) {
            return $u->kelompok_id && (int) $penerima->kelompok_id === (int) $u->kelompok_id;
        }
        if ($u->wilayah_kabupaten && $penerima->kabupaten !== $u->wilayah_kabupaten) return false;
        if ($u->wilayah_kecamatan && $penerima->kecamatan !== $u->wilayah_kecamatan) return false;
        if ($u->wilayah_desa && $penerima->desa !== $u->wilayah_desa) return false;
        return true;
    }

    public function index(Request $request)
    {
        $query = Penerima::with('kelompok');
        $this->applyWilayahLock($query);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('nik', 'like', "%{$request->search}%");
            });
        }
        if ($request->status) {
            switch ($request->status) {
                case 'pending':
                    $query->where('status', 'pending');
                    break;
                case 'ditolak':
                    $query->where('status', 'ditolak');
                    break;
                case 'terverifikasi':
                    $query->where('status', 'terverifikasi');
                    break;
                case 'menunggu_bantuan':
                    $query->where('status', 'terverifikasi')->where('terima_bantuan', false);
                    break;
                case 'menerima_bantuan':
                    $query->where('status', 'terverifikasi')->where('terima_bantuan', true);
                    break;
            }
        }
        if ($request->sumber_data) {
            $query->where('sumber_data', $request->sumber_data);
        }
        if ($request->kabupaten) {
            $query->where('kabupaten', $request->kabupaten);
        }
        if ($request->kecamatan) {
            $query->where('kecamatan', $request->kecamatan);
        }
        if ($request->desa) {
            $query->where('desa', $request->desa);
        }
        if ($request->filled('kelompok_id')) {
            $query->where('kelompok_id', $request->integer('kelompok_id'));
        }
        if (in_array((string) $request->status_terima, ['0', '1'], true)) {
            $query->where('terima_bantuan', (bool) $request->integer('status_terima'));
        }

        $penerima = $query->orderBy('created_at', 'desc')->get();

        $kelompokQuery = Kelompok::query()->orderBy('nama');
        $user = auth()->user();
        if ($user->isKetuaKelompok()) {
            $kelompokQuery->whereKey($user->kelompok_id);
        } elseif ($user->isRelawan()) {
            if ($user->wilayah_kabupaten) $kelompokQuery->where('daerah', $user->wilayah_kabupaten);
            if ($user->wilayah_kecamatan) $kelompokQuery->where('kecamatan', $user->wilayah_kecamatan);
            if ($user->wilayah_desa) $kelompokQuery->where('desa', $user->wilayah_desa);
        }
        $kelompoks = $kelompokQuery->get();

        return view('penerima.index', compact('penerima', 'kelompoks'));
    }

    public function create()
    {
        $user = auth()->user();
        $kelompoks = $user->isKetuaKelompok()
            ? Kelompok::whereKey($user->kelompok_id)->get()
            : Kelompok::all();
        return view('penerima.form', compact('kelompoks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nik' => 'required|digits:16|unique:penerimas,nik',
            'no_kk' => 'nullable',
            'nama' => 'required',
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'phone' => 'required',
            'pekerjaan' => 'nullable',
            'jumlah_keluarga' => 'nullable|integer',
            'penghasilan' => 'nullable|numeric',
            'titik_koordinat' => 'nullable',
            'kategori_kerentanan' => 'nullable|in:lansia,yatim_piatu,korban_bencana,keluarga_miskin',
            'tingkat_penghasilan' => 'nullable|in:rendah,menengah,tinggi,tidak_ada',
            'status_kelayakan' => 'nullable|in:layak,perlu_verifikasi,tidak_layak',
            'kelompok_id' => 'required|exists:kelompoks,id',
            'sumber_data' => 'required|in:mandiri,relawan,ketua_kelompok',
        ]);

        // Validasi kunci wilayah: user terkunci tidak boleh input di luar wilayahnya
        $u = auth()->user();
        if ($u && !$u->isAdmin()) {
            if ($u->wilayah_kabupaten && $data['kabupaten'] !== $u->wilayah_kabupaten) {
                return back()->withInput()->withErrors(['kabupaten' => 'Anda hanya bisa input penerima di wilayah ' . $u->wilayahLabel()]);
            }
            if ($u->wilayah_kecamatan && $data['kecamatan'] !== $u->wilayah_kecamatan) {
                return back()->withInput()->withErrors(['kecamatan' => 'Anda hanya bisa input penerima di wilayah ' . $u->wilayahLabel()]);
            }
            if ($u->wilayah_desa && $data['desa'] !== $u->wilayah_desa) {
                return back()->withInput()->withErrors(['desa' => 'Anda hanya bisa input penerima di wilayah ' . $u->wilayahLabel()]);
            }
            // Ketua kelompok: data yang diinput otomatis bersumber ketua_kelompok & status pending
            if ($u->isKetuaKelompok()) {
                if (!$u->kelompok_id) {
                    return back()->withInput()->withErrors(['kelompok_id' => 'Akun Ketua Kelompok belum terhubung ke kelompok.']);
                }
                $data['kelompok_id'] = $u->kelompok_id;
                $data['sumber_data'] = 'ketua_kelompok';
            }
        }

        $data['status'] = 'pending';
        $data['provinsi'] = 'Aceh';

        if ($request->hasFile('foto_ktp')) {
            $data['foto_ktp'] = $request->file('foto_ktp')->store('penerima/ktp', 'public');
        }

        Penerima::create($data);
        return redirect()->route('penerima.index')->with('success', 'Data penerima diajukan — menunggu verifikasi relawan.');
    }

    public function show(Penerima $penerima)
    {
        abort_unless($this->cekAksesWilayah($penerima), 403, 'Di luar wilayah kerja Anda.');
        $penerima->load('kelompok', 'verifikator', 'penerimaTerima', 'penerimaDistribusi.distribusi');
        return view('penerima.show', compact('penerima'));
    }

    public function edit(Penerima $penerima)
    {
        abort_unless($this->cekAksesWilayah($penerima), 403, 'Di luar wilayah kerja Anda.');
        $user = auth()->user();
        $kelompoks = $user->isKetuaKelompok()
            ? Kelompok::whereKey($user->kelompok_id)->get()
            : Kelompok::all();
        return view('penerima.form', compact('penerima', 'kelompoks'));
    }

    public function update(Request $request, Penerima $penerima)
    {
        abort_unless($this->cekAksesWilayah($penerima), 403, 'Di luar wilayah kerja Anda.');
        $data = $request->validate([
            'nik' => 'required|digits:16|unique:penerimas,nik,' . $penerima->id,
            'nama' => 'required',
            'no_kk' => 'nullable',
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat' => 'required',
            'kabupaten' => 'nullable',
            'kecamatan' => 'nullable',
            'desa' => 'nullable',
            'phone' => 'required',
            'pekerjaan' => 'nullable',
            'jumlah_keluarga' => 'nullable|integer',
            'penghasilan' => 'nullable|numeric',
            'titik_koordinat' => 'nullable',
            'kategori_kerentanan' => 'nullable|in:lansia,yatim_piatu,korban_bencana,keluarga_miskin',
            'tingkat_penghasilan' => 'nullable|in:rendah,menengah,tinggi,tidak_ada',
            'status_kelayakan' => 'nullable|in:layak,perlu_verifikasi,tidak_layak',
            'kelompok_id' => 'required|exists:kelompoks,id',
            'sumber_data' => 'nullable|in:mandiri,relawan,ketua_kelompok',
        ]);

        if (!auth()->user()->isAdmin()) {
            // Perubahan status hanya melalui alur verifikasi khusus Relawan/Admin.
            unset($data['status']);
            unset($data['sumber_data']);
        }

        if (auth()->user()->isKetuaKelompok()) {
            $data['kelompok_id'] = auth()->user()->kelompok_id;
            $data['sumber_data'] = 'ketua_kelompok';
        }

        $penerima->update($data);
        return redirect()->route('penerima.index')->with('success', 'Data penerima diupdate.');
    }

    public function destroy(Penerima $penerima)
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya admin yang bisa menghapus.');
        $penerima->delete();
        return redirect()->route('penerima.index')->with('success', 'Data penerima dihapus.');
    }

    // Verifikasi oleh RELAWAN / ADMIN (ketua kelompok TIDAK boleh verifikasi)
    public function verify(Penerima $penerima)
    {
        $u = auth()->user();
        abort_if($u->isKetuaKelompok(), 403, 'Ketua kelompok tidak dapat memverifikasi. Verifikasi dilakukan relawan.');
        abort_unless($this->cekAksesWilayah($penerima), 403, 'Di luar wilayah kerja Anda.');

        $data = request()->validate([
            'status' => 'nullable|in:terverifikasi,ditolak',
            'catatan' => 'nullable|string|max:1000',
        ]);
        $status = $data['status'] ?? 'terverifikasi';

        if ($status === 'terverifikasi' && !$penerima->kelompok_id) {
            return back()->with('error', 'Tetapkan kelompok penerima sebelum melakukan verifikasi.');
        }

        $penerima->update([
            'status' => $status,
            'catatan_verifikasi' => $data['catatan'] ?? null,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);
        return back()->with('success', 'Status penerima diperbarui.');
    }

    // Checklist TERIMA BANTUAN oleh relawan (setelah terverifikasi)
    public function terimaBantuan(Penerima $penerima)
    {
        $u = auth()->user();
        abort_if($u->isKetuaKelompok(), 403, 'Checklist terima bantuan dilakukan oleh relawan.');
        abort_unless($this->cekAksesWilayah($penerima), 403, 'Di luar wilayah kerja Anda.');

        if ($penerima->status !== 'terverifikasi') {
            return back()->with('error', 'Penerima harus terverifikasi dulu sebelum checklist terima bantuan.');
        }

        $penerima->update([
            'terima_bantuan' => !$penerima->terima_bantuan,
            'terima_by' => $penerima->terima_bantuan ? null : auth()->id(),
            'terima_at' => $penerima->terima_bantuan ? null : now(),
        ]);
        return back()->with('success', $penerima->terima_bantuan ? '✅ Penerima dicheklist MENERIMA BANTUAN.' : 'Checklist dibatalkan.');
    }

    public function formDaftar()
    {
        $kelompoks = Kelompok::all();
        return view('penerima.daftar', compact('kelompoks'));
    }

    public function daftarMandiri(Request $request)
    {
        $data = $request->validate([
            'nik' => 'required|digits:16|unique:penerimas,nik',
            'nama' => 'required',
            'alamat' => 'required',
            'phone' => 'required',
            'jumlah_keluarga' => 'nullable|integer',
            'privacy_consent' => 'required|accepted',
        ]);

        unset($data['privacy_consent']);
        $data['status'] = 'pending';
        $data['sumber_data'] = 'mandiri';
        $data['provinsi'] = 'Aceh';
        $data['kabupaten'] = $request->kabupaten ?? '-';
        $data['kecamatan'] = $request->kecamatan ?? '-';
        $data['desa'] = $request->desa ?? '-';
        // Pendaftaran publik belum terikat kelompok sampai diverifikasi petugas.
        $data['kelompok_id'] = null;

        Penerima::create($data);
        return back()->with('success', 'Pendaftaran berhasil! Data Anda akan diverifikasi petugas YPKM.');
    }

    // ============================================================
    // CSV IMPORT: template download, preview, store
    // ============================================================

    private const CSV_HEADERS = [
        'nik', 'no_kk', 'nama', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'alamat', 'kabupaten', 'kecamatan', 'desa',
        'rt_rw', 'phone', 'pekerjaan', 'jumlah_keluarga',
        'kelompok_id', 'sumber_data',
    ];

    public function importTemplate()
    {
        $filename = 'template-import-penerima.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, self::CSV_HEADERS);
            // Example row
            fputcsv($handle, [
                '1101010101900001', '', 'Contoh Nama', 'Banda Aceh', '1990-01-01',
                'L', 'Jl. Contoh No. 1', 'Aceh Selatan', 'Sekerak', 'Juar',
                '002/001', '081234567890', 'Petani', '4',
                '1', 'relawan',
            ]);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $rows = $this->parseCsv($path);

        // Get existing NIKs for duplicate detection
        $niks = array_filter(array_column($rows, 'nik'));
        $existingNiks = [];
        if ($niks) {
            $existingNiks = Penerima::whereIn('nik', $niks)->pluck('nik')->toArray();
        }

        // Validate kelompok_ids
        $kelompokIds = array_filter(array_column($rows, 'kelompok_id'));
        $validKelompokIds = [];
        if ($kelompokIds) {
            $validKelompokIds = Kelompok::whereIn('id', $kelompokIds)->pluck('id')->toArray();
        }

        $validRows = [];
        $errorRows = [];
        $validSumber = ['mandiri', 'relawan', 'ketua_kelompok'];

        foreach ($rows as $i => $row) {
            $lineNo = $i + 2; // +1 for 0-index, +1 for header
            $errors = [];

            if (empty($row['nik'])) {
                $errors[] = 'NIK kosong';
            } elseif (!preg_match('/^\d{16}$/', $row['nik'])) {
                $errors[] = 'NIK harus 16 digit angka';
            } elseif (in_array($row['nik'], $existingNiks)) {
                $errors[] = 'NIK sudah terdaftar di database';
            }

            if (empty($row['nama'])) $errors[] = 'Nama kosong';
            if (empty($row['alamat'])) $errors[] = 'Alamat kosong';
            if (empty($row['kabupaten'])) $errors[] = 'Kabupaten kosong';
            if (empty($row['kecamatan'])) $errors[] = 'Kecamatan kosong';
            if (empty($row['desa'])) $errors[] = 'Desa kosong';
            if (empty($row['phone'])) $errors[] = 'No HP kosong';

            if (!empty($row['jenis_kelamin']) && !in_array($row['jenis_kelamin'], ['L', 'P'])) {
                $errors[] = 'Jenis kelamin harus L atau P';
            }

            if (!empty($row['kelompok_id'])) {
                if (!in_array((int)$row['kelompok_id'], $validKelompokIds)) {
                    $errors[] = 'Kelompok ID tidak ditemukan';
                }
            } else {
                $errors[] = 'Kelompok ID kosong';
            }

            $sumber = $row['sumber_data'] ?? 'relawan';
            if (!in_array($sumber, $validSumber)) {
                $errors[] = 'Sumber data tidak valid';
            }

            // Check NIK duplicate within the same file
            $nikCount = array_count_values(array_filter(array_column($rows, 'nik')));
            if (!empty($row['nik']) && ($nikCount[$row['nik']] ?? 0) > 1) {
                $errors[] = 'NIK duplikat dalam file';
            }

            if ($errors) {
                $row['_line'] = $lineNo;
                $row['_errors'] = $errors;
                $errorRows[] = $row;
            } else {
                $row['_line'] = $lineNo;
                $validRows[] = $row;
            }
        }

        // Store parsed data in session for the actual import
        session(['import_data' => array_map(function ($r) {
            unset($r['_line'], $r['_errors']);
            return $r;
        }, $validRows)]);

        return view('penerima.import-preview', compact('validRows', 'errorRows'));
    }

    public function importStore(Request $request)
    {
        $data = session('import_data');
        if (!$data || !is_array($data)) {
            return redirect()->route('penerima.index')->with('error', 'Sesi import kedaluwarsa. Upload ulang file CSV.');
        }

        $imported = 0;
        $errors = [];

        DB::transaction(function () use ($data, &$imported, &$errors) {
            foreach ($data as $i => $row) {
                try {
                    Penerima::create([
                        'nik' => $row['nik'],
                        'no_kk' => $row['no_kk'] ?? null,
                        'nama' => $row['nama'],
                        'tempat_lahir' => $row['tempat_lahir'] ?? null,
                        'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
                        'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
                        'alamat' => $row['alamat'],
                        'provinsi' => 'Aceh',
                        'kabupaten' => $row['kabupaten'],
                        'kecamatan' => $row['kecamatan'],
                        'desa' => $row['desa'],
                        'rt_rw' => $row['rt_rw'] ?? null,
                        'phone' => $row['phone'],
                        'pekerjaan' => $row['pekerjaan'] ?? null,
                        'jumlah_keluarga' => !empty($row['jumlah_keluarga']) ? (int)$row['jumlah_keluarga'] : null,
                        'kelompok_id' => (int)$row['kelompok_id'],
                        'sumber_data' => $row['sumber_data'] ?? 'relawan',
                        'status' => 'pending',
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($i + 2) . ": " . $e->getMessage();
                }
            }
        });

        session()->forget('import_data');

        $msg = "✅ {$imported} penerima berhasil diimport.";
        if ($errors) {
            $msg .= " ⚠️ " . count($errors) . " baris gagal: " . implode('; ', array_slice($errors, 0, 3));
        }
        return redirect()->route('penerima.index')->with('success', $msg);
    }

    public function importCancel()
    {
        session()->forget('import_data');
        return redirect()->route('penerima.index')->with('success', 'Import dibatalkan.');
    }

    private function parseCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        // Skip BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return [];
        }

        // Trim and lowercase headers
        $headers = array_map(fn($h) => strtolower(trim($h)), $headers);

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === 1 && $row[0] === null) continue; // Skip empty lines
            $rows[] = array_combine($headers, array_pad($row, count($headers), ''));
        }
        fclose($handle);
        return $rows;
    }
}
