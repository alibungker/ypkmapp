@extends('layouts.app')
@section('title', 'Keuangan')
@section('content')
{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px;">
    <div style="background:#e8f5ec;border-radius:12px;padding:20px;text-align:center;">
        <div style="font-size:13px;color:#6b7280;">💰 Dana Masuk</div>
        <div style="font-size:28px;font-weight:800;color:#017723;margin-top:4px;">Rp {{ number_format($total_masuk ?? 0,0,',','.') }}</div>
    </div>
    <div style="background:#fef2f2;border-radius:12px;padding:20px;text-align:center;">
        <div style="font-size:13px;color:#6b7280;">📦 Nilai Bantuan</div>
        <div style="font-size:28px;font-weight:800;color:#dc2626;margin-top:4px;">Rp {{ number_format($total_bantuan ?? 0,0,',','.') }}</div>
    </div>
    <div style="background:#fef7e6;border-radius:12px;padding:20px;text-align:center;">
        <div style="font-size:13px;color:#6b7280;">🚐 Biaya Operasional</div>
        <div style="font-size:28px;font-weight:800;color:#b07d14;margin-top:4px;">Rp {{ number_format($total_biaya ?? 0,0,',','.') }}</div>
    </div>
    <div style="background:#00034a;border-radius:12px;padding:20px;text-align:center;">
        <div style="font-size:13px;color:rgba(255,255,255,0.6);">💵 Sisa Dana</div>
        <div style="font-size:28px;font-weight:800;color:white;margin-top:4px;">Rp {{ number_format($sisa ?? 0,0,',','.') }}</div>
    </div>
</div>

{{-- Two columns: Forms --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    {{-- Form Dana Masuk --}}
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
            <h3 style="font-size:15px;font-weight:600;">📥 Tambah Dana Donatur</h3>
        </div>
        <div style="padding:20px;">
            <form method="POST" action="{{ route('keuangan.dana.store') }}">
                @csrf
                <div style="margin-bottom:12px;">
                    <label class="form-label">Nama Donatur <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="donatur" class="form-input" required placeholder="Pemprov Aceh / Hong Kong SWAB / Donasi Umum">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label class="form-label">Tanggal Masuk <span style="color:#dc2626;">*</span></label>
                        <input type="date" name="tanggal_masuk" class="form-input" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="form-label">Jumlah <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="jumlah" class="form-input" required step="0.01" placeholder="10000000">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px;">
                    <div>
                        <label class="form-label">Jenis</label>
                        <select name="jenis" class="form-input">
                            <option value="transfer">Transfer</option>
                            <option value="uang_tunai">Uang Tunai</option>
                            <option value="barang">Barang</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-input" placeholder="Tahap 1, dll">
                    </div>
                </div>
                <div style="margin-top:16px;display:flex;gap:8px;justify-content:flex-end;">
                    <button type="submit" class="btn btn-primary">💾 Simpan Dana</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Form Biaya Operasional --}}
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
            <h3 style="font-size:15px;font-weight:600;">🚐 Tambah Biaya Operasional</h3>
        </div>
        <div style="padding:20px;">
            <form method="POST" action="{{ route('keuangan.biaya.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label class="form-label">Kategori <span style="color:#dc2626;">*</span></label>
                        <select name="kategori" class="form-input" required>
                            <option value="transportasi">🚛 Transportasi</option>
                            <option value="konsumsi">🍱 Konsumsi</option>
                            <option value="sewa">🏠 Sewa</option>
                            <option value="atk">📦 ATK</option>
                            <option value="komunikasi">📞 Komunikasi</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Jumlah <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="jumlah" class="form-input" required step="0.01" placeholder="500000">
                    </div>
                </div>
                <div style="margin-top:12px;">
                    <label class="form-label">Deskripsi <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="deskripsi" class="form-input" required placeholder="Sewa mobil perjalanan Tamiang">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px;">
                    <div>
                        <label class="form-label">Tanggal <span style="color:#dc2626;">*</span></label>
                        <input type="date" name="tanggal" class="form-input" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="form-label">Kaitkan Distribusi</label>
                        <select name="distribusi_id" class="form-input">
                            <option value="">— Tidak ada —</option>
                            @foreach($distribusi_list ?? [] as $d)
                            <option value="{{ $d->id }}">{{ $d->nama_kegiatan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="margin-top:16px;display:flex;gap:8px;justify-content:flex-end;">
                    <button type="submit" class="btn btn-green" style="background:#017723;color:white;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">💾 Simpan Biaya</button>
{{-- Ringkasan Anggaran & Pembelian --}}
<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
        <h3 style="font-size:15px;font-weight:600;">📊 Ringkasan Anggaran Kegiatan & Pembelian</h3>
    </div>
    <div style="padding:20px;">
        <table class="table-data">
            <thead><tr><th>Total Anggaran</th><th>Total Realisasi</th><th>Sisa Anggaran</th><th>% Realisasi</th></tr></thead>
            <tbody>
                <tr>
                    <td style="font-size:18px;font-weight:800;color:#00034a;">Rp {{ number_format($total_anggaran_all ?? 0,0,',','.') }}</td>
                    <td style="font-size:18px;font-weight:800;color:#017723;">Rp {{ number_format($total_realisasi_all ?? 0,0,',','.') }}</td>
                    <td style="font-size:18px;font-weight:800;color:#b07d14;">Rp {{ number_format(($total_anggaran_all ?? 0) - ($total_realisasi_all ?? 0),0,',','.') }}</td>
                    <td style="font-size:18px;font-weight:800;">{{ $total_anggaran_all > 0 ? round(($total_realisasi_all/$total_anggaran_all)*100,1) : 0 }}%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Box 1: Realisasi Anggaran Kegiatan (Batch 1) --}}
<div class="card" style="margin-top:20px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
        <div><h3 style="font-size:15px;font-weight:600;">📦 Realisasi Anggaran Kegiatan</h3>
        <p style="font-size:12px;color:#6b7280;">Total: <strong>Rp {{ number_format($anggarans->sum('anggaran'),0,',','.') }}</strong> — Realisasi: <strong>Rp {{ number_format($anggarans->sum('realisasi'),0,',','.') }}</strong></p></div>
        <button onclick="alert('Form tambah menyusul di versi berikutnya')" class="btn btn-primary btn-sm">+ Tambah</button>
    </div>
    <div style="padding:16px 20px;overflow-x:auto;">
        <table class="table-data">
            <thead><tr><th>No</th><th>Komponen</th><th>Target</th><th>Anggaran</th><th>Realisasi</th><th>%</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($anggarans as $i => $a)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:500;">{{ $a->nama_anggaran ?? $a->kategori }}</td>
                    <td>{{ $a->target_paket ? number_format($a->target_paket) . ' ' . ($a->satuan ?? '') : '-' }}</td>
                    <td>Rp {{ number_format($a->anggaran,0,',','.') }}</td>
                    <td>Rp {{ number_format($a->realisasi,0,',','.') }}</td>
                    <td>
                        @php $pct = $a->anggaran > 0 ? round(($a->realisasi/$a->anggaran)*100,1) : 0; @endphp
                        <div class="progress-bar" style="width:80px;display:inline-block;"><div class="progress-fill" style="width:{{ $pct }}%;background:{{ $pct >= 100 ? '#017723' : '#e5a820' }};"></div></div>
                        <span style="font-size:12px;margin-left:4px;">{{ $pct }}%</span>
                    </td>
                    <td><span class="badge badge-green">✅ {{ $a->catatan ?? 'Lunas' }}</span></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f8f9fa;"><td></td><td><strong>TOTAL</strong></td><td></td>
                    <td><strong>Rp {{ number_format($anggarans->sum('anggaran'),0,',','.') }}</strong></td>
                    <td><strong>Rp {{ number_format($anggarans->sum('realisasi'),0,',','.') }}</strong></td>
                    <td><strong>{{ $anggarans->sum('anggaran') > 0 ? round(($anggarans->sum('realisasi')/$anggarans->sum('anggaran'))*100,1) : 0 }}%</strong></td>
                    <td><span class="badge badge-green">✅ Lunas</span></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- Box 2: Rekap Pembelian Barang (Batch 2) --}}
