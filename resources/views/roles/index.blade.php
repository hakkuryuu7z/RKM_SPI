@extends('layouts.app')

@section('title', 'Kelola Role')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color: #1e293b;">Kelola Role Hak Akses</h4>
            <p class="text-muted small mb-0">Atur tingkatan akses untuk Admin, Office, dan Member Relasi (MR).</p>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Form Tambah Role -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header pt-4 pb-3 px-4">
                    <h6 class="fw-bold mb-0" style="color: #4f46e5;">+ Buat Role Baru</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf

                        <div class="form-floating mb-3">
                            <input type="number" name="role_level" class="form-control" id="floatLevel" placeholder="1" required>
                            <label for="floatLevel" class="text-muted">Level Role (Angka)</label>
                        </div>

                        <div class="form-floating mb-4">
                            <textarea name="role_describe" class="form-control" id="floatDesc" placeholder="Deskripsi" style="height: 100px" required></textarea>
                            <label for="floatDesc" class="text-muted">Deskripsi Role</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">Simpan Role</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Tabel Daftar Role -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header pt-4 pb-3 px-4">
                    <h6 class="fw-bold mb-0" style="color: #1e293b;">Daftar Role Terdaftar</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">ID</th>
                                    <th width="15%">Level</th>
                                    <th width="55%">Deskripsi</th>
                                    <th width="20%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                <tr>
                                    <td class="text-muted">#{{ $role->role_id }}</td>
                                    <td>
                                        <span class="badge" style="background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">
                                            Lvl {{ $role->role_level }}
                                        </span>
                                    </td>
                                    <td class="fw-medium text-dark text-wrap" style="max-width: 300px;">
                                        {{ $role->role_describe }}
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-light text-warning fw-bold" data-bs-toggle="modal" data-bs-target="#editModal{{ $role->role_id }}">Edit</button>

                                            <form action="{{ route('roles.destroy', $role->role_id) }}" method="POST" onsubmit="return confirm('Hapus role ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light text-danger fw-bold">Hapus</button>
                                            </form>
                                        </div>

                                        <!-- Modal Edit (Modern) -->
                                        <div class="modal fade" id="editModal{{ $role->role_id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg rounded-4 text-start">
                                                    <div class="modal-header border-0 pt-4 px-4">
                                                        <h5 class="fw-bold text-dark">Edit Role #{{ $role->role_id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('roles.update', $role->role_id) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body px-4">
                                                            <div class="form-floating mb-3">
                                                                <input type="number" name="role_level" class="form-control" value="{{ $role->role_level }}" required>
                                                                <label>Level Role</label>
                                                            </div>
                                                            <div class="form-floating">
                                                                <textarea name="role_describe" class="form-control" style="height: 120px" required>{{ $role->role_describe }}</textarea>
                                                                <label>Deskripsi Role</label>
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
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">Belum ada data role.</td>
                                </tr>
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
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('
            success ') }}',
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