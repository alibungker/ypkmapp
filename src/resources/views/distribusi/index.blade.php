@extends('layouts.app')
@section('title', 'Distribusi')
@section('content')
@if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
        <h3 style="font-size:15px;font-weight:600;">🚚 Distribusi Bantuan</h3>
        <a href="{{ route('distribusi.create') }}" class="btn btn-primary btn-sm">+ Buat Distribusi</a>
    </div>
    <div style="padding:16px 20px;overflow-x:auto;">
        <table class="table-data">
            <thead><tr><th>Kegiatan</th><th>Kelompok</th><th>Ketua</th><th>Koordinat</th><th>Paket</th><th>Penerima</th><th>Nilai</th><th>Tanggal</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($distribusi ?? [] as $d)
                <tr>
                    <td style="font-weight:500;">{{ $d->nama_kegiatan }}</td>
                    <td>{{ $d->kelompok->nama ?? '-' }}</td>
                    <td style="color:#6b7280;">{{ $d->kelompok->ketua->nama ?? '-' }}</td>
                    <td style="color:#6b7280;font-size:12px;font-family:monospace;">
                        @if($d->titik_koordinat) 📍 {{ $d->titik_koordinat }} @else <span style="color:#dc2626;">Belum diset</span> @endif
                    </td>
                    <td style="font-weight:600;">{{ number_format($d->jumlah_paket) }}</td>
                    <td>👥 {{ number_format($d->kelompok->jumlah_anggota ?? 0) }}</td>
                    <td>Rp {{ number_format($d->estimasi_nilai_total,0,',','.') }}</td>
                    <td style="color:#6b7280;">{{ is_object($d->tanggal) ? $d->tanggal->format('d M Y') : date('d M Y', strtotime($d->tanggal)) }}</td>
                    <td>
                        @if($d->status == 'selesai') <span class="badge badge-green">✅ Selesai</span>
                        @elseif($d->status == 'berlangsung') <span class="badge badge-gold">⏳ Berlangsung</span>
                        @else <span class="badge badge-navy">📋 Rencana</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('distribusi.edit', $d) }}" style="color:#00034a;font-size:13px;padding:4px 8px;text-decoration:none;">✏️ Edit</a>
                        <form method="POST" action="{{ route('distribusi.destroy', $d) }}" style="display:inline;" onsubmit="return confirm('Hapus distribusi ini?')">
                            @csrf @method('DELETE')
                            <button style="color:#dc2626;border:none;background:none;cursor:pointer;font-size:13px;padding:4px 8px;">🗑️</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" style="padding:32px;text-align:center;color:#9ca3af;">Belum ada distribusi</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:12px;">{{ $distribusi->links() ?? '' }}</div>
    </div>
</div>
@endsection
