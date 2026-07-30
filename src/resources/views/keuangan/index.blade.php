@extends('layouts.app')
@section('title', 'Keuangan')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-green-50 rounded-lg p-4 text-center">
        <div class="text-sm text-gray-600">Dana Masuk</div>
        <div class="text-xl font-bold text-green">Rp {{ number_format($total_masuk ?? 0,0,',','.') }}</div>
    </div>
    <div class="bg-red-50 rounded-lg p-4 text-center">
        <div class="text-sm text-gray-600">Nilai Bantuan</div>
        <div class="text-xl font-bold text-red-600">Rp {{ number_format($total_bantuan ?? 0,0,',','.') }}</div>
    </div>
    <div class="bg-yellow-50 rounded-lg p-4 text-center">
        <div class="text-sm text-gray-600">Biaya Operasional</div>
        <div class="text-xl font-bold text-yellow-700">Rp {{ number_format($total_biaya ?? 0,0,',','.') }}</div>
    </div>
    <div class="bg-navy text-white rounded-lg p-4 text-center">
        <div class="text-sm text-white/70">Sisa Dana</div>
        <div class="text-xl font-bold">Rp {{ number_format($sisa ?? 0,0,',','.') }}</div>
    </div>
</div>

<div class="bg-white rounded-xl border p-6">
    <h3 class="font-semibold mb-4">📥 Riwayat Dana Masuk</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">Donatur</th><th class="pb-2">Tanggal</th><th class="pb-2">Jumlah</th><th class="pb-2">Jenis</th></tr></thead>
            <tbody>
                @forelse($dana_masuk ?? [] as $d)
                <tr class="border-b"><td class="py-2">{{ $d->donatur }}</td><td class="py-2 text-gray-600">{{ $d->tanggal_masuk->format('d M Y') }}</td><td class="py-2 font-medium">Rp {{ number_format($d->jumlah,0,',','.') }}</td><td class="py-2">{{ $d->jenis }}</td></tr>
                @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-400">Belum ada data dana masuk</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
