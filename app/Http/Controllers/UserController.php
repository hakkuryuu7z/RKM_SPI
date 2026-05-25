<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash; // Wajib dipanggil buat enkripsi password

class UserController extends Controller
{
    // Nampilin halaman daftar pengguna & form tambah
    public function index()
    {
        // Narik data user sekalian join ke tabel role
        $users = User::with('role')->get();
        $roles = Role::all(); // Buat pilihan di dropdown form

        return view('users.index', compact('users', 'roles'));
    }

    // Proses simpan data pengguna baru
    public function store(Request $request)
    {
        $request->validate([
            'user_username' => 'required|string|max:100',
            'user_password' => 'required|min:5',
            'user_role_id'  => 'required'
        ]);
        // Simpan ke database tbmaster_users
        User::create([

            'user_username' => $request->user_username,
            'user_password' => Hash::make($request->user_password),
            'user_role_id'  => $request->user_role_id,
        ]);

        return back()->with('success', 'Akun berhasil dibuat!');
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'user_username' => 'required|string|max:100',
            'user_password' => 'nullable|min:5', // Pakai nullable biar boleh kosong
            'user_role_id'  => 'required'
        ]);

        $data = [
            'user_username' => $request->user_username,
            'user_role_id'  => $request->user_role_id,
        ];

        // Kalau password diisi, baru kita hash dan update
        if ($request->filled('user_password')) {
            $data['user_password'] = Hash::make($request->user_password);
        }

        $user->update($data);

        return back()->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus!');
    }
}
