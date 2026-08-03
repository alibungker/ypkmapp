@extends('layouts.app')
@section('title', 'Distribusi')
@section('styles')
<style>
.dist-filter{padding:14px 20px;background:#f8fafc;border-bottom:1px solid #e5e7eb;display:grid;grid-template-columns:minmax(180px,2fr) repeat(2,minmax(140px,1fr)) auto auto;gap:10px;align-items:end}
@media(max-width:900px){.dist-filter{grid-template-columns:1fr 1fr}}
@media(max-width:560px){.dist-filter{grid-template-columns:1fr;padding:12px}}
.dist-items{display:flex;gap:4px;flex-wrap:wrap;max-width:280px}
.dist-items .badge{white-space:nowrap;max-width:100%;overflow:hidden;text-overflow:ellipsis;display:inline-flex}
</style>
@endsection
@section('content')
@if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif

{{-- Dashboard Stats --}}
<div class="mobile-stack" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:20px;">
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#e8f5ec;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#017723;font-size:20px;">👥</div>
            <span style="font-size:13px;color:#6b7280;">Kelompok</span>
        </div>
        <div class="stat-value" style="font-size:18px;color:#017723;">{{ number_format($stats['kelompok']) }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">Kelompok penerima distribusi</div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#e8e8f0;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#00034a;font-size:20px;">📍</div>
            <span style="font-size:13px;color:#6b7280;">Titik Distribusi</span>
        </div>
        <div class="stat-value" style="font-size:18px;">{{ number_format($stats['titik']) }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">Total lokasi distribusi</div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#fef7e6;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#b07d14;font-size:20px;">📦</div>
            <span style="font-size:13px;color:#6b7280;">Paket</span>
        </div>
        <div class="stat-value" style="font-size:18px;color:#b07d14;">{{ number_format($stats['paket']) }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">Total paket distribusi</div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#fef2f2;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#b42318;font-size:20px;">💰</div>
            <span style="font-size:13px;color:#6b7280;">Anggaran</span>
        </div>
        <div class="stat-value" style="font-size:18px;">Rp {{ number_format($stats['anggaran'],0,',','.') }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">Total estimasi nilai</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 style="font-size:15px;font-weight:600;">Distribusi bantuan</h3>
        <div style="display:flex;gap:8px;align-items:center;">
            @if(auth()->user()->isAdmin())
            <a href="{{ route('distribusi.create') }}" class="btn btn-primary btn-sm">+ Buat Distribusi</a>
            @endif
        </div>
    </div>
    <form method="GET" action="{{ route('distribusi.index') }}" class="dist-filter">
        <div>
            <label class="form-label">Cari</label>
            <input type="search" name="q" value="{{ request('q') }}" class="form-input" placeholder="Kegiatan, lokasi, atau kelompok">
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">Semua status</option>
                <option value="direncanakan" {{ request('status')=='direncanakan' ? 'selected' : '' }}>📋 Direncanakan</option>
                <option value="berlangsung" {{ request('status')=='berlangsung' ? 'selected' : '' }}>⏳ Berlangsung</option>
                <option value="selesai" {{ request('status')=='selesai' ? 'selected' : '' }}>✅ Selesai</option>
                <option value="dibatalkan" {{ request('status')=='dibatalkan' ? 'selected' : '' }}>❌ Dibatalkan</option>
            </select>
        </div>
        <div>
            <label class="form-label">Kabupaten/Kota</label>
            <select name="daerah" class="form-input">
                <option value="">Semua daerah</option>
                @foreach($kabupatens ?? [] as $kode => $nama)
                <option value="{{ preg_replace('/^(Kabupaten|Kota)\s/', '', $nama) }}" {{ request('daerah') == preg_replace('/^(Kabupaten|Kota)\s/', '', $nama) ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm" style="min-height:36px;">🔍 Filter</button>
        <a href="{{ route('distribusi.index') }}" class="btn btn-outline btn-sm" style="text-align:center;min-height:36px;">Reset</a>
    </form>
    <div style="padding:10px 20px 0;color:#6b7280;font-size:12px;">Menampilkan <strong>{{ $distribusi->total() ?? 0 }}</strong> distribusi</div>
    <div class="card-body">
        <div class="table-wrap desktop-table">
        @php
            $sortLink = function (string $column) {
                $active = request('sort') === $column;
                $nextDirection = $active && request('direction', 'desc') === 'asc' ? 'desc' : 'asc';
                return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection, 'page' => null]);
            };
            $sortIcon = function (string $column) {
                if (request('sort') !== $column) return '↕';
                return request('direction', 'desc') === 'asc' ? '↑' : '↓';
            };
        @endphp
        <style>
            .sort-link{display:flex;align-items:center;gap:6px;color:inherit;text-decoration:none;white-space:nowrap}
            .sort-link:hover{color:#017723}.sort-icon{font-size:12px;opacity:.75}
        </style>
        <table class="table-data">
            <thead><tr>
                <th><a class="sort-link" href="{{ $sortLink('kegiatan') }}">Kegiatan <span class="sort-icon">{{ $sortIcon('kegiatan') }}</span></a></th>
                <th><a class="sort-link" href="{{ $sortLink('kelompok') }}">Kelompok <span class="sort-icon">{{ $sortIcon('kelompok') }}</span></a></th>
                <th><a class="sort-link" href="{{ $sortLink('target') }}">Target Paket <span class="sort-icon">{{ $sortIcon('target') }}</span></a></th>
                <th>Isi Paket/Kegiatan</th>
                <th><a class="sort-link" href="{{ $sortLink('penerima') }}">Penerima <span class="sort-icon">{{ $sortIcon('penerima') }}</span></a></th>
                <th><a class="sort-link" href="{{ $sortLink('nilai') }}">Nilai <span class="sort-icon">{{ $sortIcon('nilai') }}</span></a></th>
                <th><a class="sort-link" href="{{ $sortLink('tanggal') }}">Tanggal <span class="sort-icon">{{ $sortIcon('tanggal') }}</span></a></th>
                <th><a class="sort-link" href="{{ $sortLink('status') }}">Status <span class="sort-icon">{{ $sortIcon('status') }}</span></a></th>
                <th></th>
            </tr></thead>
            <tbody>
                @forelse($distribusi ?? [] as $d)
                <tr>
                    <td style="font-weight:500;">{{ $d->nama_kegiatan }}</td>
                    <td>{{ $d->kelompok->nama ?? '-' }}</td>
                    <td style="font-weight:700;color:#b07d14;">{{ number_format($d->jumlah_paket) }} paket</td>
                    <td style="min-width:180px;max-width:280px;">
                        @if($d->items->isNotEmpty())
                            <div class="dist-items">
                            @foreach($d->items as $item)
                                <span class="badge badge-navy" title="{{ $item->barang->nama ?? 'Barang' }}">{{ $item->barang->nama ?? 'Barang' }}: {{ rtrim(rtrim(number_format($item->jumlah_per_paket, 2, ',', '.'), '0'), ',') }}/paket</span>
                            @endforeach
                            </div>
                        @elseif($d->pembelianBarang->isNotEmpty())
                            <div class="dist-items">
                            @foreach($d->pembelianBarang as $barang)
                                <span class="badge badge-navy">{{ $barang->nama_barang ?? $barang->nama ?? 'Barang' }}: {{ number_format($barang->pivot->jumlah ?? 0) }}</span>
                            @endforeach
                            </div>
                        @else
                            <span style="font-size:12px;color:#9ca3af;">Belum ada rincian</span>
                        @endif
                    </td>
                    <td>👥 {{ number_format($d->kelompok->penerima_count ?? 0) }}</td>
                    <td>Rp {{ number_format($d->estimasi_nilai_total,0,',','.') }}</td>
                    <td style="color:#6b7280;">{{ is_object($d->tanggal) ? $d->tanggal->format('d M Y') : date('d M Y', strtotime($d->tanggal)) }}</td>
                    <td>
                        @if($d->status == 'selesai') <span class="badge badge-green">✅ Selesai</span>
                        @elseif($d->status == 'berlangsung') <span class="badge badge-gold">⏳ Berlangsung</span>
                        @elseif($d->status == 'dibatalkan') <span class="badge" style="background:#fce8e6;color:#dc2626;">❌ Dibatalkan</span>
                        @else <span class="badge badge-navy">📋 Rencana</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('distribusi.show', $d) }}" style="color:#017723;font-size:13px;padding:4px 8px;text-decoration:none;">👁️ Detail</a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('distribusi.edit', $d) }}" style="color:#00034a;font-size:13px;padding:4px 8px;text-decoration:none;">✏️ Edit</a>
                        <form method="POST" action="{{ route('distribusi.destroy', $d) }}" style="display:inline;" onsubmit="return confirm('Hapus distribusi ini?')">
                            @csrf @method('DELETE')
                            <button style="color:#dc2626;border:none;background:none;cursor:pointer;font-size:13px;padding:4px 8px;">🗑️</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="padding:32px;text-align:center;color:#9ca3af;">Belum ada distribusi</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="mobile-card-list" aria-label="Daftar distribusi">
            @forelse($distribusi ?? [] as $d)
            <article class="mobile-data-card">
                <div class="mobile-data-card__title">{{ $d->nama_kegiatan }}</div>
                <div class="mobile-data-card__meta">
                    {{ $d->kelompok->nama ?? '-' }} · {{ is_object($d->tanggal) ? $d->tanggal->format('d/m/Y') : date('d/m/Y', strtotime($d->tanggal)) }}<br>
                    {{ number_format($d->jumlah_paket) }} paket · Rp {{ number_format($d->estimasi_nilai_total,0,',','.') }}
                    @if($d->items->isNotEmpty()||$d->pembelianBarang->isNotEmpty())
                    <br><span style="color:#00034a;font-weight:600;">Isi paket:</span>
                    @foreach(($d->items->isNotEmpty()?$d->items:$d->pembelianBarang) as $item)
                        @php $n = $d->items->isNotEmpty()?($item->barang->nama??'Barang').': '.(rtrim(rtrim(number_format($item->jumlah_per_paket,2,',','.'),'0'),',').'/pk') : (($item->nama_barang??$item->nama??'Barang').': '.number_format($item->pivot->jumlah??0)); @endphp
                        {{ $n }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                    @endif
                </div>
                <div style="margin-top:10px;">
                    @if($d->status == 'selesai') <span class="badge badge-green">Selesai</span>
                    @elseif($d->status == 'berlangsung') <span class="badge badge-gold">Berlangsung</span>
                    @elseif($d->status == 'dibatalkan') <span class="badge" style="background:#fce8e6;color:#dc2626;">Dibatalkan</span>
                    @else <span class="badge badge-navy">Direncanakan</span>
                    @endif
                </div>
                <div class="mobile-data-card__actions">
                    <a href="{{ route('distribusi.show', $d) }}" class="btn btn-outline btn-sm">Detail</a>
                    @if(auth()->user()->isAdmin())<a href="{{ route('distribusi.edit', $d) }}" class="btn btn-outline btn-sm">Edit</a>@endif
                </div>
            </article>
            @empty
            <div class="mobile-data-card" style="text-align:center;color:#667085;">Belum ada distribusi.</div>
            @endforelse
        </div>
        <div style="margin-top:12px;">{{ $distribusi->links() ?? '' }}</div>
    </div>
</div>

@endsection
