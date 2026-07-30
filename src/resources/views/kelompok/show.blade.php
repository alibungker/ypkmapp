@extends('layouts.app')
@section('title', 'Detail Kelompok')
@section('subtitle', $kelompok->nama . ' — ' . $kelompok->daerah)

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:18px;flex-wrap:wrap;">
    <a href="{{ route('kelompok.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">➕ Tetapkan Ketua</a>
    @endif
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:16px;margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-value">{{ number_format($kelompok->penerima_count) }}</div>
        <div class="stat-label">Anggota Aktual</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="font-size:18px;color:#017723;">{{ optional($kelompok->ketuaUser)->name ?? 'Belum ditetapkan' }}</div>
        <div class="stat-label">Ketua Kelompok</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ number_format($kelompok->distribusi->count()) }}</div>
        <div class="stat-label">Distribusi</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="font-size:18px;">{{ $kelompok->desa ?: '-' }}</div>
        <div class="stat-label">Desa/Gampong</div>
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
        <h3 style="font-size:15px;font-weight:600;">📋 Informasi Kelompok</h3>
    </div>
    <div style="padding:20px;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
        <div><div class="form-label">Kode</div><strong>{{ $kelompok->kode }}</strong></div>
        <div><div class="form-label">Nama</div><strong>{{ $kelompok->nama }}</strong></div>
        <div><div class="form-label">Kabupaten/Kota</div>{{ $kelompok->daerah }}</div>
        <div><div class="form-label">Kecamatan</div>{{ $kelompok->kecamatan ?: '-' }}</div>
        <div><div class="form-label">Desa</div>{{ $kelompok->desa ?: '-' }}</div>
        <div><div class="form-label">Keterangan</div>{{ $kelompok->description ?: '-' }}</div>
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;gap:12px;align-items:center;">
        <h3 style="font-size:15px;font-weight:600;">👥 Anggota Kelompok</h3>
        <span class="badge badge-navy">{{ number_format($kelompok->penerima_count) }} anggota</span>
    </div>
    <div style="padding:16px 20px;overflow-x:auto;">
        <table class="table-data">
            <thead><tr><th>Nama</th><th>NIK</th><th>Pekerjaan</th><th>Desa</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($anggota as $p)
                <tr>
                    <td><strong>{{ $p->nama }}</strong></td>
                    <td style="font-family:monospace;">{{ auth()->user()->isAdmin() ? $p->nik : substr($p->nik, 0, 6).'******'.substr($p->nik, -4) }}</td>
                    <td>{{ $p->pekerjaan ?: '-' }}</td>
                    <td>{{ $p->desa ?: '-' }}</td>
                    <td>
                        @if($p->status === 'terverifikasi')<span class="badge badge-green">Terverifikasi</span>
                        @elseif($p->status === 'ditolak')<span class="badge" style="background:#fee2e2;color:#991b1b;">Ditolak</span>
                        @else<span class="badge badge-gold">Pending</span>@endif
                    </td>
                    <td><a href="{{ route('penerima.show', $p) }}" style="color:#017723;text-decoration:none;">Detail</a></td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:32px;color:#9ca3af;">Belum ada anggota.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:16px;">{{ $anggota->links() }}</div>
    </div>
</div>

<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
        <h3 style="font-size:15px;font-weight:600;">📦 Riwayat Distribusi</h3>
    </div>
    <div style="padding:16px 20px;overflow-x:auto;">
        <table class="table-data">
            <thead><tr><th>Kegiatan</th><th>Tanggal</th><th>Paket</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($kelompok->distribusi as $d)
                <tr>
                    <td><strong>{{ $d->nama_kegiatan }}</strong></td>
                    <td>{{ is_object($d->tanggal) ? $d->tanggal->format('d M Y') : date('d M Y', strtotime($d->tanggal)) }}</td>
                    <td>{{ number_format($d->jumlah_paket) }}</td>
                    <td>{{ ucfirst($d->status) }}</td>
                    <td><a href="{{ route('distribusi.show', $d) }}" style="color:#017723;text-decoration:none;">Detail</a></td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:32px;color:#9ca3af;">Belum ada distribusi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
