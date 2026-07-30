@extends('layouts.app')
@section('title', 'Data & Validasi Penerima')
@section('subtitle', 'Verifikasi data baru dan checklist penerima bantuan')
@section('content')
@if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ session('error') }}</div>@endif
@if($errors->any())<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ $errors->first() }}</div>@endif

<div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
    <div style="flex:1;background:#fef7e6;border:1px solid #f0dcae;border-radius:10px;padding:16px;text-align:center;">
        <div style="font-size:24px;font-weight:800;color:#b07d14;">{{ $pending->count() }}</div>
        <div style="font-size:13px;color:#6b7280;">Menunggu Verifikasi</div>
    </div>
    <div style="flex:1;background:#e8f5ec;border:1px solid #c6e6d0;border-radius:10px;padding:16px;text-align:center;">
        <div style="font-size:24px;font-weight:800;color:#017723;">{{ $terverifikasi->count() }}</div>
        <div style="font-size:13px;color:#6b7280;">Menunggu Checklist</div>
    </div>
    <div style="flex:1;background:#e8e8f0;border:1px solid #d0d0e0;border-radius:10px;padding:16px;text-align:center;">
        <div style="font-size:24px;font-weight:800;color:#00034a;">{{ $pending->count() + $terverifikasi->count() }}</div>
        <div style="font-size:13px;color:#6b7280;">Total Perlu Diproses</div>
    </div>
</div>

{{-- === TAB 1: VERIFIKASI (Pending) === --}}
<div class="card" style="margin-bottom:20px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <h3 style="font-size:15px;font-weight:600;">🔍 Verifikasi Penerima</h3>
            <p style="font-size:12px;color:#6b7280;margin-top:2px;">Data diajukan oleh Ketua Kelompok — pastikan data sesuai KTP</p>
        </div>
        <span style="background:#fef7e6;color:#b07d14;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">{{ $pending->count() }} menunggu</span>
    </div>
    <div style="padding:16px 20px;overflow-x:auto;">
        @if($pending->isEmpty())
        <div style="padding:16px;text-align:center;color:#9ca3af;font-size:13px;">✅ Tidak ada data yang menunggu verifikasi</div>
        @else
        <table class="table-data">
            <thead><tr><th>NIK</th><th>Nama</th><th>Kelompok</th><th>Desa</th><th>Pengaju</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($pending as $p)
                <tr>
                    <td style="font-family:monospace;font-size:13px;color:#6b7280;">{{ $p->nik }}</td>
                    <td style="font-weight:500;">{{ $p->nama }}</td>
                    <td>{{ $p->kelompok->nama ?? '-' }}</td>
                    <td style="color:#6b7280;">{{ $p->desa }}</td>
                    <td><span class="badge badge-gold">👤 {{ ucfirst($p->sumber_data) }}</span></td>
                    <td style="color:#6b7280;font-size:13px;">{{ $p->created_at ? (is_object($p->created_at) ? $p->created_at->format('d/m') : date('d/m', strtotime($p->created_at))) : '-' }}</td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('penerima.show', $p) }}" class="btn btn-outline btn-sm" style="text-decoration:none;">🔍 Detail</a>
                        <form method="POST" action="{{ route('penerima.verify', $p) }}" style="display:inline;">
                            @csrf <input type="hidden" name="status" value="terverifikasi">
                            <button class="btn btn-sm" style="background:#017723;color:white;border:none;cursor:pointer;padding:6px 12px;border-radius:6px;font-size:13px;">✅ Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('penerima.verify', $p) }}" style="display:inline;" onsubmit="return confirm('Tolak {{ $p->nama }}?')">
                            @csrf <input type="hidden" name="status" value="ditolak">
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

{{-- === TAB 2: VALIDASI TERIMA BANTUAN === --}}
<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <h3 style="font-size:15px;font-weight:600;">✅ Validasi Terima Bantuan</h3>
            <p style="font-size:12px;color:#6b7280;margin-top:2px;">Checklist penerima yang sudah menerima bantuan secara langsung</p>
        </div>
        <span style="background:#e8f5ec;color:#017723;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">{{ $terverifikasi->count() }} menunggu</span>
    </div>
    <div style="padding:16px 20px;overflow-x:auto;">
        @if($terverifikasi->isEmpty())
        <div style="padding:16px;text-align:center;color:#9ca3af;font-size:13px;">✅ Semua penerima sudah dichecklist</div>
        @else
        <table class="table-data">
            <thead><tr><th>NIK</th><th>Nama</th><th>Kelompok</th><th>Desa</th><th>Verifikator</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($terverifikasi as $p)
                <tr style="{{ $p->terima_bantuan ? 'background:#f0faf0;' : '' }}">
                    <td style="font-family:monospace;font-size:13px;color:#6b7280;">{{ $p->nik }}</td>
                    <td style="font-weight:500;">{{ $p->nama }}</td>
                    <td>{{ $p->kelompok->nama ?? '-' }}</td>
                    <td style="color:#6b7280;">{{ $p->desa }}</td>
                    <td style="color:#6b7280;font-size:13px;">{{ $p->verifikator->name ?? '-' }}</td>
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
