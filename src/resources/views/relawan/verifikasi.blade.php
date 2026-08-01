@extends('layouts.app')
@section('title', 'Data & Validasi Penerima')
@section('subtitle', 'Verifikasi data baru dan checklist penerima bantuan')
@section('content')
@if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ session('error') }}</div>@endif
@if($errors->any())<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ $errors->first() }}</div>@endif

{{-- Search --}}
<form method="GET" style="margin-bottom:16px;">
    <div style="display:flex;gap:8px;align-items:center;">
        <input type="text" name="search" class="form-input" style="max-width:360px;" value="{{ request('search') }}" placeholder="🔍 Cari NIK atau Nama lengkap...">
        <button type="submit" class="btn btn-primary btn-sm">Cari</button>
        @if(request('search'))<a href="{{ route('relawan.verifikasi') }}" class="btn btn-outline btn-sm" style="text-decoration:none;">↩️ Reset</a>@endif
    </div>
</form>

{{-- Stats --}}
<div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
    <div style="flex:1;background:#fef7e6;border:1px solid #f0dcae;border-radius:10px;padding:16px;text-align:center;">
        <div style="font-size:24px;font-weight:800;color:#b07d14;">{{ $pending->count() }}</div>
        <div style="font-size:13px;color:#6b7280;">Menunggu Verifikasi</div>
    </div>
    <div style="flex:1;background:#e8f5ec;border:1px solid #c6e6d0;border-radius:10px;padding:16px;text-align:center;">
        <div style="font-size:24px;font-weight:800;color:#017723;">{{ $terverifikasi->count() }}</div>
        <div style="font-size:13px;color:#6b7280;">Menunggu Checklist</div>
    </div>
    <div style="flex:1;background:#e8e8f0;border:1px solid #d0d0e0;border-radius:10px;padding:16px;text-align:center;">
        <div style="font-size:24px;font-weight:800;color:#00034a;">{{ $pending->count() + $terverifikasi->count() }}</div>
        <div style="font-size:13px;color:#6b7280;">Total Perlu Diproses</div>
    </div>
</div>

{{-- === TAB 1: VERIFIKASI (Pending) === --}}
<div class="card" style="margin-bottom:20px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <h3 style="font-size:15px;font-weight:600;">🔍 Verifikasi Penerima</h3>
            <p style="font-size:12px;color:#6b7280;margin-top:2px;">Data diajukan oleh Ketua Kelompok — pastikan data sesuai KTP</p>
        </div>
        <span style="background:#fef7e6;color:#b07d14;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">{{ $pending->count() }} menunggu</span>
    </div>
    <div style="padding:16px 20px;overflow-x:auto;">
        @if($pending->isEmpty())
        <div style="padding:16px;text-align:center;color:#9ca3af;font-size:13px;">✅ Tidak ada data yang menunggu verifikasi</div>
        @else
        <form method="POST" action="{{ route('relawan.bulk-verify') }}" id="form-verifikasi">
            @csrf
            <table class="table-data">
                <thead><tr>
                    <th style="width:36px;"><input type="checkbox" id="select-all-pending" title="Pilih semua" style="cursor:pointer;"></th>
                    <th>NIK</th><th>Nama</th><th>Kelompok</th><th>Desa</th><th>Pengaju</th><th>Tanggal</th><th>Aksi</th>
                </tr></thead>
                <tbody>
                    @foreach($pending as $p)
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $p->id }}" class="cb-pending" style="cursor:pointer;"></td>
                        <td style="font-family:monospace;font-size:13px;color:#6b7280;"><x-masked-nik :value="$p->nik" /></td>
                        <td style="font-weight:500;">{{ $p->nama }}</td>
                        <td>{{ $p->kelompok->nama ?? '-' }}</td>
                        <td style="color:#6b7280;">{{ $p->desa }}</td>
                        <td><span class="badge badge-gold">👤 {{ ucfirst($p->sumber_data) }}</span></td>
                        <td style="color:#6b7280;font-size:13px;">{{ $p->created_at ? (is_object($p->created_at) ? $p->created_at->format('d/m') : date('d/m', strtotime($p->created_at))) : '-' }}</td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('penerima.show', $p) }}" class="btn btn-outline btn-sm" style="text-decoration:none;">🔍 Detail</a>
                            <button type="button" class="btn btn-sm single-approve" style="background:#017723;color:white;border:none;cursor:pointer;padding:6px 12px;border-radius:6px;font-size:13px;" data-id="{{ $p->id }}" data-route="{{ route('penerima.verify', $p) }}">✅ Setujui</button>
                            <button type="button" class="btn btn-sm single-reject" style="background:#dc2626;color:white;border:none;cursor:pointer;padding:6px 12px;border-radius:6px;font-size:13px;" data-id="{{ $p->id }}" data-route="{{ route('penerima.verify', $p) }}" data-nama="{{ $p->nama }}">❌ Tolak</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top:12px;display:flex;gap:8px;align-items:center;">
                <span id="selected-count-pending" style="font-size:13px;color:#6b7280;"></span>
                <button type="button" id="btn-bulk-approve-pending" class="btn btn-primary btn-sm" disabled>✅ Setujui Terpilih</button>
                <button type="button" id="btn-bulk-reject-pending" class="btn btn-sm" style="background:#dc2626;color:white;border:none;cursor:pointer;" disabled>❌ Tolak Terpilih</button>
            </div>
            <div style="margin-top:12px;">{{ $pending->links() }}</div>
        </form>
        @endif
    </div>
