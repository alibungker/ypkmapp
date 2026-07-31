@extends('layouts.app')
@section('title', 'Keuangan')
@section('styles')
<style>
.finance-actions{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:20px 22px;margin-bottom:20px;background:white;border:1px solid #e5e7eb;border-radius:14px}.finance-actions h2{font-size:20px;color:#00034a;margin:3px 0}.finance-actions p{font-size:13px;color:#667085;margin:0}.finance-kicker{font-size:10px;letter-spacing:.14em;font-weight:800;color:#017723}.finance-actions__buttons{display:flex;gap:10px}.finance-modal{display:none;position:fixed;inset:0;z-index:1200;align-items:center;justify-content:center;padding:20px}.finance-modal.is-open{display:flex}.finance-modal__backdrop{position:absolute;inset:0;background:rgba(0,3,74,.68);backdrop-filter:blur(4px)}.finance-modal__panel{position:relative;width:min(650px,100%);max-height:calc(100dvh - 40px);overflow:auto;background:#fff;border-radius:18px;box-shadow:0 24px 80px rgba(0,3,74,.3)}.finance-modal__header{position:sticky;top:0;z-index:1;display:flex;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #e5e7eb;background:white}.finance-modal__header h2{font-size:21px;color:#00034a;margin:3px 0}.finance-modal__close{width:44px;height:44px;border:0;border-radius:50%;font-size:24px;cursor:pointer}.finance-modal__form{display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:24px}.form-field--full,.finance-modal__actions{grid-column:1/-1}.finance-modal__actions{display:flex;justify-content:flex-end;gap:10px}body.finance-modal-open{overflow:hidden}@media(max-width:700px){.finance-actions{align-items:stretch;flex-direction:column}.finance-actions__buttons{display:grid}.finance-modal{padding:0;align-items:flex-end}.finance-modal__panel{border-radius:18px 18px 0 0;max-height:94dvh}.finance-modal__form{grid-template-columns:1fr;padding:18px}.form-field--full,.finance-modal__actions{grid-column:1}.finance-modal__actions .btn{flex:1;min-height:48px}}
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
                    <td style="color:#dc2626;">{{ $p->qty_belum > 0 ? number_format($p->qty_belum) : '<span style="color:#017723;">0</span>' }}</td>
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

{{-- Riwayat Dana Masuk --}}
<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
        <h3 style="font-size:15px;font-weight:600;">📋 Riwayat Transaksi</h3>
    </div>
    <div style="padding:16px 20px;">
        <table class="table-data">
            <thead><tr><th>Donatur</th><th>Tanggal</th><th>Jumlah</th><th>Jenis</th><th>Keterangan</th><th></th></tr></thead>
            <tbody>
                @forelse($dana_masuk ?? [] as $d)
                <tr>
                    <td style="font-weight:500;">{{ $d->donatur }}</td>
                    <td style="color:#6b7280;">{{ is_object($d->tanggal_masuk) ? $d->tanggal_masuk->format('d M Y') : date('d M Y', strtotime($d->tanggal_masuk)) }}</td>
                    <td style="font-weight:600;color:#017723;">Rp {{ number_format($d->jumlah,0,',','.') }}</td>
                    <td><span style="padding:2px 10px;background:#e8e8f0;border-radius:6px;font-size:12px;">{{ $d->jenis }}</span></td>
                    <td style="color:#6b7280;">{{ $d->keterangan ?? '-' }}</td>
                    <td style="white-space:nowrap;">
                        <button onclick="editDana({{ $d->id }}, '{{ $d->donatur }}', '{{ is_object($d->tanggal_masuk) ? $d->tanggal_masuk->format('Y-m-d') : $d->tanggal_masuk }}', {{ $d->jumlah }}, '{{ $d->jenis }}', '{{ $d->keterangan ?? '' }}')" style="color:#00034a;border:none;background:none;cursor:pointer;font-size:13px;padding:4px 8px;">✏️ Edit</button>
                        <form method="POST" action="{{ route('keuangan.dana.delete', $d->id) }}" style="display:inline;" onsubmit="return confirm('Hapus data dana ini?')">
                            @csrf
                            <button style="color:#dc2626;border:none;background:none;cursor:pointer;font-size:13px;padding:4px 8px;">🗑️ Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="padding:32px;text-align:center;color:#9ca3af;">Belum ada dana masuk. Silakan tambah melalui form di atas.</td></tr>
                @endforelse
            </tbody>
        </table>
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
