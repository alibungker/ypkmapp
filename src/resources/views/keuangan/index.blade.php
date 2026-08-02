@extends('layouts.app')
@section('title', 'Keuangan')
@section('styles')
<style>
.finance-actions{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:20px 22px;margin-bottom:20px;background:white;border:1px solid #e5e7eb;border-radius:14px}.finance-actions h2{font-size:20px;color:#00034a;margin:3px 0}.finance-actions p{font-size:13px;color:#667085;margin:0}.finance-kicker{font-size:10px;letter-spacing:.14em;font-weight:800;color:#017723}.finance-actions__buttons{display:flex;gap:10px}.finance-modal{display:none;position:fixed;inset:0;z-index:1200;align-items:center;justify-content:center;padding:20px}.finance-modal.is-open{display:flex}.finance-modal__backdrop{position:absolute;inset:0;background:rgba(0,3,74,.68);backdrop-filter:blur(4px)}.finance-modal__panel{position:relative;width:min(650px,100%);max-height:calc(100dvh - 40px);overflow:auto;background:#fff;border-radius:18px;box-shadow:0 24px 80px rgba(0,3,74,.3)}.finance-modal__header{position:sticky;top:0;z-index:1;display:flex;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #e5e7eb;background:white}.finance-modal__header h2{font-size:21px;color:#00034a;margin:3px 0}.finance-modal__close{width:44px;height:44px;border:0;border-radius:50%;font-size:24px;cursor:pointer}.finance-modal__form{display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:24px}.form-field--full,.finance-modal__actions{grid-column:1/-1}.finance-modal__actions{display:flex;justify-content:flex-end;gap:10px}body.finance-modal-open{overflow:hidden}@media(max-width:700px){.finance-actions{align-items:stretch;flex-direction:column}.finance-actions__buttons{display:grid}.finance-modal{padding:0;align-items:flex-end}.finance-modal__panel{border-radius:18px 18px 0 0;max-height:94dvh}.finance-modal__form{grid-template-columns:1fr;padding:18px}.form-field--full,.finance-modal__actions{grid-column:1}.finance-modal__actions .btn{flex:1;min-height:48px}}
.transaction-history{margin-top:20px;overflow:hidden}.transaction-history__header{padding:18px 20px;border-bottom:1px solid #e5e7eb}.transaction-history__header h3{font-size:17px;color:#00034a;margin:3px 0}.transaction-history__header p{font-size:12px;color:#667085;margin:0}.transaction-table-wrap{width:100%;overflow-x:auto}.transaction-table{width:100%;min-width:860px;border-collapse:collapse}.transaction-table th,.transaction-table td{padding:13px 14px;border-bottom:1px solid #e5e7eb;text-align:left;vertical-align:top;font-size:13px}.transaction-table thead th{background:#00034a;color:white;font-size:11px;letter-spacing:.06em;text-transform:uppercase}.transaction-table tbody tr:nth-child(even){background:#f5f8f6}.transaction-table tbody tr:hover{background:#eef5f0}.donor-cell{font-weight:700;color:#00034a}.date-cell{white-space:nowrap;color:#4b5563}.amount-cell{white-space:nowrap;color:#017723;font-weight:800;font-variant-numeric:tabular-nums}.amount-col{text-align:right}.description-cell{color:#4b5563;max-width:260px}.transaction-type{display:inline-flex;padding:5px 9px;border-radius:999px;background:#e8f5ec;color:#017723;font-size:11px;font-weight:700}.transaction-actions{display:flex;gap:7px;align-items:center}.transaction-actions form{margin:0}.transaction-action{min-height:38px;padding:7px 11px;border-radius:7px;background:white;font-size:12px;font-weight:700;cursor:pointer}.transaction-action--edit{border:1px solid #c7c9dc;color:#00034a}.transaction-action--delete{border:1px solid #fecaca;color:#b42318}.mobile-transactions{display:none}.transaction-empty{text-align:center;padding:32px;color:#667085}
@media(max-width:767px){.desktop-transactions{display:none}.mobile-transactions{display:grid;gap:10px;padding:12px}.transaction-card{padding:15px;background:white;border:1px solid #e5e7eb;border-radius:11px}.transaction-card:nth-child(even){background:#f5f8f6}.transaction-card__top{display:grid;gap:10px}.transaction-card h4{font-size:14px;color:#00034a;margin:0}.transaction-card time{display:block;margin-top:3px;color:#667085;font-size:12px}.transaction-card__top strong{color:#017723;font-size:16px;font-variant-numeric:tabular-nums;white-space:nowrap}.transaction-card__meta{margin-top:11px}.transaction-card__meta p{margin:9px 0 0;color:#4b5563;font-size:13px}.transaction-card .transaction-actions{margin-top:13px;padding-top:12px;border-top:1px solid #e5e7eb}.transaction-card .transaction-action{min-height:44px;padding:9px 16px}}
</style>
@endsection
@section('content')
{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px;">
    <div style="background:#e8f5ec;border-radius:12px;padding:20px;text-align:center;">
        <div style="font-size:13px;color:#6b7280;">💰 Dana Masuk</div>
        <div style="font-size:28px;font-weight:800;color:#017723;margin-top:4px;">Rp {{ number_format($total_masuk ?? 0,0,',','.') }}</div>
    </div>
    <div style="background:#fef2f2;border-radius:12px;padding:20px;text-align:center;">
        <div style="font-size:13px;color:#6b7280;">📦 Nilai Bantuan</div>
        <div style="font-size:28px;font-weight:800;color:#dc2626;margin-top:4px;">Rp {{ number_format($total_bantuan ?? 0,0,',','.') }}</div>
    </div>
    <div style="background:#fef7e6;border-radius:12px;padding:20px;text-align:center;">
        <div style="font-size:13px;color:#6b7280;">🚐 Biaya Operasional</div>
        <div style="font-size:28px;font-weight:800;color:#b07d14;margin-top:4px;">Rp {{ number_format($total_biaya ?? 0,0,',','.') }}</div>
    </div>
    <div style="background:#00034a;border-radius:12px;padding:20px;text-align:center;">
        <div style="font-size:13px;color:rgba(255,255,255,0.6);">💵 Sisa Dana</div>
        <div style="font-size:28px;font-weight:800;color:white;margin-top:4px;">Rp {{ number_format($sisa ?? 0,0,',','.') }}</div>
    </div>
</div>

{{-- Aksi transaksi --}}
<div class="finance-actions"><div><span class="finance-kicker">PENCATATAN KEUANGAN</span><h2>Arus Dana & Operasional</h2><p>Ringkasan dan riwayat transaksi dalam satu dashboard.</p></div><div class="finance-actions__buttons"><button type="button" class="btn btn-outline" data-open-finance-modal="createBiayaModal">+ Biaya Operasional</button><button type="button" class="btn btn-primary" data-open-finance-modal="createDanaModal">+ Dana Donatur</button></div></div>
@include('keuangan._create-modals')

{{-- Ringkasan Anggaran & Pembelian --}}
<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
        <h3 style="font-size:15px;font-weight:600;">📊 Ringkasan Anggaran Kegiatan & Pembelian</h3>
    </div>
    <div style="padding:20px;">
        <table class="table-data">
            <thead><tr><th>Total Anggaran</th><th>Total Realisasi</th><th>Sisa Anggaran</th><th>% Realisasi</th></tr></thead>
            <tbody>
                <tr>
                    <td style="font-size:18px;font-weight:800;color:#00034a;">Rp {{ number_format($total_anggaran_all ?? 0,0,',','.') }}</td>
                    <td style="font-size:18px;font-weight:800;color:#017723;">Rp {{ number_format($total_realisasi_all ?? 0,0,',','.') }}</td>
                    <td style="font-size:18px;font-weight:800;color:#b07d14;">Rp {{ number_format(($total_anggaran_all ?? 0) - ($total_realisasi_all ?? 0),0,',','.') }}</td>
                    <td style="font-size:18px;font-weight:800;">{{ $total_anggaran_all > 0 ? round(($total_realisasi_all/$total_anggaran_all)*100,1) : 0 }}%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Box 1: Realisasi Anggaran Kegiatan (Batch 1) --}}
<div class="card" style="margin-top:20px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
        <div><h3 style="font-size:15px;font-weight:600;">📦 Realisasi Anggaran Kegiatan</h3>
        <p style="font-size:12px;color:#6b7280;">Total: <strong>Rp {{ number_format($anggarans->sum('anggaran'),0,',','.') }}</strong> — Realisasi: <strong>Rp {{ number_format($anggarans->sum('realisasi'),0,',','.') }}</strong></p></div>
        <button onclick="alert('Form tambah menyusul di versi berikutnya')" class="btn btn-primary btn-sm">+ Tambah</button>
    </div>
    <div style="padding:16px 20px;overflow-x:auto;">
        <table class="table-data">
            <thead><tr><th>No</th><th>Komponen</th><th>Target</th><th>Anggaran</th><th>Realisasi</th><th>%</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($anggarans as $i => $a)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:500;">{{ $a->nama_anggaran ?? $a->kategori }}</td>
                    <td>{{ $a->target_paket ? number_format($a->target_paket) . ' ' . ($a->satuan ?? '') : '-' }}</td>
                    <td>Rp {{ number_format($a->anggaran,0,',','.') }}</td>
                    <td>Rp {{ number_format($a->realisasi,0,',','.') }}</td>
                    <td>
                        @php $pct = $a->anggaran > 0 ? round(($a->realisasi/$a->anggaran)*100,1) : 0; @endphp
                        <div class="progress-bar" style="width:80px;display:inline-block;"><div class="progress-fill" style="width:{{ $pct }}%;background:{{ $pct >= 100 ? '#017723' : '#e5a820' }};"></div></div>
                        <span style="font-size:12px;margin-left:4px;">{{ $pct }}%</span>
                    </td>
                    <td><span class="badge badge-green">✅ {{ $a->catatan ?? 'Lunas' }}</span></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f8f9fa;"><td></td><td><strong>TOTAL</strong></td><td></td>
                    <td><strong>Rp {{ number_format($anggarans->sum('anggaran'),0,',','.') }}</strong></td>
                    <td><strong>Rp {{ number_format($anggarans->sum('realisasi'),0,',','.') }}</strong></td>
                    <td><strong>{{ $anggarans->sum('anggaran') > 0 ? round(($anggarans->sum('realisasi')/$anggarans->sum('anggaran'))*100,1) : 0 }}%</strong></td>
                    <td><span class="badge badge-green">✅ Lunas</span></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- Box 2: Rekap Pembelian Barang (Batch 2) --}}
<div class="card" style="margin-top:20px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
        <div><h3 style="font-size:15px;font-weight:600;">📋 Rekap Pembelian Barang</h3>
        <p style="font-size:12px;color:#6b7280;">Total: <strong>Rp {{ number_format($pembelian->sum('anggaran'),0,',','.') }}</strong> — Realisasi: <strong>Rp {{ number_format($pembelian->sum('realisasi'),0,',','.') }}</strong></p></div>
        <button onclick="alert('Form tambah menyusul di versi berikutnya')" class="btn btn-primary btn-sm">+ Tambah</button>
    </div>
    <div style="padding:16px 20px;overflow-x:auto;">
        <table class="table-data">
            <thead><tr><th>No</th><th>Nama Barang</th><th>Batch</th><th>Qty Rencana</th><th>Qty Terbeli</th><th>Qty Belum</th><th>Harga Satuan</th><th>Anggaran</th><th>Realisasi</th><th>Sisa</th><th>%</th></tr></thead>
            <tbody>
                @foreach($pembelian as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:500;">{{ $p->nama_barang }}</td>
                    <td style="font-size:12px;">{{ $p->batch ?? '-' }}</td>
                    <td>{{ number_format($p->qty_rencana) }}</td>
                    <td>{{ number_format($p->qty_terbeli) }}</td>
                    <td style="color:{{ $p->qty_belum > 0 ? '#dc2626' : '#017723' }};">{{ number_format($p->qty_belum) }}</td>
                    <td>Rp {{ number_format($p->harga_satuan,0,',','.') }}</td>
                    <td>Rp {{ number_format($p->anggaran,0,',','.') }}</td>
                    <td>Rp {{ number_format($p->realisasi,0,',','.') }}</td>
                    <td>Rp {{ number_format($p->sisa,0,',','.') }}</td>
                    <td>
                        <div class="progress-bar" style="width:60px;display:inline-block;"><div class="progress-fill" style="width:{{ $p->persen_real }}%;background:{{ $p->persen_real >= 100 ? '#017723' : '#e5a820' }};"></div></div>
                        <span style="font-size:12px;">{{ $p->persen_real }}%</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Rekap Biaya per Batch --}}
