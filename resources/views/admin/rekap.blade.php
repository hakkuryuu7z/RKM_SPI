@extends('layouts.app')

@section('content')
<!-- Import Pustaka Eksternal Chart.js untuk Kebutuhan Visualisasi Grafik Donut -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<div class="container-fluid px-4 py-3">
    <!-- 🏛️ HEADER UTAMA INTERFACES -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Laporan Rekap Kunjungan Historis</h4>
            <small class="text-muted">Arsip pelaporan, evaluasi durasi waktu kerja, dan audit log realisasi aktivitas lapangan personil MR</small>
        </div>
    </div>

    <!-- 📊 PANEL FILTER DATA PENCARIAN -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.rekap.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control bg-light text-dark" value="{{ $startDate }}">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="form-control bg-light text-dark" value="{{ $endDate }}">
                </div>
                <div class="col-12 col-sm-8 col-md-4">
                    <label class="form-label small fw-semibold text-secondary">Personil Marketing Representative (MR)</label>
                    <select name="user_id" class="form-select bg-light text-dark">
                        <option value="">-- Tampilkan Semua Personil --</option>
                        @foreach($masterMr as $mr)
                        <option value="{{ $mr->user_id }}" {{ $userId == $mr->user_id ? 'selected' : '' }}>
                            {{ $mr->user_username }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-4 col-md-2">
                    <button type="submit" class="btn btn-dark fw-semibold w-100">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Cari Arsip
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 🗂️ NAVIGASI PILIHAN STRUKTUR TAB HALAMAN -->
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4 rounded-pill" id="tab-akurasi-kpi" data-bs-toggle="pill" data-bs-target="#panel-akurasi" type="button" role="tab">
                <i class="fa-solid fa-chart-pie me-2"></i>Rangkuman Akurasi Performa MR
            </button>
        </li>
        <li class="nav-item ms-2" role="presentation">
            <button class="nav-link fw-bold px-4 rounded-pill" id="tab-log-mentah" data-bs-toggle="pill" data-bs-target="#panel-log-mentah" type="button" role="tab">
                <i class="fa-solid fa-clock-rotate-left me-2"></i>Log Aktivitas Kunjungan Lapangan
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">

        <!-- ================================================================= -->
        <!-- KONTEN TAB 1: RANGKUMAN AKURASI PER MR (DONUT CHART VISUALS)      -->
        <!-- ================================================================= -->
        <div class="tab-pane fade show active" id="panel-akurasi" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted small">Grafik ringkasan rasio pencapaian target kunjungan toko per personil lapangan</div>
                <div>
                    <a href="{{ route('admin.rekap.export.excel', ['start_date' => $startDate, 'end_date' => $endDate, 'user_id' => $userId]) }}" class="btn btn-sm btn-success fw-semibold px-3 py-2 shadow-sm">
                        <i class="fa-solid fa-file-excel me-2"></i> Export Excel Report
                    </a>
                    <a href="{{ route('admin.rekap.export.pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'user_id' => $userId]) }}" target="_blank" class="btn btn-sm btn-danger fw-semibold px-3 py-2 shadow-sm ms-1">
                        <i class="fa-solid fa-file-pdf me-2"></i> Cetak PDF Report
                    </a>
                </div>
            </div>

            @forelse($reportAkurasi as $dataReport)
            <div class="card border-0 shadow-sm rounded-3 mb-3 bg-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7 col-lg-8 border-end">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar bg-primary text-white rounded-circle text-center fw-bold me-3" style="width: 45px; height: 45px; line-height: 45px; font-size: 18px;">
                                    {{ strtoupper(substr($dataReport['username'], 0, 1)) }}
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0">{{ strtoupper($dataReport['username']) }}</h5>
                                    <small class="text-muted">Klasifikasi Jabatan: Marketing Representative (MR)</small>
                                </div>
                            </div>
                            <div class="row g-2 text-center">
                                <div class="col-6 col-sm-3">
                                    <div class="bg-light p-2 rounded-3">
                                        <small class="text-muted d-block small fw-medium">Total Rencana</small>
                                        <span class="fw-bold text-dark fs-5">{{ $dataReport['target'] }}</span> <small class="text-muted">Toko</small>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="bg-success-subtle p-2 rounded-3 text-success">
                                        <small class="text-success d-block small fw-medium">Selesai (Checkout)</small>
                                        <span class="fw-bold fs-5">{{ $dataReport['checkout'] }}</span> <small class="text-success small">Toko</small>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="bg-warning-subtle p-2 rounded-3 text-warning">
                                        <small class="text-warning d-block small fw-medium">Tertunda (Checkin)</small>
                                        <span class="fw-bold fs-5">{{ $dataReport['checkin'] }}</span> <small class="text-warning small">Toko</small>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="bg-danger-subtle p-2 rounded-3 text-danger">
                                        <small class="text-danger d-block small fw-medium">Terlewat (Miss)</small>
                                        <span class="fw-bold fs-5">{{ $dataReport['missed'] }}</span> <small class="text-danger small">Toko</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 col-lg-4 text-center mt-3 mt-md-0 d-flex align-items-center justify-content-center">
                            <div class="me-4 text-start">
                                <small class="text-muted d-block fw-semibold text-uppercase small">Akurasi Kerja</small>
                                <h2 class="fw-bold text-primary mb-0">{{ $dataReport['persentase'] }}%</h2>
                            </div>
                            <div style="width: 110px; height: 110px;">
                                <canvas id="chart-donut-{{ $dataReport['user_id'] }}"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center bg-white p-5 rounded-3 shadow-sm text-muted">Tidak ditemukan data rangkuman KPI.</div>
            @endforelse
        </div>

        <!-- ================================================================= -->
        <!-- KONTEN TAB 2: LOG AKTIVITAS KUNJUNGAN LAPANGAN (TABEL DETAIL)     -->
        <!-- ================================================================= -->
        <div class="tab-pane fade" id="panel-log-mentah" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="table-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="ps-4">No.</th>
                                    <th>Tanggal</th>
                                    <th>Nama MR</th>
                                    <th>Kode / Nama Member Outlet</th>
                                    <th class="text-center">Check In / Out</th>
                                    <th class="text-center">Status Order</th>
                                    <th>Keterangan Lapangan</th>
                                    <th class="text-center">Aksi Audit</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @forelse($allRekap as $index => $rekap)
                                <tr>
                                    <td class="ps-4 text-secondary fw-medium">{{ $allRekap->firstItem() + $index }}</td>
                                    <td class="fw-medium text-dark">{{ \Carbon\Carbon::parse($rekap->rkm_tanggal)->format('d-m-Y') }}</td>
                                    <td><span class="fw-bold text-dark">{{ $rekap->user->user_username ?? 'ID: '.$rekap->rkm_user_id }}</span></td>
                                    <td>
                                        <span class="d-block fw-semibold text-primary">{{ $rekap->rkm_kodemember }}</span>
                                        <small class="text-muted text-wrap d-block" style="max-width: 200px;">{{ $rekap->rkm_nama_member ?? 'Data Toko Tidak Terdaftar' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-success small fw-medium d-block"><i class="fa-solid fa-right-to-bracket me-1"></i>{{ $rekap->waktu_checkin ? \Carbon\Carbon::parse($rekap->waktu_checkin)->format('H:i:s') : '-' }}</span>
                                        <span class="text-danger small fw-medium d-block"><i class="fa-solid fa-right-from-bracket me-1"></i>{{ $rekap->waktu_checkout ? \Carbon\Carbon::parse($rekap->waktu_checkout)->format('H:i:s') : '-' }}</span>
                                    </td>
                                    <!-- 💡 MENAMPILKAN STATUS ORDER LANGSUNG DI BARIS TABEL -->
                                    <td class="text-center">
                                        @if(strtoupper($rekap->rkm_order_status) === 'ORDER')
                                        <span class="badge bg-success text-white rounded-pill px-2 small">
                                            <i class="fa-solid fa-cart-shopping me-1"></i> ORDER
                                        </span>
                                        @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 small">
                                            <i class="fa-solid fa-ban me-1"></i> TIDAK ORDER
                                        </span>
                                        @endif
                                    </td>
                                    <!-- 💡 MENAMPILKAN KETERANGAN MEMBER LANGSUNG DI BARIS TABEL -->
                                    <td>
                                        <div class="text-wrap text-secondary" style="max-width: 220px; font-size: 11px;">
                                            {{ $rekap->rkm_keteranganmember ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 btn-detail-rekap" data-id="{{ $rekap->rkm_id }}">
                                            <i class="fa-solid fa-eye text-primary me-1"></i> Periksa Bukti
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">Tidak ditemukan rekaman historis realisasi kunjungan pada log mentah.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 p-3 border-top">
                        <div class="text-muted small">Menampilkan {{ $allRekap->firstItem() ?? 0 }} sampai {{ $allRekap->lastItem() ?? 0 }} dari {{ $allRekap->total() }} total log.</div>
                        <div>{{ $allRekap->links('pagination::bootstrap-5') }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- 🏛========= MODAL AUDIT BUKTI REALISASI KUNJUNGAN ========= -->
<div class="modal fade" id="modalAuditKunjungan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-file-shield text-success me-2"></i>Lembar Audit Hasil Kunjungan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <small class="text-muted fw-semibold d-block">Tanggal Kunjungan</small>
                        <div class="fw-bold text-dark" id="aud-tanggal">-</div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted fw-semibold d-block">Nama Lapangan (MR)</small>
                        <div class="fw-bold text-dark" id="aud-mr">-</div>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <small class="text-muted fw-semibold d-block">Target Pelanggan / Outlet</small>
                        <div class="fw-bold text-primary" id="aud-toko">-</div>
                    </div>
                    <div class="col-6 border-top pt-2">
                        <small class="text-muted fw-semibold d-block">Jam Masuk (Check-In)</small>
                        <div class="text-dark fw-medium" id="aud-checkin">-</div>
                    </div>
                    <div class="col-6 border-top pt-2">
                        <small class="text-muted fw-semibold d-block">Jam Keluar (Check-Out)</small>
                        <div class="text-dark fw-medium" id="aud-checkout">-</div>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <small class="text-muted fw-semibold d-block mb-1">Status Pengambilan Order Toko</small>
                        <div class="fw-bold text-dark" id="aud-order-status">-</div>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <small class="text-muted fw-semibold d-block mb-1">Keterangan / Catatan Alasan Lapangan</small>
                        <div class="p-2 bg-light rounded text-dark small" id="aud-catatan" style="min-height: 50px;">-</div>
                    </div>
                    <div class="col-12 border-top pt-2 text-center">
                        <small class="text-muted text-start d-block mb-2">Lampiran Dokumentasi Foto Kamera (Klik gambar untuk memperbesar di tab baru)</small>
                        <div id="container-foto">
                            <img src="" id="aud-foto" class="img-fluid rounded border shadow-sm img-thumbnail"
                                style="max-height: 270px; object-fit: cover; cursor: pointer;"
                                alt="Bukti Lapangan"
                                onclick="window.open(this.src, '_blank')">
                        </div>
                        <div id="foto-kosong" class="text-muted small py-3 bg-light rounded border border-dashed">
                            <i class="fa-solid fa-image-slice d-block mb-1"></i> Tidak ada lampiran gambar dari lapangan.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Tutup Dokumen</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // 1. ANCHOR DATA: KONVERSI DATA LARAVEL MENJADI JSON NATIVE JAVASCRIPT (ANTI-ERRORS)
        const dataAkurasi = @json($reportAkurasi);

        // Perulangan murni menggunakan JavaScript untuk me-render Donut Chart per MR
        dataAkurasi.forEach(function(chartReport) {
            const canvasId = "chart-donut-" + chartReport.user_id;
            const canvasElement = document.getElementById(canvasId);

            // Validasi proteksi jika elemen canvas tidak ditemukan di layar
            if (!canvasElement) return;

            const ctx = canvasElement.getContext('2d');
            const tCheckout = chartReport.checkout;
            const tCheckin = chartReport.checkin;
            const tMissed = chartReport.missed;
            const isNoData = (tCheckout === 0 && tCheckin === 0 && tMissed === 0);

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: isNoData ? ['Belum Ada Penugasan'] : ['Selesai', 'Tertunda', 'Terlewat'],
                    datasets: [{
                        data: isNoData ? [100] : [tCheckout, tCheckin, tMissed],
                        backgroundColor: isNoData ? ['#e9ecef'] : ['#28a745', '#ffc107', '#dc3545'],
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: !isNoData
                        }
                    },
                    cutout: '70%',
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });

        // =================================================================
        // 2. LOGIKA AJAX MODAL DETAIL JENDELA AUDIT (TABEL LOG TAB 2)
        // =================================================================
        const modalElement = new bootstrap.Modal(document.getElementById('modalAuditKunjungan'));

        document.querySelectorAll('.btn-detail-rekap').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Memuat Arsip...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('/admin/rekap-kunjungan/detail/' + id)
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();

                        // Mengisi komponen teks formal di dalam modal
                        document.getElementById('aud-tanggal').textContent = data.tanggal;
                        document.getElementById('aud-mr').textContent = data.mr;
                        document.getElementById('aud-toko').textContent = data.kode + ' / ' + data.nama_toko;
                        document.getElementById('aud-checkin').textContent = data.checkin;
                        document.getElementById('aud-checkout').textContent = data.checkout;
                        document.getElementById('aud-catatan').textContent = data.catatan;

                        // Pengecekan status aktivitas order komersial
                        const audOrderStatus = document.getElementById('aud-order-status');
                        if (data.order_status && data.order_status.toLowerCase() === 'order') {
                            audOrderStatus.innerHTML = '<span class="badge bg-success text-white rounded-pill px-3 py-1"><i class="fa-solid fa-cart-shopping me-1"></i> Sukses Pengambilan Order</span>';
                        } else {
                            audOrderStatus.innerHTML = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1"><i class="fa-solid fa-ban me-1"></i> Tidak Mengambil Order / Toko Tutup</span>';
                        }

                        // Mengatur visualisasi lampiran dokumentasi foto kamera
                        const imgNode = document.getElementById('aud-foto');
                        const fotoKosongNode = document.getElementById('foto-kosong');

                        if (data.foto) {
                            imgNode.src = data.foto;
                            document.getElementById('container-foto').style.display = 'block';
                            fotoKosongNode.style.display = 'none';
                        } else {
                            document.getElementById('container-foto').style.display = 'none';
                            fotoKosongNode.style.display = 'block';
                        }

                        modalElement.show();
                    })
                    .catch(error => {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Memuat',
                            text: 'Sistem mengalami kegagalan penarikan arsip data server.',
                            confirmButtonColor: '#dc3545'
                        });
                    });
            });
        });
    });
</script>
@endsection