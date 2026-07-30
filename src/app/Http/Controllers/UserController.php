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
        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $kelompoks = \App\Models\Kelompok::all();
        return view('users.create', compact('kelompoks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nik' => 'nullable|digits_between:1,20|unique:users,nik',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,relawan,ketua_kelompok',
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
            'nik' => 'nullable|digits_between:1,20|unique:users,nik,' . $user->id,
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:admin,relawan,ketua_kelompok',
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
}