<div class="card" style="margin-top:20px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
        <div><h3 style="font-size:15px;font-weight:600;">📦 Rekap Biaya per Batch / Kegiatan</h3>
        <p style="font-size:12px;color:#6b7280;">Total biaya operasional dikelompokkan per batch untuk memudahkan kontrol anggaran.</p></div>
    </div>
    <div style="padding:16px 20px;overflow-x:auto;">
        <table class="table-data">
            <thead><tr><th>Batch / Kegiatan</th><th>Jumlah Transaksi</th><th style="text-align:right;">Total Biaya</th><th style="text-align:right;">% dari Total</th></tr></thead>
            <tbody>
                @forelse($biayaBatch ?? [] as $b)
                <tr>
                    <td style="font-weight:600;color:#00034a;">{{ $b->batch }}</td>
                    <td>{{ $b->jumlah_transaksi }}</td>
                    <td style="text-align:right;font-weight:700;">Rp {{ number_format($b->total,0,',','.') }}</td>
                    <td style="text-align:right;">{{ $total_biaya > 0 ? round($b->total/$total_biaya*100,1) : 0 }}%</td>
                </tr>
                @empty<tr><td colspan="4" class="transaction-empty">Belum ada biaya operasional tercatat.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Riwayat Biaya Operasional --}}
