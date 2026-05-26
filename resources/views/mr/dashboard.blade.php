@extends('layouts.app')

@section('title', 'Tugas Hari Ini')

@section('content')
<style>
    /* Styling khusus biar tampilan di HP makin cantik kayak aplikasi beneran */
    .mobile-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: 0.2s;
    }

    .mobile-card:active {
        transform: scale(0.98);
    }

    /* Efek menciut pas diklik di HP */
    .icon-box {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
</style>

<div class="container-fluid px-3 pb-5 max-w-md mx-auto" style="max-width: 500px;">

    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">
                Halo, {{ Auth::user()->user_username }}! <i class="fa-solid fa-hand text-warning fa-bounce"></i>
            </h5>
            <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($hariIni)->translatedFormat('l, d F Y') }}</small>
        </div>
        <div class="bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill small">
            {{ $tugasHariIni->count() }} Tugas
        </div>
    </div>

    <h6 class="fw-bold text-secondary mb-3">
        <i class="fa-solid fa-location-dot text-danger me-1"></i> Rute Kunjungan Hari Ini
    </h6>

    @if($tugasHariIni->isEmpty())
    <div class="text-center p-5 bg-white mobile-card mt-4">
        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="100" class="mb-3 opacity-50">
        <h6 class="fw-bold text-muted">Belum Ada Rute Hari Ini</h6>
        <p class="small text-muted mb-0">Silakan hubungi SPV Anda atau nikmati waktu istirahat.</p>
    </div>
    @else
    <div class="d-flex flex-column gap-3">
        @foreach($tugasHariIni as $index => $tugas)
        @php
        $m = $tugas->member; // Relasi ke data member
        // Simulasi status (Nanti kita hubungin sama tabel tbtr_rkm beneran)
        $status = 'BELUM';
        @endphp

        <a href="{{ route('mr.toko.detail', $tugas->jlr_id) }}" class="text-decoration-none">
            <div class="card mobile-card bg-white p-3">
                <div class="d-flex align-items-center gap-3">

                    <div class="icon-box {{ $status == 'SELESAI' ? 'bg-success-subtle text-success' : 'bg-light text-secondary' }}">
                        <i class="fa-solid {{ $status == 'SELESAI' ? 'fa-check' : 'fa-store' }}"></i>
                    </div>

                    <div class="flex-grow-1">
                        <h6 class="fw-bold text-dark mb-1 text-truncate" style="max-width: 200px;">
                            {{ $index + 1 }}. {{ $m ? $m->nama : 'Toko Tidak Ditemukan' }}
                        </h6>
                        <p class="small text-muted mb-0"><i class="fa-solid fa-barcode me-1"></i> {{ $tugas->jlr_kodemember }}</p>
                    </div>

                    <div class="text-muted">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection