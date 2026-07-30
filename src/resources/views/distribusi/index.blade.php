@extends('layouts.app')
@section('title', 'Distribusi')
@section('content')
<div class="bg-white rounded-xl border p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold">🚚 Distribusi Bantuan</h3>
        <a href="#" class="px-4 py-2 bg-navy text-white rounded-lg text-sm font-medium">+ Buat Jadwal</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b"><th class="pb-3">Kegiatan</th><th class="pb-3">Daerah</th><th class="pb-3">Paket</th><th class="pb-3">Nilai</th><th class="pb-3">Tanggal</th><th class="pb-3">Status</th></tr></thead>
            <tbody>
                @forelse($distribusi ?? [] as $d)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 font-medium">{{ $d->nama_kegiatan }}</td>
                    <td class="py-3">{{ $d->kelompok->daerah ?? '-' }}</td>
                    <td class="py-3">{{ $d->jumlah_paket }}</td>
                    <td class="py-3">Rp {{ number_format($d->estimasi_nilai_total,0,',','.') }}</td>
                    <td class="py-3">{{ $d->tanggal->format('d M Y') }}</td>
                    <td class="py-3">
                        @if($d->status == 'selesai') <span class="text-green-600 bg-green-50 px-2 py-1 rounded-full text-xs">✅ Selesai</span>
                        @elseif($d->status == 'berlangsung') <span class="text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full text-xs">⏳ Berlangsung</span>
                        @else <span class="text-navy bg-navy/5 px-2 py-1 rounded-full text-xs">📋 Rencana</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-8 text-center text-gray-400">Belum ada distribusi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
