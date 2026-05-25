@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color: #1e293b;">Kelola Pengguna</h4>
            <p class="text-muted small mb-0">Manajemen akses dan akun staf RKM SPI.</p>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Form Tambah Pengguna -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header pt-4 pb-3 px-4">
                    <h6 class="fw-bold mb-0" style="color: #4f46e5;">+ Tambah Pengguna Baru</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf


                        <!-- Floating Input Username -->
                        <div class="form-floating mb-3">
                            <input type="text" name="user_username" class="form-control" id="floatingUser" placeholder="Username" required>
                            <label for="floatingUser" class="text-muted">Username</label>
                        </div>

                        <!-- Floating Input Password -->

                        <!-- Bagian Input Password -->
                        <div class="input-group mb-4">
                            <div class="form-floating flex-grow-1">
                                <input type="password" name="user_password" class="form-control" id="floatingPass" placeholder="Password" style="border-top-right-radius: 0; border-bottom-right-radius: 0;" required>
                                <label for="floatingPass" class="text-muted">Password (Min. 5 Karakter)</label>
                            </div>
                            <!-- Tombol buat toggle mata -->
                            <button class="btn btn-light" type="button" id="togglePassword" style="border: 1px solid #cbd5e1; border-left: none; border-radius: 0 10px 10px 0; background-color: #f8fafc;">
                                <span id="eyeIcon">👁️</span>
                            </button>
                        </div>

                        <!-- Select Role -->
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Hak Akses (Role)</label>
                            <select name="user_role_id" class="form-select form-select-lg" style="font-size: 0.95rem;" required>
                                <option value="" disabled selected>-- Pilih Hak Akses --</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->role_id }}">{{ $role->role_describe }} (Level {{ $role->role_level }})</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 mt-2">Simpan Akun Baru</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Tabel Daftar Pengguna -->
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0" style="color: #1e293b;">Daftar Akun Terdaftar</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">ID</th>
                                    <th width="40%">Username</th>
                                    <th width="30%">Role Akses</th>
                                    <th width="20%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Bagian Tabel di users/index.blade.php -->
                                @forelse($users as $user)
                                <tr>
                                    <td class="fw-medium text-muted">#{{ $user->user_id }}</td>
                                    <td class="fw-semibold text-dark">{{ $user->user_username }}</td>
                                    <td>
                                        <span class="badge" style="background-color: #e0e7ff; color: #4338ca;">
                                            {{ $user->role ? $user->role->role_describe : 'No Role' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Tombol Edit -->
                                            <button class="btn btn-sm btn-light text-warning fw-bold" data-bs-toggle="modal" data-bs-target="#editUser{{ $user->user_id }}">Edit</button>

                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('users.destroy', $user->user_id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light text-danger fw-bold">Hapus</button>
                                            </form>
                                        </div>

                                        <!-- Modal Edit User -->
                                        <div class="modal fade" id="editUser{{ $user->user_id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg rounded-4 text-start">
                                                    <div class="modal-header border-0 pt-4 px-4">
                                                        <h5 class="fw-bold text-dark">Edit Pengguna</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('users.update', $user->user_id) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body px-4">
                                                            <!-- Username -->
                                                            <div class="form-floating mb-3">
                                                                <input type="text" name="user_username" class="form-control" value="{{ $user->user_username }}" required>
                                                                <label>Username</label>
                                                            </div>

                                                            <!-- Password (Optional) -->
                                                            <div class="form-floating mb-3">
                                                                <input type="password" name="user_password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                                                                <label>Password (Kosongkan jika tidak diubah)</label>
                                                            </div>

                                                            <!-- Role -->
                                                            <div class="mb-2">
                                                                <label class="form-label text-muted small fw-semibold text-uppercase">Hak Akses (Role)</label>
                                                                <select name="user_role_id" class="form-select" required>
                                                                    @foreach($roles as $role)
                                                                    <option value="{{ $role->role_id }}" {{ $user->user_role_id == $role->role_id ? 'selected' : '' }}>
                                                                        {{ $role->role_describe }}
                                                                        </span>
                                                                        @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pb-4 px-4">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                ...
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- KODE BARU: Fitur Intip Password ---
        const togglePassword = document.querySelector("#togglePassword");
        const passwordInput = document.querySelector("#floatingPass");
        const eyeIcon = document.querySelector("#eyeIcon");

        if (togglePassword) {
            togglePassword.addEventListener("click", function() {
                const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
                passwordInput.setAttribute("type", type);
                eyeIcon.textContent = type === "password" ? "👁️" : "🙈";
                passwordInput.focus();
            });
        }

        // --- KODE LAMA: Notifikasi SweetAlert ---
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session("success") }}',
            timer: 2000,
            showConfirmButton: false
        });
        @endif

        @if(count($errors) > 0)
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ $errors->first() }}'
        });
        @endif
    });
</script>
@endpush