<div class="card transaction-history" style="margin-top:20px;">
    <div class="transaction-history__header"><div><span class="finance-kicker">AUDIT PENGELUARAN</span><h3>Riwayat Biaya Operasional</h3><p>{{ count($biaya ?? []) }} transaksi terbaru</p></div></div>
    <div class="transaction-table-wrap desktop-transactions"><table class="transaction-table"><thead><tr><th>Tanggal</th><th>Kategori</th><th>Batch</th><th>Pihak Penerima</th><th>Deskripsi</th><th class="amount-col">Jumlah</th></tr></thead><tbody>
    @forelse($biaya ?? [] as $b)
    <tr>
        <td class="date-cell">{{ is_object($b->tanggal) ? $b->tanggal->format('d M Y') : date('d M Y', strtotime($b->tanggal)) }}</td>
        <td><span class="transaction-type">{{ ucfirst(str_replace('_',' ',$b->kategori)) }}</span></td>
        <td style="font-size:12px;">{{ $b->batch_kegiatan ?? '-' }}</td>
        <td>{{ $b->pihak_penerima ?? '-' }}</td>
        <td class="description-cell">{{ $b->deskripsi }}</td>
        <td class="amount-cell">Rp {{ number_format($b->jumlah,0,',','.') }}</td>
    </tr>
    @empty<tr><td colspan="6" class="transaction-empty">Belum ada biaya operasional.</td></tr>@endforelse
    </tbody></table></div>
    <div class="mobile-transactions">
    @forelse($biaya ?? [] as $b)
    <article class="transaction-card"><div class="transaction-card__top"><div><h4>{{ $b->deskripsi }}</h4><time>{{ is_object($b->tanggal) ? $b->tanggal->format('d M Y') : date('d M Y', strtotime($b->tanggal)) }} • {{ $b->batch_kegiatan ?? '-' }}</time></div><strong>Rp {{ number_format($b->jumlah,0,',','.') }}</strong></div><div class="transaction-card__meta"><span class="transaction-type">{{ ucfirst(str_replace('_',' ',$b->kategori)) }}</span><p>{{ $b->pihak_penerima ?? 'Tanpa pihak' }}</p></div></article>
    @empty<div class="transaction-empty">Belum ada biaya operasional.</div>@endforelse
    </div>
