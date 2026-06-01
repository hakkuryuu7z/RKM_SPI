@extends('layouts.app')

@section('title', 'Detail Kunjungan')

@section('content')

@if(session('error'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'error',
            title: 'Gagal Keluar Toko!',
            text: "{!! session('error') !!}",
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Mengerti'
        });
    });
</script>
@endif

@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'success',
            title: 'Transaksi Berhasil!',
            text: "{!! session('success') !!}",
            confirmButtonColor: '#198754',
            confirmButtonText: 'Ok'
        });
    });
</script>
@endif

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Validasi Ditolak!',
            text: '{{ $errors->first() }}',
            confirmButtonColor: '#dc3545'
        });
    });
</script>
@endif

<div class="container-fluid px-3 pb-5" style="max-width: 500px; margin: 0 auto;">

    <div class="mb-3 mt-2">
        <a href="{{ route('dashboard') }}" class="text-decoration-none text-secondary small fw-bold">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Rencana Rute
        </a>
    </div>

    <div class="card border-0 rounded-4 shadow-sm bg-white p-4 mb-4">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="bg-primary-subtle text-primary rounded-3 p-2 d-inline-block">
                <i class="fa-solid fa-store fa-lg"></i>
            </div>
            <div>
                <span class="badge bg-secondary-subtle text-secondary small mb-1">Target Kunjungan</span>
                <h5 class="fw-bold text-dark mb-0">{{ $tugas->member ? $tugas->member->nama : 'Toko Tanpa Nama' }}</h5>
            </div>
        </div>

        <hr class="text-muted opacity-25">

        @php $m = $tugas->member; @endphp
        <div class="d-flex flex-column gap-3 mt-2">
            <div>
                <small class="text-muted d-block"><i class="fa-solid fa-barcode me-1"></i> Kode Member Outlet</small>
                <span class="fw-semibold text-dark">{{ $tugas->jlr_kodemember }}</span>
            </div>
            <div>
                <small class="text-muted d-block"><i class="fa-solid fa-phone me-1"></i> Nomor Telepon Outlet</small>
                @if($m && $m->hp)
                <a href="tel:{{ $m->hp }}" class="text-decoration-none fw-semibold text-primary">{{ $m->hp }}</a>
                @else
                <span class="fw-semibold text-muted">-</span>
                @endif
            </div>
            <div>
                <small class="text-muted d-block"><i class="fa-solid fa-map-location-dot me-1"></i> Wilayah Teritorial (Kelurahan)</small>
                <span class="fw-semibold text-dark">{{ $m && $m->kelurahan ? $m->kelurahan : '-' }}</span>
            </div>
            <div class="mb-2">
                <small class="text-muted d-block"><i class="fa-solid fa-location-crosshairs me-1"></i> Titik Koordinat Acuan</small>
                <span class="fw-bold text-dark small" id="target-koordinat">
                    {{ $m && $m->koordinat ? $m->koordinat : 'kosong' }}
                </span>
            </div>

            @if($m && $m->koordinat && $m->koordinat != 'kosong')
            <div class="mt-3">
                <a href="https://maps.google.com/?q={{ urlencode($m->koordinat) }}"
                    target="_blank"
                    class="btn btn-outline-danger btn-sm w-100 fw-bold rounded-2 pt-2 pb-2 shadow-sm">
                    <i class="fa-solid fa-map-location-dot me-2"></i> Navigasi Google Maps
                </a>
            </div>
            @endif
        </div>
    </div>

    @if(!$kunjungan || $kunjungan->status_kunjungan == 'BELUM')
    <div class="card border-0 rounded-4 shadow-sm bg-white p-4 text-center">
        <i class="fa-solid fa-location-crosshairs fa-2x text-primary mb-3"></i>
        <h6 class="fw-bold text-dark">Validasi Geofencing Lokasi GPS</h6>
        <p class="small text-muted mb-4">Sistem akan mencocokkan lokasi GPS perangkat Anda dengan radius posisi resmi outlet saat ini.</p>

        <form id="form-checkin" action="{{ route('mr.checkin.store', ['id' => $tugas->getKey()]) }}" method="POST">
            @csrf
            <input type="hidden" name="lat_mr" id="lat_mr">
            <input type="hidden" name="lng_mr" id="lng_mr">

            <button type="button" id="btn-checkin" data-radius="{{ $setting->radius_meter ?? 50 }}" class="btn btn-primary w-100 fw-bold rounded-2 pt-2 pb-2 shadow-sm">
                <i class="fa-solid fa-right-to-bracket me-2"></i> MASUK TOKO (CHECK-IN)
            </button>
        </form>
    </div>

    @elseif($kunjungan->status_kunjungan == 'CHECKIN')
    <div class="card border-0 rounded-4 shadow-sm bg-white p-4">
        <div class="alert alert-success border-0 rounded-3 d-flex align-items-center gap-2 mb-4" style="font-size: 0.85rem;">
            <i class="fa-solid fa-circle-check fa-lg"></i>
            <div>Konfirmasi: Anda telah melakukan Check-In pada pukul <b>{{ \Carbon\Carbon::parse($kunjungan->waktu_checkin)->format('H:i') }} WIB</b></div>
        </div>

        <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-file-pen me-1 text-primary"></i> Form Laporan Realisasi Kunjungan</h6>

        <form id="form-checkout" action="{{ route('mr.checkout.store', $kunjungan->rkm_id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Status Operasional / Hasil Order</label>
                <select class="form-select" name="rkm_order_status" id="rkm_order_status" required>
                    <option value="Order">Outlet Buka & Terdapat Penjualan (Order)</option>
                    <option value="Tidak">Outlet Buka & Tidak Terdapat Penjualan</option>
                    <option value="Tutup">⚠️ Outlet Tutup / Gembok</option>
                </select>
            </div>

            <div class="mb-3" id="wrapper-trx">
                <label class="form-label small fw-bold text-secondary">Nomor Dokumen Transaksi (Nota / TRX)</label>
                <input type="text" class="form-control" name="rkm_trx" placeholder="Masukkan nomor nota transaksi resmi">
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Catatan Ringkasan Laporan Lapangan</label>
                <textarea class="form-control" name="rkm_keteranganmember" rows="3" placeholder="Deskripsikan kondisi aktual outlet (Contoh: Bertemu penanggung jawab, toko sedang renovasi, dll)" required></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Dokumentasi Kamera Bukti Lapangan</label>
                <input type="file" class="form-control" name="foto_kunjungan[]" accept="image/*" multiple required>
                <small class="text-muted d-block mt-1">Anda dapat memilih atau mengambil lebih dari satu foto sekaligus sebagai bukti otentik lapangan.</small>
            </div>

            <button type="button" id="btn-checkout" data-radius="{{ $setting->radius_meter ?? 50 }}" class="btn btn-danger w-100 fw-bold rounded-2 pt-2 pb-2 shadow-sm">
                <i class="fa-solid fa-right-from-bracket me-2"></i> SIMPAN & SELESAIKAN TUGAS (CHECK-OUT)
            </button>
        </form>
    </div>

    @else
    <div class="card border-0 rounded-4 shadow-sm bg-success text-white p-4 text-center">
        <i class="fa-solid fa-circle-check fa-3x mb-3"></i>
        <h5 class="fw-bold">Pelaksanaan Tugas Selesai</h5>
        <p class="small mb-0 opacity-75">Aktivitas pelaporan untuk tujuan outlet ini telah terekam secara permanen di dalam sistem server untuk hari ini.</p>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. LOGIKA INTERAKTIF FORM TOGGLE STATUS OPERASIONAL ---
        const selectStatus = document.getElementById('rkm_order_status');
        const wrapperTrx = document.getElementById('wrapper-trx');
        const inputKeterangan = document.querySelector('textarea[name="rkm_keteranganmember"]');

        if (selectStatus && wrapperTrx) {
            selectStatus.addEventListener('change', function() {
                if (this.value === 'Order') {
                    wrapperTrx.style.display = 'block';
                    if (inputKeterangan.value === 'Outlet Tutup / Gembok') {
                        inputKeterangan.value = '';
                    }
                } else if (this.value === 'Tutup') {
                    wrapperTrx.style.display = 'none';
                    inputKeterangan.value = 'Outlet Tutup / Gembok';
                } else {
                    wrapperTrx.style.display = 'none';
                    if (inputKeterangan.value === 'Outlet Tutup / Gembok') {
                        inputKeterangan.value = '';
                    }
                }
            });
        }

        // --- 2. FORMULA MATHEMATICAL GEOFENCING (HAVERSINE) ---
        function hitungJarakMeter(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        // --- 3. EKSTRAKSI DATA ACUAN KOORDINAT OUTLET ---
        const targetTikor = document.getElementById('target-koordinat').innerText.trim();
        let targetLat = 0,
            targetLng = 0;
        if (targetTikor !== 'kosong' && targetTikor.includes(',')) {
            [targetLat, targetLng] = targetTikor.split(',').map(Number);
        }

        // === 🔓 PROSES VALIDASI CHECK-IN ===
        const btnCheckIn = document.getElementById('btn-checkin');
        if (btnCheckIn) {
            const batasRadius = Number(btnCheckIn.dataset.radius);

            btnCheckIn.addEventListener('click', function() {
                if (targetTikor === 'kosong') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Terkunci',
                        text: 'Koordinat lokasi acuan outlet belum terdaftar pada sistem database.'
                    });
                    return;
                }

                btnCheckIn.disabled = true;
                btnCheckIn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Menyinkronkan Koordinat Satelit GPS...`;

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const mrLat = position.coords.latitude;
                        const mrLng = position.coords.longitude;
                        const jarakMeter = hitungJarakMeter(mrLat, mrLng, targetLat, targetLng);

                        if (jarakMeter > batasRadius) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Proses Check-In Ditolak!',
                                html: `Posisi Anda terdeteksi berada <b>${jarakMeter.toFixed(1)} Meter</b> dari outlet. Batas maksimal toleransi radius kerja adalah <b>${batasRadius} Meter</b>.`,
                                confirmButtonColor: '#dc3545'
                            });
                            btnCheckIn.disabled = false;
                            btnCheckIn.innerHTML = `<i class="fa-solid fa-right-to-bracket me-2"></i> MASUK TOKO (CHECK-IN)`;
                        } else {
                            document.getElementById('lat_mr').value = mrLat;
                            document.getElementById('lng_mr').value = mrLng;
                            document.getElementById('form-checkin').submit();
                        }
                    },
                    function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kegagalan Sinyal Perangkat',
                            text: 'Gagal mengunci titik koordinat GPS eksternal. Periksa pengaturan lokasi perangkat Anda.'
                        });
                        btnCheckIn.disabled = false;
                        btnCheckIn.innerHTML = `<i class="fa-solid fa-right-to-bracket me-2"></i> MASUK TOKO (CHECK-IN)`;
                    }, {
                        enableHighAccuracy: true,
                        timeout: 15000
                    }
                );
            });
        }

        // === 🔓 PROSES VALIDASI CHECK-OUT ===
        const btnCheckOut = document.getElementById('btn-checkout');
        const formCheckout = document.getElementById('form-checkout');

        if (btnCheckOut && formCheckout) {
            const batasRadius = Number(btnCheckOut.dataset.radius);

            btnCheckOut.addEventListener('click', function() {
                if (!formCheckout.reportValidity()) {
                    return;
                }

                if (targetTikor === 'kosong') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Tergembok',
                        text: 'Penyimpanan ditolak karena data koordinat outlet tidak valid. Segera hubungi Departemen EDP.'
                    });
                    return;
                }

                btnCheckOut.disabled = true;
                btnCheckOut.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Memverifikasi Jarak Aktual Checkout...`;

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const currentLat = position.coords.latitude;
                        const currentLng = position.coords.longitude;
                        const jarakMeter = hitungJarakMeter(currentLat, currentLng, targetLat, targetLng);

                        if (jarakMeter > batasRadius) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Proses Check-Out Ditolak!',
                                html: `Anda berada <b>${jarakMeter.toFixed(1)} Meter</b> di luar batas area kerja outlet. Silakan kembali ke radius dalam outlet untuk menutup kunjungan resmi.`,
                                confirmButtonColor: '#dc3545'
                            });
                            btnCheckOut.disabled = false;
                            btnCheckOut.innerHTML = `<i class="fa-solid fa-right-from-bracket me-2"></i> SIMPAN & SELESAIKAN TUGAS (CHECK-OUT)`;
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Lokasi Terverifikasi Valid!',
                                text: 'Mengunggah data berkas laporan operasional ke server pusat...',
                                showConfirmButton: false,
                                timer: 1500
                            });

                            setTimeout(() => {
                                formCheckout.submit();
                            }, 1500);
                        }
                    },
                    function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Geofencing Gagal',
                            text: 'Sistem tidak dapat memvalidasi posisi perimeter Anda. Pastikan modul GPS eksternal perangkat aktif.'
                        });
                        btnCheckOut.disabled = false;
                        btnCheckOut.innerHTML = `<i class="fa-solid fa-right-from-bracket me-2"></i> SIMPAN & SELESAIKAN TUGAS (CHECK-OUT)`;
                    }, {
                        enableHighAccuracy: true,
                        timeout: 15000
                    }
                );
            });
        }
    });
</script>
@endpush