@extends('layouts.app')
@section('title', 'Manajemen Barang & Kegiatan')
@section('subtitle', 'Tambah, edit, hapus item anggaran kegiatan & pembelian barang')
@section('content')
@if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
@if($errors->any())<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ $errors->first() }}</div>@endif

{{-- Tabs --}}
<div style="display:flex;gap:4px;margin-bottom:20px;">
    <button onclick="showTab('kegiatan')" id="tab_kegiatan" style="padding:10px 20px;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-size:14px;background:#00034a;color:white;">📦 Kegiatan</button>
    <button onclick="showTab('pembelian')" id="tab_pembelian" style="padding:10px 20px;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-size:14px;background:#e5e7eb;color:#374151;">📋 Pembelian Barang</button>
</div>

{{-- Tab KEGIATAN --}}
<div id="panel_kegiatan">
    <div class="card">
        <div class="barang-card-header">
            <div><span class="section-kicker">RENCANA & REALISASI</span><h3>Daftar Kegiatan</h3></div>
            <button type="button" class="btn btn-primary" data-open-modal="createKegiatanModal">+ Tambah Kegiatan</button>
        </div>
            <div style="padding:14px 18px;overflow-x:auto;">
                <table class="table-data">
                    <thead><tr><th>No</th><th>Nama Kegiatan</th><th>Target</th><th>Anggaran</th><th>Realisasi</th><th>%</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse($anggarans as $i => $a)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td style="font-weight:500;">{{ $a->nama_anggaran ?? $a->kategori }}</td>
                            <td>{{ $a->target_paket ? number_format($a->target_paket).' '.$a->satuan : '-' }}</td>
                            <td>Rp {{ number_format($a->anggaran,0,',','.') }}</td>
                            <td>Rp {{ number_format($a->realisasi,0,',','.') }}</td>
                            <td>{{ $a->anggaran > 0 ? round(($a->realisasi/$a->anggaran)*100,1) : 0 }}%</td>
                            <td><span class="badge badge-green">✅ {{ $a->catatan ?? 'Lunas' }}</span></td>
                            <td style="white-space:nowrap;">
                                <button onclick="editKegiatan({{ $a->id }})" style="color:#00034a;border:none;background:none;cursor:pointer;font-size:12px;">✏️</button>
                                <form method="POST" action="{{ route('barang.kegiatan.destroy', $a) }}" style="display:inline;" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button style="color:#dc2626;border:none;background:none;cursor:pointer;font-size:12px;">🗑️</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" style="padding:24px;text-align:center;color:#9ca3af;">Belum ada kegiatan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
</div>

{{-- Tab PEMBELIAN BARANG --}}
<div id="panel_pembelian" style="display:none;">
    <div class="card">
        <div class="barang-card-header">
            <div><span class="section-kicker">PENGADAAN BARANG</span><h3>Daftar Pembelian Barang</h3></div>
            <button type="button" class="btn btn-primary" data-open-modal="createPembelianModal">+ Pembelian Barang</button>
        </div>
        <div style="padding:14px 18px;overflow-x:auto;">
            <table class="table-data">
                <thead><tr><th>No</th><th>Nama Barang</th><th>Batch</th><th>Qty Renc</th><th>Qty Beli</th><th>Belum</th><th>Harga</th><th>Anggaran</th><th>Realisasi</th><th>Sisa</th><th>%</th><th></th></tr></thead>
                <tbody>
                    @forelse($pembelian as $i => $p)
                    <tr><td>{{ $i+1 }}</td><td style="font-weight:500;font-size:13px;">{{ $p->nama_barang }}</td><td>{{ $p->batch ?? '-' }}</td><td>{{ number_format($p->qty_rencana) }}</td><td>{{ number_format($p->qty_terbeli) }}</td><td>{{ $p->qty_belum > 0 ? number_format($p->qty_belum) : '0' }}</td><td>{{ number_format($p->harga_satuan) }}</td><td>{{ number_format($p->anggaran) }}</td><td>{{ number_format($p->realisasi) }}</td><td>{{ number_format($p->sisa) }}</td><td>{{ $p->persen_real }}%</td><td style="white-space:nowrap;"><button onclick="editPembelian({{ $p->id }})" class="icon-action">✏️</button><form method="POST" action="{{ route('barang.pembelian.destroy', $p) }}" style="display:inline;" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="icon-action icon-action--danger">🗑️</button></form></td></tr>
                    @empty<tr><td colspan="12" class="empty-row">Belum ada item barang</td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('barang._create-modals')