</div>

{{-- Riwayat Dana Masuk --}}
<div class="card transaction-history">
    <div class="transaction-history__header"><div><span class="finance-kicker">AUDIT DANA MASUK</span><h3>Riwayat Transaksi</h3><p>{{ count($dana_masuk ?? []) }} transaksi tercatat</p></div></div>
    <div class="transaction-table-wrap desktop-transactions"><table class="transaction-table"><thead><tr><th>Donatur</th><th>Tanggal</th><th class="amount-col">Jumlah</th><th>Jenis</th><th>Keterangan</th><th>Aksi</th></tr></thead><tbody>
    @forelse($dana_masuk ?? [] as $d)
    <tr><td class="donor-cell">{{ $d->donatur }}</td><td class="date-cell">{{ is_object($d->tanggal_masuk) ? $d->tanggal_masuk->format('d M Y') : date('d M Y', strtotime($d->tanggal_masuk)) }}</td><td class="amount-cell">Rp {{ number_format($d->jumlah,0,',','.') }}</td><td><span class="transaction-type">{{ ucfirst(str_replace('_',' ',$d->jenis)) }}</span></td><td class="description-cell">{{ $d->keterangan ?? '-' }}</td><td><div class="transaction-actions"><button type="button" onclick="editDana({{ $d->id }}, @js($d->donatur), @js(is_object($d->tanggal_masuk) ? $d->tanggal_masuk->format('Y-m-d') : $d->tanggal_masuk), {{ $d->jumlah }}, @js($d->jenis), @js($d->keterangan ?? ''))" class="transaction-action transaction-action--edit">Edit</button><form method="POST" action="{{ route('keuangan.dana.delete', $d->id) }}" onsubmit="return confirm('Hapus transaksi ini?')">@csrf<button class="transaction-action transaction-action--delete">Hapus</button></form></div></td></tr>
    @empty<tr><td colspan="6" class="transaction-empty">Belum ada transaksi dana masuk.</td></tr>@endforelse
    </tbody></table></div>
    <div class="mobile-transactions">
    @forelse($dana_masuk ?? [] as $d)
    <article class="transaction-card"><div class="transaction-card__top"><div><h4>{{ $d->donatur }}</h4><time>{{ is_object($d->tanggal_masuk) ? $d->tanggal_masuk->format('d M Y') : date('d M Y', strtotime($d->tanggal_masuk)) }}</time></div><strong>Rp {{ number_format($d->jumlah,0,',','.') }}</strong></div><div class="transaction-card__meta"><span class="transaction-type">{{ ucfirst(str_replace('_',' ',$d->jenis)) }}</span><p>{{ $d->keterangan ?? 'Tanpa keterangan' }}</p></div><div class="transaction-actions"><button type="button" onclick="editDana({{ $d->id }}, @js($d->donatur), @js(is_object($d->tanggal_masuk) ? $d->tanggal_masuk->format('Y-m-d') : $d->tanggal_masuk), {{ $d->jumlah }}, @js($d->jenis), @js($d->keterangan ?? ''))" class="transaction-action transaction-action--edit">Edit</button><form method="POST" action="{{ route('keuangan.dana.delete', $d->id) }}" onsubmit="return confirm('Hapus transaksi ini?')">@csrf<button class="transaction-action transaction-action--delete">Hapus</button></form></div></article>
    @empty<div class="transaction-empty">Belum ada transaksi dana masuk.</div>@endforelse
    </div>