</div>

{{-- === TAB 2: VALIDASI TERIMA BANTUAN === --}}
<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <h3 style="font-size:15px;font-weight:600;">✅ Validasi Terima Bantuan</h3>
            <p style="font-size:12px;color:#6b7280;margin-top:2px;">Checklist penerima yang sudah menerima bantuan secara langsung</p>
        </div>
        <span style="background:#e8f5ec;color:#017723;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">{{ $terverifikasi->count() }} menunggu</span>
    </div>
    <div style="padding:16px 20px;overflow-x:auto;">
        @if($terverifikasi->isEmpty())
        <div style="padding:16px;text-align:center;color:#9ca3af;font-size:13px;">✅ Semua penerima sudah dichecklist</div>
        @else
        <form method="POST" action="{{ route('relawan.bulk-terima') }}" id="form-terima">
            @csrf
            <table class="table-data">
                <thead><tr>
                    <th style="width:36px;"><input type="checkbox" id="select-all-terima" title="Pilih semua" style="cursor:pointer;"></th>
                    <th>NIK</th><th>Nama</th><th>Kelompok</th><th>Desa</th><th>Verifikator</th><th>Status</th><th>Aksi</th>
                </tr></thead>
                <tbody>
                    @foreach($terverifikasi as $p)
                    <tr style="{{ $p->terima_bantuan ? 'background:#f0faf0;' : '' }}">
                        @if(!$p->terima_bantuan)
                        <td><input type="checkbox" name="ids[]" value="{{ $p->id }}" class="cb-terima" style="cursor:pointer;"></td>
                        @else
                        <td><span style="display:inline-block;width:14px;"></span></td>
                        @endif
                        <td style="font-family:monospace;font-size:13px;color:#6b7280;"><x-masked-nik :value="$p->nik" /></td>
                        <td style="font-weight:500;">{{ $p->nama }}</td>
                        <td>{{ $p->kelompok->nama ?? '-' }}</td>
                        <td style="color:#6b7280;">{{ $p->desa }}</td>
                        <td style="color:#6b7280;font-size:13px;">{{ $p->verifikator->name ?? '-' }}</td>
                        <td>
                            @if($p->terima_bantuan)
                            <span class="badge badge-green">✅ SUDAH TERIMA</span>
                            @else
                            <span class="badge badge-gold">⏳ Menunggu</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('penerima.show', $p) }}" class="btn btn-outline btn-sm" style="text-decoration:none;">🔍 Detail</a>
                            @if($p->terima_bantuan)
                            <button type="button" class="btn btn-sm single-batal" style="background:#dc2626;color:white;border:none;cursor:pointer;padding:6px 12px;border-radius:6px;font-size:13px;" data-route="{{ route('penerima.terima-bantuan', $p) }}">↩️ Batalkan</button>
                            @else
                            <button type="button" class="btn btn-sm single-terima" style="background:#017723;color:white;border:none;cursor:pointer;padding:6px 12px;border-radius:6px;font-size:13px;" data-route="{{ route('penerima.terima-bantuan', $p) }}">✅ Terima Bantuan</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top:12px;display:flex;gap:8px;align-items:center;">
                <span id="selected-count-terima" style="font-size:13px;color:#6b7280;"></span>
                <button type="button" id="btn-bulk-terima" class="btn btn-primary btn-sm" disabled>✅ Tandai Terima Terpilih</button>
            </div>
            <div style="margin-top:12px;">{{ $terverifikasi->links() }}</div>
        </form>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
// --- Select All + Bulk: Tab 1 (Pending Verifikasi) ---
const selectAllPending = document.getElementById('select-all-pending');
const cbsPending = () => document.querySelectorAll('.cb-pending');
const countPending  = document.getElementById('selected-count-pending');
const btnApprove    = document.getElementById('btn-bulk-approve-pending');
const btnReject     = document.getElementById('btn-bulk-reject-pending');
const formVerify    = document.getElementById('form-verifikasi');

