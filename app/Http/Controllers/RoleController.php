<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    // Nampilin halaman form dan daftar role
    public function index()
    {
        // Urutkan berdasarkan level terkecil (biasanya makin kecil makin tinggi aksesnya, misal 1 = Admin)
        $roles = Role::orderBy('role_level', 'asc')->get();

        return view('roles.index', compact('roles'));
    }

    // Proses simpan role baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'role_level'    => 'required|numeric|unique:tbmaster_role,role_level',
            'role_describe' => 'required|string|max:100'
        ], [
            'role_level.unique' => 'Level role ini sudah dipakai, pilih angka lain!'
        ]);

        // Simpan ke database
        Role::create([
            'role_level'    => $request->role_level,
            'role_describe' => $request->role_describe,
        ]);

        return back()->with('success', 'Mantap! Role baru berhasil ditambahkan.');
    }
    // Proses Update (Edit)
    public function update(Request $request, $id)
    {
        $request->validate([
            'role_level'    => 'required|numeric|unique:tbmaster_role,role_level,' . $id . ',role_id',
            'role_describe' => 'required|string|max:100'
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'role_level'    => $request->role_level,
            'role_describe' => $request->role_describe,
        ]);

        return back()->with('success', 'Role berhasil diupdate!');
    }

    // Proses Delete (Hapus)
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Opsional: Kasih validasi biar role yang udah dipake user ga bisa dihapus
        // if ($role->users()->count() > 0) {
        //     return back()->with('error', 'Gagal! Role ini sedang digunakan oleh pengguna.');
        // }

        $role->delete();

        return back()->with('success', 'Role berhasil dihapus!');
    }
}
