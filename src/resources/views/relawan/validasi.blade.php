@extends('layouts.app')
@section('title', 'Validasi Terima Bantuan')
@section('subtitle', 'Checklist penerima yang sudah menerima bantuan secara langsung')
@section('content')
<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
        <h3 style="font-size:15px;font-weight:600;">✅ Validasi Terima Bantuan</h3>
        <span style="font-size:13px;color:#017723;">🔒 {{ auth()->user()->wilayahLabel() }}</span>
    </div>
    <div style="padding:16px 20px;overflow-x:auto;">
        @if($penerima->isEmpty())
        <div style="padding:32px;text-align:center;color:#9ca3af;">
            <div style="font-size:40px;margin-bottom:8px;">📋</div>
            <div style="font-weight:500;">Semua penerima sudah dichecklist</div>
            <div style="font-size:13px;margin-top:4px;">Tidak ada penerima terverifikasi yang menunggu validasi terima bantuan.</div>
        </div>
        @else
        <table class="table-data">
            <thead><tr><th>NIK</th><th>Nama</th><th>Kelompok</th><th>Wilayah</th><th>Diverifikasi Oleh</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($penerima as $p)
                <tr style="{{ $p->terima_bantuan ? 'background:#f0faf0;' : '' }}">
                    <td style="font-family:monospace;font-size:13px;color:#6b7280;">{{ $p->nik }}</td>
                    <td style="font-weight:500;">{{ $p->nama }}</td>
                    <td>{{ $p->kelompok->nama ?? '-' }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $p->desa }}, {{ $p->kecamatan }}</td>
                    <td style="color:#6b7280;">{{ $p->verifikator->name ?? '-' }}</td>
                    <td>
                        @if($p->terima_bantuan)
                        <span class="badge badge-green">✅ SUDAH TERIMA</span>
                        @else
                        <span class="badge badge-gold">⏳ Menunggu</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('penerima.show', $p) }}" class="btn btn-outline btn-sm" style="text-decoration:none;">🔍 Detail</a>
                        <form method="POST" action="{{ route('penerima.terima-bantuan', $p) }}" style="display:inline;">
                            @csrf
                            @if($p->terima_bantuan)
                            <button class="btn btn-sm" style="background:#dc2626;color:white;border:none;cursor:pointer;padding:6px 12px;border-radius:6px;font-size:13px;">↩️ Batalkan</button>
                            @else
                            <button class="btn btn-sm" style="background:#017723;color:white;border:none;cursor:pointer;padding:6px 12px;border-radius:6px;font-size:13px;">✅ Terima Bantuan</button>
                            @endif
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