<div class="card" style="margin-top:20px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
        <div><h3 style="font-size:15px;font-weight:600;">📋 Rekap Pembelian Barang</h3>
        <p style="font-size:12px;color:#6b7280;">Total: <strong>Rp {{ number_format($pembelian->sum('anggaran'),0,',','.') }}</strong> — Realisasi: <strong>Rp {{ number_format($pembelian->sum('realisasi'),0,',','.') }}</strong></p></div>
        <button onclick="alert('Form tambah menyusul di versi berikutnya')" class="btn btn-primary btn-sm">+ Tambah</button>
    </div>
    <div style="padding:16px 20px;overflow-x:auto;">
        <table class="table-data">
            <thead><tr><th>No</th><th>Nama Barang</th><th>Batch</th><th>Qty Rencana</th><th>Qty Terbeli</th><th>Qty Belum</th><th>Harga Satuan</th><th>Anggaran</th><th>Realisasi</th><th>Sisa</th><th>%</th></tr></thead>
            <tbody>
                @foreach($pembelian as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:500;">{{ $p->nama_barang }}</td>
                    <td style="font-size:12px;">{{ $p->batch ?? '-' }}</td>
                    <td>{{ number_format($p->qty_rencana) }}</td>
                    <td>{{ number_format($p->qty_terbeli) }}</td>
                    <td style="color:#dc2626;">{{ $p->qty_belum > 0 ? number_format($p->qty_belum) : '<span style="color:#017723;">0</span>' }}</td>
                    <td>Rp {{ number_format($p->harga_satuan,0,',','.') }}</td>
                    <td>Rp {{ number_format($p->anggaran,0,',','.') }}</td>
                    <td>Rp {{ number_format($p->realisasi,0,',','.') }}</td>
                    <td>Rp {{ number_format($p->sisa,0,',','.') }}</td>
                    <td>
                        <div class="progress-bar" style="width:60px;display:inline-block;"><div class="progress-fill" style="width:{{ $p->persen_real }}%;background:{{ $p->persen_real >= 100 ? '#017723' : '#e5a820' }};"></div></div>
                        <span style="font-size:12px;">{{ $p->persen_real }}%</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Riwayat Dana Masuk --}}
