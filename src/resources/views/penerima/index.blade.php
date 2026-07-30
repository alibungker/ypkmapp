@extends('layouts.app')
@section('title', 'Data Penerima')
@section('content')
<div class="bg-white rounded-xl border">
    <div class="px-5 py-4 border-b flex items-center justify-between flex-wrap gap-4">
        <h3 class="font-semibold">👥 Data Penerima Manfaat</h3>
        <div class="flex gap-2">
            <a href="{{ route('penerima.create') }}" class="px-4 py-2 bg-navy text-white rounded-lg text-sm font-medium hover:bg-navy/90">+ Tambah</a>
            <a href="{{ route('penerima.export') }}" class="px-4 py-2 border rounded-lg text-sm font-medium hover:bg-gray-50">📥 Export</a>
        </div>
    </div>
    <div class="p-4">
        <form class="flex gap-2 mb-4 flex-wrap">
            <input type="text" name="search" placeholder="Cari NIK/nama..." value="{{ request('search') }}" class="px-3 py-2 border rounded-lg text-sm flex-1 min-w-[200px]">
            <select name="status" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">Semua Status</option>
                <option value="pending" @selected(request('status')=='pending')>Pending</option>
                <option value="terverifikasi" @selected(request('status')=='terverifikasi')>Terverifikasi</option>
                <option value="ditolak" @selected(request('status')=='ditolak')>Ditolak</option>
            </select>
            <button class="px-4 py-2 border rounded-lg text-sm">🔍 Cari</button>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b"><th class="pb-3">NIK</th><th class="pb-3">Nama</th><th class="pb-3">Kelompok</th><th class="pb-3">Sumber</th><th class="pb-3">Status</th><th class="pb-3"></th></tr></thead>
                <tbody>
                    @forelse($penerima as $p)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 text-gray-500">{{ $p->nik }}</td>
                        <td class="py-3 font-medium">{{ $p->nama }}</td>
                        <td class="py-3">{{ $p->kelompok->nama ?? '-' }}</td>
                        <td class="py-3"><span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $p->sumber_data }}</span></td>
                        <td class="py-3">
                            @if($p->status == 'terverifikasi') <span class="text-green-600 bg-green-50 px-2 py-1 rounded-full text-xs font-medium">✅ Terverifikasi</span>
                            @elseif($p->status == 'pending') <span class="text-yellow-700 bg-yellow-50 px-2 py-1 rounded-full text-xs font-medium">⏳ Pending</span>
                            @else <span class="text-red-600 bg-red-50 px-2 py-1 rounded-full text-xs font-medium">❌ Ditolak</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <a href="{{ route('penerima.show', $p) }}" class="text-navy hover:underline text-sm">Detail</a>
                            <a href="{{ route('penerima.edit', $p) }}" class="text-gray-500 hover:text-navy ml-3 text-sm">Edit</a>
                            @if($p->status == 'pending')
                            <form method="POST" action="{{ route('penerima.verify', $p) }}" class="inline ml-3">
                                @csrf
                                <input type="hidden" name="status" value="terverifikasi">
                                <button class="text-green-600 hover:underline text-sm">✅ Verifikasi</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-gray-400">Belum ada data penerima</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $penerima->links() }}</div>
    </div>
</div>
@endsection
