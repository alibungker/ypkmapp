@extends('layouts.app')
@section('title', 'Distribusi')
@section('content')
@if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif

{{-- Dashboard Stats --}}
<div class="mobile-stack" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:20px;">
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#e8f5ec;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#017723;font-size:20px;">👥</div>
            <span style="font-size:13px;color:#6b7280;">Kelompok</span>
        </div>
        <div class="stat-value" style="color:#017723;">{{ number_format($stats['kelompok']) }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">Kelompok penerima distribusi</div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#e8e8f0;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#00034a;font-size:20px;">📍</div>
            <span style="font-size:13px;color:#6b7280;">Titik Distribusi</span>
        </div>
        <div class="stat-value">{{ number_format($stats['titik']) }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">Total lokasi distribusi</div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#fef7e6;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#b07d14;font-size:20px;">📦</div>
            <span style="font-size:13px;color:#6b7280;">Paket</span>
        </div>
        <div class="stat-value" style="color:#b07d14;">{{ number_format($stats['paket']) }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">Total paket distribusi</div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#fef2f2;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#b42318;font-size:20px;">💰</div>
            <span style="font-size:13px;color:#6b7280;">Anggaran</span>
        </div>
        <div class="stat-value">Rp {{ number_format($stats['anggaran'],0,',','.') }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">Total estimasi nilai</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 style="font-size:15px;font-weight:600;">Distribusi bantuan</h3>
        <div style="display:flex;gap:8px;align-items:center;">
            <form method="GET" style="display:inline;">
                <select name="status" class="form-input" style="width:170px;font-size:13px;" onchange="this.form.submit()">
                    <option value="">Semua status</option>
                    <option value="direncanakan" {{ request('status')=='direncanakan' ? 'selected' : '' }}>📋 Direncanakan</option>
                    <option value="berlangsung" {{ request('status')=='berlangsung' ? 'selected' : '' }}>⏳ Berlangsung</option>
                    <option value="selesai" {{ request('status')=='selesai' ? 'selected' : '' }}>✅ Selesai</option>
                    <option value="dibatalkan" {{ request('status')=='dibatalkan' ? 'selected' : '' }}>❌ Dibatalkan</option>
                </select>
            </form>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('distribusi.create') }}" class="btn btn-primary btn-sm">+ Buat Distribusi</a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrap desktop-table">
        <table class="table-data">
            <thead><tr><th>Kegiatan</th><th>Kelompok</th><th>Ketua</th><th>Koordinat</th><th>Paket</th><th>Penerima</th><th>Nilai</th><th>Tanggal</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($distribusi ?? [] as $d)
                <tr>
                    <td style="font-weight:500;">{{ $d->nama_kegiatan }}</td>
                    <td>{{ $d->kelompok->nama ?? '-' }}</td>
                    <td style="color:#6b7280;">{{ optional(optional($d->kelompok)->ketuaUser)->name ?? '-' }}</td>
                    <td style="color:#6b7280;font-size:12px;font-family:monospace;">
                        @if($d->titik_koordinat) 📍 {{ $d->titik_koordinat }} @else <span style="color:#dc2626;">Belum diset</span> @endif
                    </td>
                    <td style="font-weight:600;">{{ number_format($d->jumlah_paket) }}</td>
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
                <tr><td colspan="10" style="padding:32px;text-align:center;color:#9ca3af;">Belum ada distribusi</td></tr>
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
