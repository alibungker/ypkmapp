@extends('layouts.app')
@section('title', 'Dashboard PEDULI YPKM')
@section('subtitle', 'Ringkasan aktivitas penyaluran bantuan')

@section('content')
{{-- Stats --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-navy/10 rounded-lg flex items-center justify-center text-lg">👥</div>
            <span class="text-sm text-gray-500">Penerima</span>
        </div>
        <div class="text-2xl font-bold text-navy">{{ number_format($stats['penerima']) }}</div>
        <div class="flex gap-2 mt-1 text-xs">
            <span class="text-green-600">✅ {{ $stats['penerima_terverifikasi'] }} siap</span>
            <span class="text-yellow-600">⏳ {{ $stats['penerima_pending'] }} pending</span>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-green/10 rounded-lg flex items-center justify-center text-lg">📋</div>
            <span class="text-sm text-gray-500">Kelompok</span>
        </div>
        <div class="text-2xl font-bold text-green">{{ number_format($stats['kelompok']) }}</div>
        <div class="text-xs text-gray-500 mt-1">📍 Tersebar di Aceh</div>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-gold/10 rounded-lg flex items-center justify-center text-lg">🚚</div>
            <span class="text-sm text-gray-500">Distribusi</span>
        </div>
        <div class="text-2xl font-bold text-yellow-700">{{ number_format($stats['distribusi']) }}</div>
        <div class="flex gap-2 mt-1 text-xs">
            <span class="text-green-600">✅ {{ $stats['distribusi_selesai'] }} selesai</span>
            <span class="text-yellow-600">⏳ {{ $stats['distribusi_berlangsung'] }} berlangsung</span>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center text-lg">💰</div>
            <span class="text-sm text-gray-500">Nilai Bantuan</span>
        </div>
        <div class="text-2xl font-bold text-navy">Rp {{ number_format($stats['total_nilai_bantuan'], 0, ',', '.') }}</div>
        <div class="text-xs text-gray-500 mt-1">💵 Dana masuk: Rp {{ number_format($stats['total_dana_masuk'], 0, ',', '.') }}</div>
    </div>
</div>

{{-- Cards --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Distribusi Terbaru --}}
    <div class="bg-white rounded-xl border">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold">🚚 Distribusi Terbaru</h3>
            <a href="{{ route('distribusi.index') }}" class="text-sm text-navy hover:underline">Lihat Semua</a>
        </div>
        <div class="p-4">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500"><th class="pb-2">Kegiatan</th><th class="pb-2">Daerah</th><th class="pb-2">Tanggal</th><th class="pb-2">Status</th></tr></thead>
                <tbody>
                    @forelse($distribusi_terbaru as $d)
                    <tr class="border-t">
                        <td class="py-2 font-medium">{{ $d->nama_kegiatan }}</td>
                        <td class="py-2 text-gray-600">{{ $d->kelompok->daerah ?? '-' }}</td>
                        <td class="py-2 text-gray-600">{{ $d->tanggal->format('d M Y') }}</td>
                        <td class="py-2">
                            @if($d->status == 'selesai') <span class="text-green-600 bg-green-50 px-2 py-1 rounded-full text-xs font-medium">✅ Selesai</span>
                            @elseif($d->status == 'berlangsung') <span class="text-yellow-700 bg-yellow-50 px-2 py-1 rounded-full text-xs font-medium">⏳ Berlangsung</span>
                            @else <span class="text-navy bg-navy/5 px-2 py-1 rounded-full text-xs font-medium">📋 Rencana</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-8 text-center text-gray-400">Belum ada distribusi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Progress Distribusi --}}
    <div class="bg-white rounded-xl border">
        <div class="px-5 py-4 border-b">
            <h3 class="font-semibold">📊 Progress Distribusi</h3>
        </div>
        <div class="p-4 space-y-4">
            @forelse($distribusi_terbaru->take(4) as $d)
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span>{{ $d->nama_kegiatan }}</span>
                    <span class="text-gray-500">{{ $d->jumlah_paket }} paket</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    @php $persen = $d->status == 'selesai' ? 100 : ($d->status == 'berlangsung' ? 45 : 0); @endphp
                    <div class="h-2 rounded-full {{ $d->status == 'selesai' ? 'bg-green' : ($d->status == 'berlangsung' ? 'bg-gold' : 'bg-navy/30') }}" style="width: {{ $persen }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-center py-4">Belum ada data</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Ringkasan Keuangan --}}
<div class="mt-6 bg-white rounded-xl border">
    <div class="px-5 py-4 border-b">
        <h3 class="font-semibold">💰 Ringkasan Keuangan</h3>
    </div>
    <div class="p-4 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-green-50 rounded-lg p-4 text-center">
            <div class="text-sm text-gray-600">Dana Masuk</div>
            <div class="text-xl font-bold text-green">Rp {{ number_format($stats['total_dana_masuk'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-red-50 rounded-lg p-4 text-center">
            <div class="text-sm text-gray-600">Nilai Bantuan</div>
            <div class="text-xl font-bold text-red-600">Rp {{ number_format($stats['total_nilai_bantuan'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-yellow-50 rounded-lg p-4 text-center">
            <div class="text-sm text-gray-600">Biaya Operasional</div>
            <div class="text-xl font-bold text-yellow-700">Rp {{ number_format($stats['total_biaya'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-navy text-white rounded-lg p-4 text-center">
            <div class="text-sm text-white/70">Sisa Dana</div>
            <div class="text-xl font-bold">Rp {{ number_format($stats['total_dana_masuk'] - $stats['total_nilai_bantuan'] - $stats['total_biaya'], 0, ',', '.') }}</div>
        </div>
    </div>
</div>
@endsection
