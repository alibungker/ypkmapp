<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,relawan,ketua_kelompok',
            'phone' => 'nullable',
            'wilayah_kabupaten' => 'nullable',
            'wilayah_kecamatan' => 'nullable',
            'wilayah_desa' => 'nullable',
        ]);
        $data['password'] = Hash::make($data['password']);
        User::create($data);
        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:admin,relawan,ketua_kelompok',
            'phone' => 'nullable',
            'wilayah_kabupaten' => 'nullable',
            'wilayah_kecamatan' => 'nullable',
            'wilayah_desa' => 'nullable',
        ]);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
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
