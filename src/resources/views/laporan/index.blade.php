@extends('layouts.app')
@section('title', 'Laporan')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="font-semibold mb-4">📊 Rekap Distribusi</h3>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">Daerah</th><th class="pb-2">Paket</th><th class="pb-2">Nilai</th><th class="pb-2">Penerima</th></tr></thead>
            <tbody>
                <tr class="border-b"><td>Aceh Tamiang</td><td>750</td><td>Rp 112,5 Jt</td><td>522 KK</td></tr>
                <tr class="border-b"><td>Pidie</td><td>300</td><td>Rp 45 Jt</td><td>281 KK</td></tr>
                <tr class="border-b"><td>Aceh Utara</td><td>200</td><td>Rp 30 Jt</td><td>198 KK</td></tr>
                <tr class="border-b"><td>Bireuen</td><td>150</td><td>Rp 22,5 Jt</td><td>156 KK</td></tr>
                <tr class="border-b"><td>Subulussalam</td><td>120</td><td>Rp 18 Jt</td><td>120 KK</td></tr>
                <tr class="border-b"><td>Aceh Besar</td><td>200</td><td>Rp 30 Jt</td><td>200 KK</td></tr>
                <tr class="font-semibold bg-gray-50"><td>Total</td><td>1.720</td><td>Rp 258 Jt</td><td>1.477 KK</td></tr>
            </tbody>
        </table>
        <div class="mt-4 flex gap-2">
            <button class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">📥 Export Excel</button>
            <button class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">🖨️ Cetak</button>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-6">
        <h3 class="font-semibold mb-4">💰 Rekap Keuangan</h3>
        <table class="w-full text-sm">
            <tr class="border-b"><td class="py-2">💰 Dana Masuk</td><td class="py-2 font-medium text-right text-green">Rp 235.000.000</td></tr>
            <tr class="border-b"><td class="py-2">📦 Nilai Bantuan</td><td class="py-2 font-medium text-right text-red-600">Rp 180.000.000</td></tr>
            <tr class="border-b"><td class="py-2">🚐 Biaya Operasional</td><td class="py-2 font-medium text-right text-yellow-700">Rp 12.000.000</td></tr>
            <tr class="font-semibold border-t-2"><td class="py-2">💵 Sisa Dana</td><td class="py-2 font-medium text-right text-navy">Rp 43.000.000</td></tr>
        </table>
        <div class="mt-4">
            <button class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">📥 Export Laporan Keuangan</button>
        </div>
    </div>
</div>
@endsection