{{-- Edit Modal Kegiatan --}}
<div id="editModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;" onclick="if(event.target===this)closeEdit()">
    <div style="background:white;border-radius:12px;padding:24px;width:90%;max-width:500px;max-height:90vh;overflow-y:auto;" onclick="event.stopPropagation()">
        <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">✏️ Edit</h3>
        <div id="editFormContainer"></div>
    </div>
</div>
@endsection

@section('styles')
<style>
.barang-card-header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:16px 18px;border-bottom:1px solid #e5e7eb}.barang-card-header h3{font-size:17px;margin:2px 0 0;color:#111827}.section-kicker,.create-modal__eyebrow{font-size:10px;letter-spacing:.12em;font-weight:800;color:#017723}.icon-action{border:0;background:none;cursor:pointer;padding:6px}.icon-action--danger{color:#dc2626}.empty-row{padding:28px;text-align:center;color:#9ca3af}.create-modal{display:none;position:fixed;inset:0;z-index:1200;align-items:center;justify-content:center;padding:20px}.create-modal.is-open{display:flex}.create-modal__backdrop{position:absolute;inset:0;background:rgba(0,3,74,.66);backdrop-filter:blur(4px)}.create-modal__panel{position:relative;width:min(680px,100%);max-height:calc(100dvh - 40px);overflow:auto;overscroll-behavior:contain;background:#fff;border-radius:18px;box-shadow:0 24px 80px rgba(0,3,74,.3)}.create-modal__header{position:sticky;top:0;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;gap:20px;padding:20px 24px;border-bottom:1px solid #e5e7eb;background:#fff}.create-modal__header h2{margin:3px 0 0;color:#00034a;font-size:21px}.create-modal__close{border:0;background:#f3f4f6;border-radius:50%;min-width:44px;width:44px;height:44px;font-size:24px;cursor:pointer}.create-modal__form{display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:24px}.form-field--full,.create-modal__actions{grid-column:1/-1}.form-label span{color:#dc2626}.form-hint{display:block;margin-top:5px;color:#667085;font-size:11px}.auto-total{background:#f5f7fa;color:#00034a;font-weight:700}.create-modal__actions{display:flex;justify-content:flex-end;gap:10px;padding-top:6px}.btn-secondary{background:#eef0f4;color:#374151}body.modal-open{overflow:hidden}
@media(max-width:640px){.barang-card-header{align-items:stretch;flex-direction:column}.barang-card-header .btn{width:100%}.create-modal{padding:0;align-items:flex-end}.create-modal__panel{border-radius:18px 18px 0 0;max-height:94dvh}.create-modal__form{grid-template-columns:1fr;padding:18px}.form-field--full,.create-modal__actions{grid-column:1}.create-modal__actions{background:#fff;border-top:1px solid #e5e7eb;margin:8px -18px -18px;padding:14px 18px calc(18px + env(safe-area-inset-bottom));box-shadow:0 -6px 16px rgba(0,3,74,.06)}.create-modal__actions .btn{flex:1;min-height:48px}}
</style>
@endsection

@section('scripts')
<script>
function showTab(tab) {
    document.getElementById('panel_kegiatan').style.display = tab === 'kegiatan' ? 'block' : 'none';
    document.getElementById('panel_pembelian').style.display = tab === 'pembelian' ? 'block' : 'none';
    document.getElementById('tab_kegiatan').style.background = tab === 'kegiatan' ? '#00034a' : '#e5e7eb';
    document.getElementById('tab_pembelian').style.background = tab === 'pembelian' ? '#00034a' : '#e5e7eb';
    document.getElementById('tab_kegiatan').style.color = tab === 'kegiatan' ? 'white' : '#374151';
    document.getElementById('tab_pembelian').style.color = tab === 'pembelian' ? 'white' : '#374151';
}

// Modal create: focus management, Escape, backdrop, and validation reopen.
let activeCreateModal = null;
let createModalTrigger = null;
function openCreateModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    createModalTrigger = document.activeElement;
    activeCreateModal = modal;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
    const first = modal.querySelector('input:not([type="hidden"]), select, button');
    if (first) first.focus();
}
function closeCreateModal() {
    if (!activeCreateModal) return;
    activeCreateModal.classList.remove('is-open');
    activeCreateModal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    activeCreateModal = null;
    if (createModalTrigger) createModalTrigger.focus();
}
document.querySelectorAll('[data-open-modal]').forEach(button => button.addEventListener('click', () => openCreateModal(button.dataset.openModal)));
document.querySelectorAll('[data-close-modal]').forEach(button => button.addEventListener('click', closeCreateModal));

function calculatePurchaseTotals(form) {
    const price = Math.max(0, Number(form.elements.harga_satuan?.value) || 0);
    const planned = Math.max(0, Number(form.elements.qty_rencana?.value) || 0);
    const purchased = Math.max(0, Number(form.elements.qty_terbeli?.value) || 0);
    if (form.elements.anggaran) form.elements.anggaran.value = price * planned;
    if (form.elements.realisasi) form.elements.realisasi.value = price * purchased;
}
function bindPurchaseCalculator(form) {
    if (!form || form.dataset.calculatorBound === 'true') return;
    form.dataset.calculatorBound = 'true';
    ['harga_satuan', 'qty_rencana', 'qty_terbeli'].forEach(name => form.elements[name]?.addEventListener('input', () => calculatePurchaseTotals(form)));
    calculatePurchaseTotals(form);
}
document.querySelectorAll('form[action$="/barang/pembelian"], form[data-purchase-calculator]').forEach(bindPurchaseCalculator);

document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && activeCreateModal) closeCreateModal();
    if (event.key !== 'Tab' || !activeCreateModal) return;
    const focusable = [...activeCreateModal.querySelectorAll('button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled])')];
    if (!focusable.length) return;
    const first = focusable[0], last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
    else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
});

const invalidForm = @json(old('form_type'));
const requestedTab = @json(request()->query('tab'));
const activeTab = invalidForm || (requestedTab === 'pembelian' ? 'pembelian' : 'kegiatan');
showTab(activeTab);
if (invalidForm === 'kegiatan') openCreateModal('createKegiatanModal');
if (invalidForm === 'pembelian') openCreateModal('createPembelianModal');

function closeEdit() { document.getElementById('editModal').style.display = 'none'; }
window.editKegiatan = function(id) {
    fetch('/barang/kegiatan/' + id + '/edit')
        .then(r => r.text())
        .then(html => { document.getElementById('editFormContainer').innerHTML = html; document.getElementById('editModal').style.display = 'flex'; })
        .catch(() => alert('Gagal memuat form edit'));
};
window.editPembelian = function(id) {
    fetch('/barang/pembelian/' + id + '/edit')
        .then(r => r.text())
        .then(html => { document.getElementById('editFormContainer').innerHTML = html; document.getElementById('editModal').style.display = 'flex'; bindPurchaseCalculator(document.querySelector('#editFormContainer form[data-purchase-calculator]')); })
        .catch(() => alert('Gagal memuat form edit'));
};
</script>
@endsection
