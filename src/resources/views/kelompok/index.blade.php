@extends('layouts.app')
@section('title', 'Data Kelompok')
@section('content')
<div class="bg-white rounded-xl border p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold">📋 Data Kelompok</h3>
        <button onclick="alert('Form tambah menyusul')" class="px-4 py-2 bg-navy text-white rounded-lg text-sm font-medium">+ Tambah</button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b"><th class="pb-3">Kode</th><th class="pb-3">Nama</th><th class="pb-3">Daerah</th><th class="pb-3">Anggota</th><th class="pb-3">Ketua</th></tr></thead>
            <tbody>
                @forelse($kelompoks ?? [] as $k)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3">{{ $k->kode }}</td>
                    <td class="py-3 font-medium">{{ $k->nama }}</td>
                    <td class="py-3">{{ $k->daerah }}</td>
                    <td class="py-3">{{ $k->penerima_count ?? 0 }} org</td>
                    <td class="py-3">-</td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-8 text-center text-gray-400">Belum ada kelompok</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
