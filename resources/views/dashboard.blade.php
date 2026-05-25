@extends('layouts.app')

@section('title', 'Dashboard RKM SPI')

@section('content')
<div class="container-fluid px-0">

    <div class="card border-0 rounded-4 mb-4" style="background: linear-gradient(135deg, #0c4a6e 0%, #0ea5e9 100%); color: white; overflow: hidden; position: relative;">
        <div style="position: absolute; right: -5%; top: -20%; opacity: 0.1; transform: scale(1.5);">
            <svg width="300" height="300" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <path fill="#ffffff" d="M42.7,-73.4C55.9,-66.2,67.6,-54.6,76.5,-41.2C85.4,-27.8,91.5,-12.6,90.2,2C88.9,16.6,80.1,30.7,70.5,43.4C60.9,56.1,50.4,67.3,37.3,74.5C24.2,81.7,8.5,84.9,-6.2,84.6C-20.9,84.3,-34.6,80.5,-46.8,73.1C-59,65.7,-69.7,54.7,-76.3,41.6C-82.9,28.5,-85.4,13.3,-84.6,-1.5C-83.8,-16.3,-79.6,-31.6,-71.4,-43.8C-63.2,-56,-51,-65.1,-37.8,-72.3C-24.6,-79.5,-10.4,-84.8,2.7,-88.7C15.8,-92.6,31.6,-85.1,42.7,-73.4Z" transform="translate(100 100)" />
            </svg>
        </div>

        <div class="card-body p-4 p-md-5 position-relative z-index-1">
            <p class="mb-1 text-info fw-bold" style="letter-spacing: 2px; font-size: 0.8rem;">OVERVIEW</p>
            <h3 class="fw-bold mb-1">Selamat Datang, {{ Auth::user()->user_username }}! 👋</h3>
            <p class="mb-0" style="color: #bae6fd;">Senang melihat Anda kembali. Berikut adalah ringkasan sistem hari ini.</p>
        </div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-md-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 p-3">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-light rounded-3 d-flex justify-content-center align-items-center me-3" style="width: 60px; height: 60px;">
                        <span class="fs-3">👥</span>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-bold">TOTAL PENGGUNA</p>
                        <h4 class="fw-bold mb-0" style="color: #0c4a6e;">69 <span class="fs-6 text-muted fw-normal">Akun</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 p-3">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-light rounded-3 d-flex justify-content-center align-items-center me-3" style="width: 60px; height: 60px;">
                        <span class="fs-3">🛡️</span>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-bold">TINGKAT AKSES</p>
                        <h4 class="fw-bold mb-0" style="color: #0c4a6e;">69<span class="fs-6 text-muted fw-normal">Role</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 p-3" style="border: 1px dashed #cbd5e1 !important; background-color: #f8fafc;">
                <div class="card-body d-flex align-items-center opacity-50">
                    <div class="bg-white rounded-3 d-flex justify-content-center align-items-center me-3 shadow-sm" style="width: 60px; height: 60px;">
                        <span class="fs-3">📊</span>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-bold">LAPORAN MR</p>
                        <h6 class="fw-bold mb-0 text-muted">Fitur Mendatang...</h6>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold" style="color: #1e293b;">📌 Informasi Sistem</h6>
                    <p class="text-muted small mb-0">Sistem <strong>RKM SPI (Digital Monitoring System)</strong> saat ini sedang dalam tahap pengembangan. Anda masuk sebagai <span class="badge bg-primary">{{ Auth::user()->role ? Auth::user()->role->role_describe : 'Belum Ada Role' }}</span>. Gunakan menu di sebelah kiri untuk menavigasi aplikasi.</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

<!-- Script tambahan khusus di halaman dashboard -->
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Cek notif dari Controller
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Mantap!',
            text: '{{ session('
            success ') }}',
            timer: 2000,
            showConfirmButton: false
        });
        @endif
        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Waduh...',
            text: '{{ session('
            error ') }}'
        });
        @endif
    });
</script>
@endpush