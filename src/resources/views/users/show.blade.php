@extends('layouts.app')
@section('title', 'Biodata: ' . $user->name)
@section('content')
<div style="max-width:800px;">
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:15px;font-weight:600;">👤 Biodata User</h3>
            <div style="display:flex;gap:8px;">
                <a href="{{ route('users.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
            </div>
        </div>
        <div style="padding:20px;display:grid;grid-template-columns:200px 1fr;gap:24px;">
            {{-- Foto --}}
            <div style="text-align:center;">
                <div style="width:140px;height:140px;margin:0 auto 12px;border-radius:50%;overflow:hidden;border:4px solid #e5e7eb;background:#f0f0f0;display:flex;align-items:center;justify-content:center;">
                    @if($user->foto)
                    <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto" style="width:100%;height:100%;object-fit:cover;">
                    @else
                    <div style="font-size:48px;color:#9ca3af;">👤</div>
                    @endif
                </div>
                <div style="font-size:13px;color:#6b7280;">{{ $user->role == 'admin' ? '👑 Admin' : ($user->role == 'relawan' ? '🤝 Relawan' : '👤 Ketua Kelompok') }}</div>
                <div style="font-size:12px;color:#e5a820;margin-top:4px;">🔒 {{ $user->wilayahLabel() }}</div>
            </div>
            {{-- Detail --}}
            <div>
                <table style="width:100%;font-size:14px;border-collapse:collapse;">
                    <tr><td style="padding:8px 0;color:#6b7280;width:35%;">NIK</td>
                        <td style="font-weight:600;">{{ $user->nik ?? '-' }}</td></tr>
                    <tr><td style="padding:8px 0;color:#6b7280;">Nama Lengkap</td>
                        <td style="font-weight:600;color:#00034a;font-size:16px;">{{ $user->name }}</td></tr>
                    <tr><td style="padding:8px 0;color:#6b7280;">Email</td>
                        <td>{{ $user->email }}</td></tr>
                    <tr><td style="padding:8px 0;color:#6b7280;">No. HP</td>
                        <td>{{ $user->phone ?? '-' }}</td></tr>
                    <tr><td style="padding:8px 0;color:#6b7280;">Jenis Kelamin</td>
                        <td>{{ $user->jenis_kelamin == 'L' ? 'Laki-laki' : ($user->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td></tr>
                    <tr><td style="padding:8px 0;color:#6b7280;">Tempat/Tgl Lahir</td>
                        <td>{{ $user->tempat_lahir ?? '-' }}{{ $user->tanggal_lahir ? ', ' . (is_object($user->tanggal_lahir) ? $user->tanggal_lahir->format('d M Y') : date('d M Y', strtotime($user->tanggal_lahir))) : '' }}</td></tr>
                    <tr><td style="padding:8px 0;color:#6b7280;">Alamat</td>
                        <td>{{ $user->alamat_lengkap ?? '-' }}</td></tr>
                    <tr><td style="padding:8px 0;color:#6b7280;">Wilayah Kerja</td>
                        <td style="font-weight:500;">🔒 {{ $user->wilayahLabel() }}</td></tr>
                    <tr><td style="padding:8px 0;color:#6b7280;">Status</td>
                        <td>@if($user->is_active) <span class="badge badge-green">✅ Aktif</span> @else <span style="background:#fce8e6;color:#dc2626;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">❌ Nonaktif</span> @endif</td></tr>
                    <tr><td style="padding:8px 0;color:#6b7280;">Bergabung</td>
                        <td style="color:#6b7280;">{{ $user->created_at ? (is_object($user->created_at) ? $user->created_at->format('d M Y') : date('d M Y', strtotime($user->created_at))) : '-' }}</td></tr>
                </table>
                {{-- Tombol Aksi --}}
                <div style="display:flex;gap:8px;margin-top:20px;padding-top:16px;border-top:1px solid #e5e7eb;">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">✏️ Edit Biodata</a>
                    @if(auth()->user()->isAdmin() && $user->id !== auth()->id())
                    <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus user ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline btn-sm">🗑️ Hapus</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