</div>

{{-- Edit Modal --}}
                    {{-- Edit Modal --}}
                    <div id="editModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;" onclick="if(event.target===this)closeEdit()">
                        <div style="background:white;border-radius:12px;padding:24px;width:90%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,0.3);" onclick="event.stopPropagation()">
                            <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">✏️ Edit Dana Donatur</h3>
                            <form id="editForm" method="POST">
                                @csrf
                                <div style="margin-bottom:12px;">
                                    <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Nama Donatur</label>
                                    <input id="editDonatur" name="donatur" class="form-input" required>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                                    <div>
                                        <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Tanggal Masuk</label>
                                        <input id="editTanggal" name="tanggal_masuk" type="date" class="form-input" required>
                                    </div>
                                    <div>
                                        <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Jumlah (Rp)</label>
                                        <input id="editJumlah" name="jumlah" type="number" step="0.01" class="form-input" required>
                                    </div>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px;">
                                    <div>
                                        <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Jenis</label>
                                        <select id="editJenis" name="jenis" class="form-input">
                                            <option value="transfer">Transfer</option>
                                            <option value="uang_tunai">Uang Tunai</option>
                                            <option value="barang">Barang</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Keterangan</label>
                                        <input id="editKeterangan" name="keterangan" class="form-input" placeholder="Opsional">
                                    </div>
                                </div>
                                <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:20px;padding-top:16px;border-top:1px solid #e5e7eb;">
                                    <button type="button" onclick="closeEdit()" class="btn btn-outline">Batal</button>
                                    <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <script>
                    function editDana(id, donatur, tgl, jumlah, jenis, keterangan) {
                        document.getElementById('editForm').action = '/keuangan/dana/' + id + '/update';
                        document.getElementById('editDonatur').value = donatur;
                        document.getElementById('editTanggal').value = tgl;
                        document.getElementById('editJumlah').value = jumlah;
                        document.getElementById('editJenis').value = jenis;
                        document.getElementById('editKeterangan').value = keterangan;
                        document.getElementById('editModal').style.display = 'flex';
                    }
                    function closeEdit() {
                        document.getElementById('editModal').style.display = 'none';
                    }
                    </script>
@section('scripts')
<script>
let activeFinanceModal=null,financeTrigger=null;
function openFinanceModal(id){const m=document.getElementById(id);if(!m)return;financeTrigger=document.activeElement;activeFinanceModal=m;m.classList.add('is-open');m.setAttribute('aria-hidden','false');document.body.classList.add('finance-modal-open');m.querySelector('button,input,select')?.focus();}
function closeFinanceModal(){if(!activeFinanceModal)return;activeFinanceModal.classList.remove('is-open');activeFinanceModal.setAttribute('aria-hidden','true');document.body.classList.remove('finance-modal-open');activeFinanceModal=null;financeTrigger?.focus();}
document.querySelectorAll('[data-open-finance-modal]').forEach(b=>b.addEventListener('click',()=>openFinanceModal(b.dataset.openFinanceModal)));
document.querySelectorAll('[data-close-finance-modal]').forEach(b=>b.addEventListener('click',closeFinanceModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape'&&activeFinanceModal)closeFinanceModal();});
const invalidFinanceForm=@json(old('form_type'));
if(invalidFinanceForm==='dana')openFinanceModal('createDanaModal');
if(invalidFinanceForm==='biaya')openFinanceModal('createBiayaModal');
</script>
@endsection
@endsection
