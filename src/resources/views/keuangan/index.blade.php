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
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Riwayat Dana Masuk --}}
<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
        <h3 style="font-size:15px;font-weight:600;">📋 Riwayat Transaksi</h3>
    </div>
    <div style="padding:16px 20px;">
        <table class="table-data">
            <thead><tr><th>Donatur</th><th>Tanggal</th><th>Jumlah</th><th>Jenis</th><th>Keterangan</th></tr></thead>
            <tbody>
                @forelse($dana_masuk ?? [] as $d)
                <tr>
                    <td style="font-weight:500;">{{ $d->donatur }}</td>
                    <td style="color:#6b7280;">{{ is_object($d->tanggal_masuk) ? $d->tanggal_masuk->format('d M Y') : date('d M Y', strtotime($d->tanggal_masuk)) }}</td>
                    <td style="font-weight:600;color:#017723;">Rp {{ number_format($d->jumlah,0,',','.') }}</td>
                    <td><span style="padding:2px 10px;background:#e8e8f0;border-radius:6px;font-size:12px;">{{ $d->jenis }}</span></td>
                    <td style="color:#6b7280;">{{ $d->keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="padding:32px;text-align:center;color:#9ca3af;">Belum ada dana masuk. Silakan tambah melalui form di atas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
