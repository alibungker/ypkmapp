@extends('layouts.app')
@section('title', 'Detail Penerima')
@section('content')
<div class="max-w-3xl">
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:15px;font-weight:600;">👤 Detail Penerima</h3>
            <div style="display:flex;gap:8px;">
                <a href="{{ route('penerima.edit', $penerima) }}" class="btn btn-outline btn-sm">✏️ Edit</a>
                <a href="{{ route('penerima.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
            </div>
        </div>
        <div style="padding:20px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">NIK</label>
                    <div style="font-size:15px;font-weight:600;margin-top:4px;">{{ $penerima->nik }}</div>
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">No. KK</label>
                    <div style="font-size:15px;font-weight:600;margin-top:4px;">{{ $penerima->no_kk ?? '-' }}</div>
                </div>
                <div style="grid-column:1/-1;">
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">Nama Lengkap</label>
                    <div style="font-size:18px;font-weight:700;color:#00034a;margin-top:4px;">{{ $penerima->nama }}</div>
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">Tempat / Tanggal Lahir</label>
                    <div style="font-size:14px;margin-top:4px;">{{ $penerima->tempat_lahir ?? '-' }} {{ $penerima->tanggal_lahir ? '/ '.($penerima->tanggal_lahir instanceof \Carbon\Carbon ? $penerima->tanggal_lahir->format('d M Y') : date('d M Y', strtotime($penerima->tanggal_lahir))) : '' }}</div>
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">Jenis Kelamin</label>
                    <div style="font-size:14px;margin-top:4px;">{{ $penerima->jenis_kelamin == 'L' ? 'Laki-laki' : ($penerima->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</div>
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">Pekerjaan</label>
                    <div style="font-size:14px;margin-top:4px;">{{ $penerima->pekerjaan ?? '-' }}</div>
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">No. HP</label>
                    <div style="font-size:14px;margin-top:4px;">{{ $penerima->phone }}</div>
                </div>
                <div style="grid-column:1/-1;">
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">Alamat</label>
                    <div style="font-size:14px;margin-top:4px;">{{ $penerima->alamat }}</div>
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">Kabupaten</label>
                    <div style="font-size:14px;margin-top:4px;">{{ $penerima->kabupaten }}</div>
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">Kecamatan</label>
                    <div style="font-size:14px;margin-top:4px;">{{ $penerima->kecamatan }}</div>
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">Desa</label>
                    <div style="font-size:14px;margin-top:4px;">{{ $penerima->desa }}</div>
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">Jumlah Keluarga</label>
                    <div style="font-size:14px;margin-top:4px;">{{ $penerima->jumlah_keluarga }} org</div>
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">Kelompok</label>
                    <div style="font-size:14px;margin-top:4px;font-weight:500;">{{ $penerima->kelompok->nama ?? '-' }}</div>
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">Sumber Data</label>
                    <div style="font-size:14px;margin-top:4px;"><span style="background:#e8e8f0;padding:2px 8px;border-radius:6px;font-size:12px;">{{ $penerima->sumber_data }}</span></div>
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">Status</label>
                    <div style="margin-top:4px;">
                        @if($penerima->status == 'terverifikasi') <span class="badge badge-green">✅ Terverifikasi</span>
                        @elseif($penerima->status == 'pending') <span class="badge badge-gold">⏳ Pending</span>
                        @else <span style="background:#fce8e6;color:#dc2626;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">❌ Ditolak</span>
                        @endif
                    </div>
                </div>
                @if($penerima->verified_by)
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;">Diverifikasi Oleh</label>
                    <div style="font-size:14px;margin-top:4px;">{{ $penerima->verifikator->name ?? 'System' }}</div>
                </div>
                @endif
            </div>

            {{-- Panel Verifikasi (Relawan / Admin) — Ketua Kelompok TIDAK bisa verifikasi --}}
            @if($penerima->status == 'pending' && !auth()->user()->isKetuaKelompok())
            <div style="margin-top:20px;padding:16px;background:#fef7e6;border:1px solid #f0dcae;border-radius:10px;">
                <div style="font-size:14px;font-weight:600;margin-bottom:10px;">🔍 Verifikasi Relawan</div>
                <p style="font-size:12px;color:#6b7280;margin-bottom:10px;">Data diajukan oleh <strong>{{ $penerima->sumber_data == 'ketua_kelompok' ? 'Ketua Kelompok' : $penerima->sumber_data }}</strong>. Pastikan data sesuai KTP.</p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <form method="POST" action="{{ route('penerima.verify', $penerima) }}">
                        @csrf
                        <input type="hidden" name="status" value="terverifikasi">
                        <button class="btn btn-sm" style="background:#017723;color:white;">✅ Setujui (Verifikasi)</button>
                    </form>
                    <form method="POST" action="{{ route('penerima.verify', $penerima) }}" onsubmit="return confirm('Tolak penerima ini?')">
                        @csrf
                        <input type="hidden" name="status" value="ditolak">
                        <button class="btn btn-sm" style="background:#dc2626;color:white;">❌ Tolak</button>
                    </form>
                </div>
            </div>

            {{-- Checklist TERIMA BANTUAN oleh relawan (setelah terverifikasi) --}}
            @elseif($penerima->status == 'terverifikasi' && !auth()->user()->isKetuaKelompok())
            <div style="margin-top:20px;padding:16px;background:#e8f5ec;border:1px solid #c6e6d0;border-radius:10px;">
                <div style="font-size:14px;font-weight:600;margin-bottom:10px;">
                    @if($penerima->terima_bantuan)
                    ✅ PENERIMA SUDAH MENERIMA BANTUAN
                    @else
                    📋 Checklist Terima Bantuan
                    @endif
                </div>
                <p style="font-size:12px;color:#6b7280;margin-bottom:10px;">
                    @if($penerima->terima_bantuan)
                    Telah dichecklist oleh relawan pada {{ is_object($penerima->terima_at) ? $penerima->terima_at->format('d M Y H:i') : date('d M Y H:i', strtotime($penerima->terima_at)) }}.
                    @else
                    Pastikan penerima sudah menerima bantuan secara langsung.
                    @endif
                </p>
                <form method="POST" action="{{ route('penerima.terima-bantuan', $penerima) }}">
                    @csrf
                    <button class="btn btn-sm" style="{{ $penerima->terima_bantuan ? 'background:#dc2626;color:white;' : 'background:#017723;color:white;' }}">
                        {{ $penerima->terima_bantuan ? '↩️ Batalkan Checklist' : '✅ Terima Bantuan' }}
                    </button>
                </form>
            </div>
            @elseif($penerima->status == 'terverifikasi' && auth()->user()->isKetuaKelompok())
            <div style="margin-top:20px;padding:16px;background:#f0f0f0;border:1px solid #e5e7eb;border-radius:10px;">
                <div style="font-size:14px;font-weight:600;">⏳ Menunggu Checklist Relawan</div>
                <p style="font-size:12px;color:#6b7280;margin-top:4px;">Data sudah diverifikasi. Menunggu relawan melakukan checklist terima bantuan.</p>
            </div>
        </div>
    </div>
</div>
@endsection