<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
        <h3 style="font-size:15px;font-weight:600;">📋 Riwayat Transaksi</h3>
    </div>
    <div style="padding:16px 20px;">
        <table class="table-data">
            <thead><tr><th>Donatur</th><th>Tanggal</th><th>Jumlah</th><th>Jenis</th><th>Keterangan</th><th></th></tr></thead>
            <tbody>
                @forelse($dana_masuk ?? [] as $d)
                <tr>
                    <td style="font-weight:500;">{{ $d->donatur }}</td>
                    <td style="color:#6b7280;">{{ is_object($d->tanggal_masuk) ? $d->tanggal_masuk->format('d M Y') : date('d M Y', strtotime($d->tanggal_masuk)) }}</td>
                    <td style="font-weight:600;color:#017723;">Rp {{ number_format($d->jumlah,0,',','.') }}</td>
                    <td><span style="padding:2px 10px;background:#e8e8f0;border-radius:6px;font-size:12px;">{{ $d->jenis }}</span></td>
                    <td style="color:#6b7280;">{{ $d->keterangan ?? '-' }}</td>
                    <td style="white-space:nowrap;">
                        <button onclick="editDana({{ $d->id }}, '{{ $d->donatur }}', '{{ is_object($d->tanggal_masuk) ? $d->tanggal_masuk->format('Y-m-d') : $d->tanggal_masuk }}', {{ $d->jumlah }}, '{{ $d->jenis }}', '{{ $d->keterangan ?? '' }}')" style="color:#00034a;border:none;background:none;cursor:pointer;font-size:13px;padding:4px 8px;">✏️ Edit</button>
                        <form method="POST" action="{{ route('keuangan.dana.delete', $d->id) }}" style="display:inline;" onsubmit="return confirm('Hapus data dana ini?')">
                            @csrf
                            <button style="color:#dc2626;border:none;background:none;cursor:pointer;font-size:13px;padding:4px 8px;">🗑️ Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="padding:32px;text-align:center;color:#9ca3af;">Belum ada dana masuk. Silakan tambah melalui form di atas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Edit Modal --}}
                    {{-- Edit Modal --}}
                    <div id="editModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;" onclick="if(event.target===this)closeEdit()">
                        <div style="background:white;border-radius:12px;padding:24px;width:90%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,0.3);" onclick="event.stopPropagation()">
                            <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">✏️ Edit Dana Donatur</h3>
                            <form id="editForm" method="POST">
                                @csrf
                                <div style="margin-bottom:12px;">
                                    <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Nama Donatur</label>
                                    <input id="editDonatur" name="donatur" class="form-input" required>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                                    <div>
                                        <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Tanggal Masuk</label>
                                        <input id="editTanggal" name="tanggal_masuk" type="date" class="form-input" required>
                                    </div>
                                    <div>
                                        <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Jumlah (Rp)</label>
                                        <input id="editJumlah" name="jumlah" type="number" step="0.01" class="form-input" required>
                                    </div>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px;">
                                    <div>
                                        <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Jenis</label>
                                        <select id="editJenis" name="jenis" class="form-input">
                                            <option value="transfer">Transfer</option>
                                            <option value="uang_tunai">Uang Tunai</option>
                                            <option value="barang">Barang</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Keterangan</label>
                                        <input id="editKeterangan" name="keterangan" class="form-input" placeholder="Opsional">
                                    </div>
                                </div>
                                <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:20px;padding-top:16px;border-top:1px solid #e5e7eb;">
                                    <button type="button" onclick="closeEdit()" class="btn btn-outline">Batal</button>
                                    <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <script>
                    function editDana(id, donatur, tgl, jumlah, jenis, keterangan) {
                        document.getElementById('editForm').action = '/keuangan/dana/' + id + '/update';
                        document.getElementById('editDonatur').value = donatur;
                        document.getElementById('editTanggal').value = tgl;
                        document.getElementById('editJumlah').value = jumlah;
                        document.getElementById('editJenis').value = jenis;
                        document.getElementById('editKeterangan').value = keterangan;
                        document.getElementById('editModal').style.display = 'flex';
                    }
                    function closeEdit() {
                        document.getElementById('editModal').style.display = 'none';
                    }
                    </script>
                    @endsection