function updateBulkPending() {
    const checked = [...cbsPending()].filter(c => c.checked).length;
    const total   = cbsPending().length;
    countPending.textContent = checked > 0 ? `${checked} dari ${total} dipilih` : '';
    if (btnApprove) btnApprove.disabled = checked === 0;
    if (btnReject)  btnReject.disabled  = checked === 0;
    if (selectAllPending) selectAllPending.checked = total > 0 && checked === total;
}
if (selectAllPending) selectAllPending.addEventListener('change', () => {
    cbsPending().forEach(cb => cb.checked = selectAllPending.checked);
    updateBulkPending();
});
document.addEventListener('change', e => { if (e.target.classList.contains('cb-pending')) updateBulkPending(); });

if (btnApprove) btnApprove.addEventListener('click', () => {
    if (!confirm('Setujui ' + [...cbsPending()].filter(c=>c.checked).length + ' data?')) return;
    // hapus input status ditolak jika ada, set status terverifikasi
    let hidden = formVerify.querySelector('input[name="status"][value="ditolak"]');
    if (hidden) hidden.remove();
    if (!formVerify.querySelector('input[name="status"]')) {
        const h = document.createElement('input'); h.type='hidden'; h.name='status'; h.value='terverifikasi';
        formVerify.appendChild(h);
    }
    formVerify.submit();
});
if (btnReject) btnReject.addEventListener('click', () => {
    if (!confirm('Tolak ' + [...cbsPending()].filter(c=>c.checked).length + ' data?')) return;
    let hidden = formVerify.querySelector('input[name="status"][value="terverifikasi"]');
    if (hidden) hidden.remove();
    if (!formVerify.querySelector('input[name="status"]')) {
        const h = document.createElement('input'); h.type='hidden'; h.name='status'; h.value='ditolak';
        formVerify.appendChild(h);
    }
    formVerify.submit();
});

// --- Select All + Bulk: Tab 2 (Terima Bantuan) ---
const selectAllTerima = document.getElementById('select-all-terima');
const cbsTerima = () => document.querySelectorAll('.cb-terima');
const countTerima = document.getElementById('selected-count-terima');
const btnTerima   = document.getElementById('btn-bulk-terima');
const formTerima  = document.getElementById('form-terima');

function updateBulkTerima() {
    const checked = [...cbsTerima()].filter(c => c.checked).length;
    const total   = cbsTerima().length;
    countTerima.textContent = checked > 0 ? `${checked} dari ${total} dipilih` : '';
    if (btnTerima) btnTerima.disabled = checked === 0;
    if (selectAllTerima) selectAllTerima.checked = total > 0 && checked === total;
}
if (selectAllTerima) selectAllTerima.addEventListener('change', () => {
    cbsTerima().forEach(cb => cb.checked = selectAllTerima.checked);
    updateBulkTerima();
});
document.addEventListener('change', e => { if (e.target.classList.contains('cb-terima')) updateBulkTerima(); });

if (btnTerima) btnTerima.addEventListener('click', () => {
    if (!confirm('Tandai terima bantuan untuk ' + [...cbsTerima()].filter(c=>c.checked).length + ' data?')) return;
    formTerima.submit();
});

// Submit aksi per penerima tanpa nested form (HTML valid).
function submitSingle(route, fields = {}) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = route;
    const csrf = document.createElement('input');
    csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    Object.entries(fields).forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden'; input.name = name; input.value = value;
        form.appendChild(input);
    });
    document.body.appendChild(form);
    form.submit();
}
document.querySelectorAll('.single-approve').forEach(btn => btn.addEventListener('click', () => {
    if (confirm('Setujui penerima ini?')) submitSingle(btn.dataset.route, {status:'terverifikasi'});
}));
document.querySelectorAll('.single-reject').forEach(btn => btn.addEventListener('click', () => {
    if (confirm('Tolak ' + btn.dataset.nama + '?')) submitSingle(btn.dataset.route, {status:'ditolak'});
}));
document.querySelectorAll('.single-terima').forEach(btn => btn.addEventListener('click', () => {
    if (confirm('Tandai penerima ini sudah menerima bantuan?')) submitSingle(btn.dataset.route);
}));
document.querySelectorAll('.single-batal').forEach(btn => btn.addEventListener('click', () => {
    if (confirm('Batalkan checklist penerimaan bantuan?')) submitSingle(btn.dataset.route);
}));
</script>
@endsection
