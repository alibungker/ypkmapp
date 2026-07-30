@extends('layouts.app')
@section('title', 'Verifikasi Penerima')
@section('subtitle', 'Setujui atau tolak data penerima yang diajukan oleh Ketua Kelompok')
@section('content')
<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
        <h3 style="font-size:15px;font-weight:600;">🔍 Verifikasi Penerima</h3>
    </div>
    <div style="padding:16px 20px;overflow-x:auto;">
        @if($penerima->isEmpty())
        <div style="padding:32px;text-align:center;color:#9ca3af;">
            <div style="font-size:40px;margin-bottom:8px;">✅</div>
            <div style="font-weight:500;">Tidak ada data yang menunggu verifikasi</div>
            <div style="font-size:13px;margin-top:4px;">Semua data penerima di wilayah {{ auth()->user()->wilayahLabel() }} sudah diverifikasi.</div>
        </div>
        @else
        <table class="table-data">
            <thead><tr><th>NIK</th><th>Nama</th><th>Diajukan Oleh</th><th>Kelompok</th><th>Wilayah</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($penerima as $p)
                <tr>
                    <td style="font-family:monospace;font-size:13px;color:#6b7280;">{{ $p->nik }}</td>
                    <td style="font-weight:500;">{{ $p->nama }}</td>
                    <td><span class="badge badge-gold">👤 {{ ucfirst($p->sumber_data) }}</span></td>
                    <td>{{ $p->kelompok->nama ?? '-' }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $p->desa }}, {{ $p->kecamatan }}</td>
                    <td style="color:#6b7280;">{{ $p->created_at ? (is_object($p->created_at) ? $p->created_at->format('d M Y') : date('d M Y', strtotime($p->created_at))) : '-' }}</td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('penerima.show', $p) }}" class="btn btn-outline btn-sm" style="text-decoration:none;">🔍 Detail</a>
                        <form method="POST" action="{{ route('penerima.verify', $p) }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="status" value="terverifikasi">
                            <button class="btn btn-sm" style="background:#017723;color:white;border:none;cursor:pointer;padding:6px 12px;border-radius:6px;font-size:13px;">✅ Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('penerima.verify', $p) }}" style="display:inline;" onsubmit="return confirm('Tolak {{ $p->nama }}?')">
                            @csrf
                            <input type="hidden" name="status" value="ditolak">
                            <button class="btn btn-sm" style="background:#dc2626;color:white;border:none;cursor:pointer;padding:6px 12px;border-radius:6px;font-size:13px;">❌ Tolak</button>
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
