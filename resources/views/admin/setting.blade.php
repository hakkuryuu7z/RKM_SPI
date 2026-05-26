@extends('layouts.app')

@section('title', 'Pengaturan Validasi MR')

@section('content')
<div class="container-fluid px-4">

    <div class="d-flex align-items-center gap-2 mb-4 mt-2">
        <div class="bg-primary text-white rounded-3 p-2 d-inline-block">
            <i class="fa-solid fa-sliders fa-lg"></i>
        </div>
        <div>
            <h4 class="fw-bold text-dark mb-0">Pengaturan Parameter Kunjungan</h4>
            <p class="text-muted small mb-0">Atur batasan jarak GPS dan durasi minimal MR berada di lokasi toko.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 rounded-4 shadow-sm bg-white p-4">

                <form action="{{ route('admin.setting.update') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="radius_meter" class="form-label fw-bold text-secondary mb-2">
                            <i class="fa-solid fa-ruler-combined text-primary me-1"></i> Batas Toleransi Radius Jarak
                        </label>
                        <div class="input-group">
                            <input type="number"
                                class="form-control form-control-lg @error('radius_meter') is-invalid @enderror"
                                id="radius_meter"
                                name="radius_meter"
                                value="{{ old('radius_meter', $setting->radius_meter) }}"
                                placeholder="Contoh: 50" required>
                            <span class="input-group-text bg-light fw-bold text-muted">Meter</span>
                        </div>
                        <small class="text-muted d-block mt-1">Jarak maksimal HP milik MR ke titik koordinat asli toko agar diizinkan Check-In.</small>
                        @error('radius_meter')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="minimal_menit" class="form-label fw-bold text-secondary mb-2">
                            <i class="fa-solid fa-clock text-warning me-1"></i> Minimal Durasi Kunjungan (Dwell Time)
                        </label>
                        <div class="input-group">
                            <input type="number"
                                class="form-control form-control-lg @error('minimal_menit') is-invalid @enderror"
                                id="minimal_menit"
                                name="minimal_menit"
                                value="{{ old('minimal_menit', $setting->minimal_menit) }}"
                                placeholder="Contoh: 15" required>
                            <span class="input-group-text bg-light fw-bold text-muted">Menit</span>
                        </div>
                        <small class="text-muted d-block mt-1">Waktu minimal MR harus berada di dalam toko sebelum diperbolehkan melakukan Check-Out.</small>
                        @error('minimal_menit')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="text-muted opacity-25 mb-4">

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-2"></i> SIMPAN PERUBAHAN
                    </button>
                </form>

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
            title: 'Mantap!',
            text: "{{ session('success') }}",
            timer: 2000,
            showConfirmButton: false
        });
        @endif
    });
</script>
@endpush