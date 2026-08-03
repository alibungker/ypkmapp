<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('kelompok')->orderBy('role')->orderBy('name')->get();
        $kelompoks = \App\Models\Kelompok::withCount('penerima')->orderBy('nama')->get();
        return view('users.index', compact('users', 'kelompoks'));
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $kelompoks = \App\Models\Kelompok::withCount('penerima')->orderBy('nama')->get();
        return view('users.create', compact('kelompoks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nik' => 'nullable|digits:16|unique:users,nik',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:super_admin,pengurus,bendahara,staff,staff_keuangan,relawan,ketua_kelompok',
            'kelompok_id' => 'nullable|exists:kelompoks,id',
            'phone' => 'nullable',
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'wilayah_kabupaten' => 'nullable',
            'wilayah_kecamatan' => 'nullable',
            'wilayah_desa' => 'nullable',
            'alamat_lengkap' => 'nullable',
        ]);

        if ($data['role'] === 'ketua_kelompok') {
            if (empty($data['kelompok_id'])) {
                return back()->withInput()->withErrors(['kelompok_id' => 'Kelompok wajib dipilih untuk Ketua Kelompok.']);
            }
            $sudahAda = User::where('role', 'ketua_kelompok')
                ->where('kelompok_id', $data['kelompok_id'])
                ->exists();
            if ($sudahAda) {
                return back()->withInput()->withErrors(['kelompok_id' => 'Kelompok tersebut sudah memiliki akun Ketua Kelompok.']);
            }
        } else {
            $data['kelompok_id'] = null;
        }

        $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('users/foto', 'public');
        }

        User::create($data);
        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nik' => 'nullable|digits:16|unique:users,nik,' . $user->id,
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:super_admin,pengurus,bendahara,staff,staff_keuangan,relawan,ketua_kelompok',
            'kelompok_id' => 'nullable|exists:kelompoks,id',
            'phone' => 'nullable',
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'wilayah_kabupaten' => 'nullable',
            'wilayah_kecamatan' => 'nullable',
            'wilayah_desa' => 'nullable',
            'alamat_lengkap' => 'nullable',
        ]);

        if ($data['role'] === 'ketua_kelompok') {
            if (empty($data['kelompok_id'])) {
                return back()->withInput()->withErrors(['kelompok_id' => 'Kelompok wajib dipilih untuk Ketua Kelompok.']);
            }
            $sudahAda = User::where('role', 'ketua_kelompok')
                ->where('kelompok_id', $data['kelompok_id'])
                ->where('id', '!=', $user->id)
                ->exists();
            if ($sudahAda) {
                return back()->withInput()->withErrors(['kelompok_id' => 'Kelompok tersebut sudah memiliki akun Ketua Kelompok.']);
            }
        } else {
            $data['kelompok_id'] = null;
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('users/foto', 'public');
        }

        $user->update($data);
        return redirect()->route('users.index')->with('success', 'User diupdate.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa hapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User dihapus.');
    }

    public function profile()
    {
        $user = auth()->user();
        return view('users.profile', compact('user'));
    }

    public function card()
    {
        $user = auth()->user();
        return view('users.card', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name' => 'required|max:150',
            'phone' => 'nullable|max:20',
            'nip' => 'nullable|max:20',
            'tempat_lahir' => 'nullable|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat_lengkap' => 'nullable|max:500',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
        ]);
        if (empty($data['password'])) unset($data['password']);
        else $data['password'] = Hash::make($data['password']);
        if ($request->hasFile('foto')) $data['foto'] = $request->file('foto')->store('users/foto', 'public');
        $user->update($data);
        return back()->with('success', 'Profil diperbarui.');
    }
}
