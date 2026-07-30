@extends('layouts.app')
@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan aktivitas penyaluran bantuan')

@section('content')
{{-- Stats --}}
<div class="mobile-stack" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px;">
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#e8e8f0;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#00034a;"><x-icon name="users" size="20"/></div>
            <span style="font-size:13px;color:#6b7280;">Penerima</span>
        </div>
        <div class="stat-value">{{ number_format($stats['penerima']) }}</div>
        <div style="display:flex;gap:12px;margin-top:6px;font-size:12px;">
            <span style="color:#017723;">✅ {{ $stats['penerima_terverifikasi'] }} siap</span>
            <span style="color:#b07d14;">⏳ {{ $stats['penerima_pending'] }} pending</span>
        </div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#e8f5ec;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#017723;"><x-icon name="group" size="20"/></div>
            <span style="font-size:13px;color:#6b7280;">Kelompok</span>
        </div>
        <div class="stat-value" style="color:#017723;">{{ number_format($stats['kelompok']) }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">📍 Tersebar di Aceh</div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#fef7e6;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#9a6b0d;"><x-icon name="truck" size="20"/></div>
            <span style="font-size:13px;color:#6b7280;">Distribusi</span>
        </div>
        <div class="stat-value" style="color:#b07d14;">{{ number_format($stats['distribusi']) }}</div>
        <div style="display:flex;gap:12px;margin-top:6px;font-size:12px;">
            <span style="color:#017723;">✅ {{ $stats['distribusi_selesai'] }} selesai</span>
            <span style="color:#b07d14;">⏳ {{ $stats['distribusi_berlangsung'] }} berlangsung</span>
        </div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#fef2f2;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#b42318;"><x-icon name="wallet" size="20"/></div>
            <span style="font-size:13px;color:#6b7280;">Nilai Bantuan</span>
        </div>
        <div class="stat-value">Rp {{ number_format($stats['total_nilai_bantuan'],0,',','.') }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">💵 Dana masuk: Rp {{ number_format($stats['total_dana_masuk'],0,',','.') }}</div>
    </div>
</div>

{{-- Two columns --}}
<div class="mobile-stack" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    {{-- Distribusi Terbaru --}}
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:15px;font-weight:600;display:flex;align-items:center;gap:8px;"><x-icon name="truck"/> Distribusi terbaru</h3>
            <a href="{{ route('distribusi.index') }}" style="font-size:13px;color:#00034a;text-decoration:none;">Lihat Semua →</a>
        </div>
        <div class="card-body table-wrap">
            <table class="table-data">
                <thead><tr><th>Kegiatan</th><th>Daerah</th><th>Tanggal</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($distribusi_terbaru as $d)
                    <tr>
                        <td style="font-weight:500;">{{ $d->nama_kegiatan }}</td>
                        <td style="color:#6b7280;">{{ $d->kelompok->daerah ?? '-' }}</td>
                        <td style="color:#6b7280;">{{ is_object($d->tanggal) ? $d->tanggal->format('d M Y') : date('d M Y', strtotime($d->tanggal)) }}</td>
                        <td>
                            @if($d->status == 'selesai') <span class="badge badge-green">✅ Selesai</span>
                            @elseif($d->status == 'berlangsung') <span class="badge badge-gold">⏳ Berlangsung</span>
                            @else <span class="badge badge-navy">📋 Rencana</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding:32px;text-align:center;color:#9ca3af;">Belum ada distribusi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Progress --}}
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
            <h3 style="font-size:15px;font-weight:600;display:flex;align-items:center;gap:8px;"><x-icon name="report"/> Progres distribusi</h3>
        </div>
        <div style="padding:16px 20px;">
            @forelse($distribusi_terbaru->take(4) as $d)
            @php $persen = $d->status == 'selesai' ? 100 : ($d->status == 'berlangsung' ? 45 : 0); @endphp
            <div style="margin-bottom:14px;">
                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;">
                    <span>{{ $d->nama_kegiatan }}</span>
                    <span style="color:#6b7280;">{{ $d->jumlah_paket }} paket</span>
                </div>
                <div class="progress-bar"><div class="progress-fill" style="width:{{ $persen }}%;background:{{ $d->status == 'selesai' ? '#017723' : ($d->status == 'berlangsung' ? '#e5a820' : '#00034a') }};"></div></div>
            </div>
            @empty
            <p style="text-align:center;padding:24px;color:#9ca3af;">Belum ada data</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Ringkasan Keuangan --}}
<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
        <h3 style="font-size:15px;font-weight:600;display:flex;align-items:center;gap:8px;"><x-icon name="wallet"/> Ringkasan keuangan</h3>
    </div>
    <div class="mobile-stack" style="padding:20px;display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
        <div style="background:#e8f5ec;border-radius:8px;padding:16px;text-align:center;">
            <div style="font-size:13px;color:#6b7280;">Dana Masuk</div>
            <div style="font-size:20px;font-weight:700;color:#017723;margin-top:4px;">Rp {{ number_format($stats['total_dana_masuk'],0,',','.') }}</div>
        </div>
        <div style="background:#fef2f2;border-radius:8px;padding:16px;text-align:center;">
            <div style="font-size:13px;color:#6b7280;">Nilai Bantuan</div>
            <div style="font-size:20px;font-weight:700;color:#dc2626;margin-top:4px;">Rp {{ number_format($stats['total_nilai_bantuan'],0,',','.') }}</div>
        </div>
        <div style="background:#fef7e6;border-radius:8px;padding:16px;text-align:center;">
            <div style="font-size:13px;color:#6b7280;">Biaya Operasional</div>
            <div style="font-size:20px;font-weight:700;color:#b07d14;margin-top:4px;">Rp {{ number_format($stats['total_biaya'],0,',','.') }}</div>
        </div>
        <div style="background:#00034a;border-radius:8px;padding:16px;text-align:center;">
            <div style="font-size:13px;color:rgba(255,255,255,0.6);">Sisa Dana</div>
            <div style="font-size:20px;font-weight:700;color:white;margin-top:4px;">Rp {{ number_format($stats['total_dana_masuk'] - $stats['total_nilai_bantuan'] - $stats['total_biaya'],0,',','.') }}</div>
        </div>
    </div>
</div>
@endsection
