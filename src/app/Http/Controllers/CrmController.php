<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CrmController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'pengurus');
        $pengurus = User::whereNotNull('jabatan')->orderBy('jabatan')->get();
        $mitra = DB::table('mitra')->orderByDesc('total_kontribusi')->get();
        $relawan = DB::table('relawans')->whereNotNull('nama_lengkap')->orderBy('nama_lengkap')->get();
        $penerima = DB::table('penerimas')->whereNotNull('kategori_kerentanan')->orderBy('nama')->get();
        $stats = ['pengurus'=>$pengurus->count(),'mitra'=>$mitra->count(),'relawan'=>$relawan->count(),'penerima'=>$penerima->count(),'kontribusi'=>$mitra->sum('total_kontribusi'),'jam_relawan'=>$relawan->sum('jam_kontribusi')];
        return view('crm.index', compact('tab','pengurus','mitra','relawan','penerima','stats'));
    }

    public function create(string $type) { abort_unless(in_array($type,['pengurus','mitra','relawan']),404); return view('crm.form', compact('type')); }

    public function edit(string $type, int $id)
    {
        abort_unless(in_array($type,['pengurus','mitra','relawan']),404);
        $record = $type === 'pengurus' ? User::findOrFail($id) : DB::table($type === 'mitra' ? 'mitra' : 'relawans')->find($id);
        abort_unless($record,404);
        return view('crm.form', compact('type','record'));
    }

    public function store(Request $request, string $type)
    {
        if ($type === 'pengurus') {
            $d=$this->validatePengurus($request);
            $d['role']=User::roleFromJabatan($d['jabatan']);
            $d['password']=Hash::make($d['password']);
            $d['is_active']=$d['status_aktif'];
            User::create($d);
        } elseif ($type === 'mitra') {
            $d=$this->validateMitra($request); DB::table('mitra')->insert($d+['created_at'=>now(),'updated_at'=>now()]);
        } elseif ($type === 'relawan') {
            $d=$this->validateRelawan($request); DB::transaction(function() use($d){ $u=User::create(['name'=>$d['nama_lengkap'],'nik'=>$d['nik'],'email'=>$d['email'],'phone'=>$d['phone'],'password'=>Hash::make(str()->random(32)),'role'=>'relawan','is_active'=>true,'status_aktif'=>true,'wilayah_kabupaten'=>$d['domisili_kota']]); DB::table('relawans')->insert($d+['user_id'=>$u->id,'keahlian'=>$d['keahlian_utama'],'daerah_tugas'=>$d['domisili_kota'],'status'=>'aktif','created_at'=>now(),'updated_at'=>now()]); });
        } else abort(404);
        return redirect()->route('crm.index',['tab'=>$type])->with('success','Data berhasil ditambahkan.');
    }

    public function update(Request $request, string $type, int $id)
    {
        if ($type === 'pengurus') {
            $u=User::findOrFail($id);
            $d=$this->validatePengurus($request,$id);
            if(empty($d['password'])) unset($d['password']); else $d['password']=Hash::make($d['password']);
            $d['role']=User::roleFromJabatan($d['jabatan']);
            $d['is_active']=$d['status_aktif'];
            $u->update($d);
        } elseif ($type === 'mitra') {
            abort_unless(DB::table('mitra')->where('id',$id)->exists(),404); DB::table('mitra')->where('id',$id)->update($this->validateMitra($request)+['updated_at'=>now()]);
        } elseif ($type === 'relawan') {
            $r=DB::table('relawans')->find($id); abort_unless($r,404); $d=$this->validateRelawan($request,$id,$r->user_id); DB::transaction(function() use($r,$id,$d){ User::whereKey($r->user_id)->update(['name'=>$d['nama_lengkap'],'nik'=>$d['nik'],'email'=>$d['email'],'phone'=>$d['phone'],'is_active'=>$d['status_ketersediaan']!=='nonaktif','status_aktif'=>$d['status_ketersediaan']!=='nonaktif','wilayah_kabupaten'=>$d['domisili_kota']]); DB::table('relawans')->where('id',$id)->update($d+['keahlian'=>$d['keahlian_utama'],'daerah_tugas'=>$d['domisili_kota'],'status'=>$d['status_ketersediaan']==='nonaktif'?'nonaktif':'aktif','updated_at'=>now()]); });
        } else abort(404);
        return redirect()->route('crm.index',['tab'=>$type])->with('success','Data berhasil diperbarui.');
    }

    public function destroy(string $type, int $id)
    {
        if ($type==='pengurus') { $u=User::findOrFail($id); abort_if($u->id===auth()->id(),422,'Akun sendiri tidak dapat dinonaktifkan.'); $u->update(['status_aktif'=>false,'is_active'=>false]); }
        elseif($type==='relawan'){ $r=DB::table('relawans')->find($id); abort_unless($r,404); DB::transaction(function()use($r,$id){DB::table('relawans')->where('id',$id)->update(['status'=>'nonaktif','status_ketersediaan'=>'nonaktif','updated_at'=>now()]);User::whereKey($r->user_id)->update(['is_active'=>false,'status_aktif'=>false]);}); }
        elseif($type==='mitra'){ abort_unless(DB::table('mitra')->where('id',$id)->exists(),404); DB::table('mitra')->where('id',$id)->delete(); }
        else abort(404);
        return redirect()->route('crm.index',['tab'=>$type])->with('success',$type==='mitra'?'Data dihapus.':'Data dinonaktifkan.');
    }

    private function validatePengurus(Request $r, ?int $id=null): array { return $r->validate(['name'=>'required|string|max:150','nip'=>['nullable','max:20',Rule::unique('users','nip')->ignore($id)],'jabatan'=>'required|string|max:100','email'=>['required','email',Rule::unique('users','email')->ignore($id)],'phone'=>'nullable|max:20','nik'=>['nullable','max:20',Rule::unique('users','nik')->ignore($id)],'tempat_lahir'=>'nullable|max:100','tanggal_lahir'=>'nullable|date','jenis_kelamin'=>'nullable|in:L,P','alamat_lengkap'=>'nullable|max:500','nama_bank'=>'nullable|max:100','nomor_rekening'=>'nullable|max:30','atas_nama_rekening'=>'nullable|max:150','status_aktif'=>'required|boolean','password'=>[$id?'nullable':'required','min:8']]); }
    private function validateMitra(Request $r): array { return $r->validate(['nama_instansi'=>'required|max:180','kategori'=>'required|in:csr_perusahaan,lembaga_donor,komunitas,perorangan','pic_nama'=>'nullable|max:150','pic_email'=>'nullable|email','pic_phone'=>'nullable|max:20','no_mou'=>'nullable|max:100','jenis_dukungan'=>'required|in:finansial,barang,jasa','total_kontribusi'=>'required|numeric|min:0']); }
    private function validateRelawan(Request $r, ?int $id=null, ?int $userId=null): array { return $r->validate(['nama_lengkap'=>'required|max:150','nik'=>['required','digits:16',Rule::unique('relawans','nik')->ignore($id),Rule::unique('users','nik')->ignore($userId)],'tempat_tanggal_lahir'=>'required|max:150','jenis_kelamin'=>'required|in:L,P','phone'=>'required|max:20','email'=>['required','email',Rule::unique('relawans','email')->ignore($id),Rule::unique('users','email')->ignore($userId)],'keahlian_utama'=>'required|in:Medis,Logistik,SAR/Evakuasi,IT/Dokumentasi,Pengajar','status_ketersediaan'=>'required|in:siap_tanggap_bencana,akhir_pekan,nonaktif','jam_kontribusi'=>'required|integer|min:0','domisili_kota'=>'required|max:100']); }
}
