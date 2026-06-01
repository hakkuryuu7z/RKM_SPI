@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- 🏛️ HEADER DASHBOARD MANAGEMENT -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Dashboard Monitoring SPI</h4>
            <small class="text-muted">Pemantauan real-time aktivitas rute kunjungan lapangan personil Marketing Representative (MR)</small>
        </div>
        <div class="badge bg-primary pt-2 pb-2 px-3 rounded-3 fw-semibold shadow-sm">
            <i class="fa-solid fa-calendar-day me-2"></i>
            {{ \Carbon\Carbon::parse($hariIni)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
        </div>
    </div>

    <!-- 📊 PANEL REKAPITULASI METRIK STATISTIK HARI INI -->
    <div class="row g-3 mb-4">
        <!-- Total Perencanaan Rute -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1 fw-semibold">Total Rencana RKM</small>
                        <h3 class="fw-bold text-dark mb-0">{{ $metrics['total_tugas'] }}</h3>
                    </div>
                    <div class="p-3 bg-primary-subtle rounded-3 text-primary">
                        <i class="fa-solid fa-route fa-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Kunjungan Selesai -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1 fw-semibold">Selesai Kunjungan</small>
                        <h3 class="fw-bold text-success mb-0">{{ $metrics['total_checkout'] }}</h3>
                    </div>
                    <div class="p-3 bg-success-subtle rounded-3 text-success">
                        <i class="fa-solid fa-circle-check fa-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Sedang di Toko -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1 fw-semibold">Sedang Di Lokasi</small>
                        <h3 class="fw-bold text-warning mb-0">{{ $metrics['total_checkin'] }}</h3>
                    </div>
                    <div class="p-3 bg-warning-subtle rounded-3 text-warning">
                        <i class="fa-solid fa-house-laptop fa-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Belum Dikunjungi -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1 fw-semibold">Belum Dikunjungi</small>
                        <h3 class="fw-bold text-secondary mb-0">{{ $metrics['total_belum'] }}</h3>
                    </div>
                    <div class="p-3 bg-light rounded-3 text-secondary">
                        <i class="fa-solid fa-clock fa-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 📋 TABEL MONITORING LIVE PERGERAKAN LAPANGAN -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-list-check me-2 text-primary"></i>Status Realisasi Rute Lapangan Per Hari Ini
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4">No.</th>
                            <th>Nama Personil (MR)</th>
                            <th>Kode / Nama Member Outlet</th>
                            <th class="text-center">Waktu Check-In</th>
                            <th class="text-center">Waktu Check-Out</th>
                            <th class="text-center">Status Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($allTugas as $index => $tugas)
                        <tr>
                            <td class="ps-4 fw-medium text-secondary">{{ $index + 1 }}</td>
                            <td>
                                <!-- 💡 MENAMPILKAN USERNAME YANG SESUAI DENGAN DATABASE ANDA -->
                                <span class="fw-bold text-dark">
                                    {{ $tugas->user->user_username ?? 'User ID: ' . $tugas->jlr_user_id }}
                                </span>
                            </td>
                            <td>
                                <span class="d-block fw-semibold text-primary">{{ $tugas->jlr_kodemember }}</span>
                                <small class="text-muted">{{ $tugas->member->nama ?? 'Data Member Tidak Ditemukan' }}</small>
                            </td>
                            <td class="text-center fw-medium text-dark">
                                {{ $tugas->jam_masuk_hari_ini ? \Carbon\Carbon::parse($tugas->jam_masuk_hari_ini)->format('H:i:s') : '-' }}
                            </td>
                            <td class="text-center fw-medium text-dark">
                                {{ $tugas->jam_keluar_hari_ini ? \Carbon\Carbon::parse($tugas->jam_keluar_hari_ini)->format('H:i:s') : '-' }}
                            </td>
                            <td class="text-center">
                                @if($tugas->status_hari_ini === 'CHECKOUT')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                                    <i class="fa-solid fa-circle-check me-1"></i> CHECK-OUT
                                </span>
                                @elseif($tugas->status_hari_ini === 'CHECKIN')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1 fw-bold">
                                    <i class="fa-solid fa-spinner fa-spin me-1"></i> SEDANG DI TOKO
                                </span>
                                @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1 fw-bold">
                                    <i class="fa-solid fa-clock me-1"></i> BELUM KUNJUNGAN
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-folder-open d-block fa-3x mb-3 text-secondary"></i>
                                Belum ada data perencanaan rute (RKM) yang didistribusikan untuk hari ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection