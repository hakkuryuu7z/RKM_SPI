@extends('layouts.app')

@section('title', 'Detail Kunjungan')

@section('content')

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Gagal Keluar!',
            text: '{{ session('
            error ') }}'
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
            text: '{{ $errors->first() }}'
        });
    });
</script>
@endif
<div class="container-fluid px-3 pb-5" style="max-width: 500px; margin: 0 auto;">

    <div class="mb-3 mt-2">
        <a href="{{ route('dashboard') }}" class="text-decoration-none text-secondary small fw-bold">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Rute
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
                <small class="text-muted d-block"><i class="fa-solid fa-barcode me-1"></i> Kode Member</small>
                <span class="fw-semibold text-dark">{{ $tugas->jlr_kodemember }}</span>
            </div>
            <div>
                <small class="text-muted d-block"><i class="fa-solid fa-phone me-1"></i> No. HP Toko</small>
                @if($m && $m->hp)
                <a href="tel:{{ $m->hp }}" class="text-decoration-none fw-semibold text-primary">{{ $m->hp }}</a>
                @else
                <span class="fw-semibold text-muted">-</span>
                @endif
            </div>
            <div>
                <small class="text-muted d-block"><i class="fa-solid fa-map-location-dot me-1"></i> Wilayah (Kelurahan)</small>
                <span class="fw-semibold text-dark">{{ $m && $m->kelurahan ? $m->kelurahan : '-' }}</span>
            </div>
            <div>
                <small class="text-muted d-block"><i class="fa-solid fa-location-dot me-1"></i> Koordinat Toko</small>
                @if($m && $m->lat && $m->lng)
                <span class="fw-semibold text-dark d-block small mb-2" id="target-koordinat">{{ $m->lat }},{{ $m->lng }}</span>
                @else
                <span class="fw-semibold text-muted d-block mt-1" id="target-koordinat">kosong</span>
                @endif
            </div>
        </div>
    </div>

    @if(!$kunjungan || $kunjungan->status_kunjungan == 'BELUM')
    <div class="card border-0 rounded-4 shadow-sm bg-white p-4 text-center">
        <i class="fa-solid fa-location-crosshairs fa-2x text-primary mb-3"></i>
        <h6 class="fw-bold text-dark">Validasi Lokasi GPS</h6>
        <p class="small text-muted mb-4">Sistem akan mencocokkan lokasi GPS HP Anda dengan posisi toko saat ini.</p>

        <form id="form-checkin" action="{{ route('mr.checkin.store', ['id' => $tugas->getKey()]) }}" method="POST">
            @csrf
            <input type="hidden" name="lat_mr" id="lat_mr">
            <input type="hidden" name="lng_mr" id="lng_mr">

            <button type="button" id="btn-checkin" data-radius="{{ $setting->radius_meter ?? 50 }}" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm">
                <i class="fa-solid fa-right-to-bracket me-2"></i> MASUK TOKO (CHECK-IN)
            </button>
        </form>
    </div>

    @elseif($kunjungan->status_kunjungan == 'CHECKIN')
    <div class="card border-0 rounded-4 shadow-sm bg-white p-4">
        <div class="alert alert-success border-0 rounded-3 d-flex align-items-center gap-2 mb-4" style="font-size: 0.85rem;">
            <i class="fa-solid fa-circle-check fa-lg"></i>
            <div>Anda sudah Check-In pada jam <b>{{ \Carbon\Carbon::parse($kunjungan->waktu_checkin)->format('H:i') }} WIB</b></div>
        </div>

        <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-file-pen me-1 text-primary"></i> Laporan Kunjungan Toko</h6>

        <form action="{{ route('mr.checkout.store', $kunjungan->rkm_id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Status Kunjungan / Order</label>
                <select class="form-select" name="rkm_order_status" id="rkm_order_status" required>
                    <option value="Order">Toko Buka & Ada Orderan</option>
                    <option value="Tidak">Toko Buka & Tidak Ada Orderan</option>
                    <option value="Tutup">⚠️ Toko Tutup / Gembok</option>
                </select>
            </div>

            <div class="mb-3" id="wrapper-trx">
                <label class="form-label small fw-bold text-secondary">No. Transaksi (Nota/TRX)</label>
                <input type="text" class="form-control" name="rkm_trx" placeholder="Masukkan nomor nota penjualan">
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Catatan / Keterangan Kunjungan</label>
                <textarea class="form-control" name="rkm_keteranganmember" rows="3" placeholder="Contoh: Owner sedang keluar / Toko tutup gembok" required></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Foto Bukti Kunjungan</label>
                <input type="file" class="form-control" name="foto_kunjungan" accept="image/*" required>
                <small class="text-muted d-block mt-1">Bolo bisa foto langsung atau ambil dari galeri HP.</small>
            </div>

            <button type="submit" class="btn btn-danger w-100 py-3 rounded-3 fw-bold shadow-sm">
                <i class="fa-solid fa-right-from-bracket me-2"></i> SIMPAN & KELUAR TOKO (CHECK-OUT)
            </button>
        </form>
    </div>

    @else
    <div class="card border-0 rounded-4 shadow-sm bg-success text-white p-4 text-center">
        <i class="fa-solid fa-circle-check fa-3x mb-3"></i>
        <h5 class="fw-bold">Kunjungan Selesai</h5>
        <p class="small mb-0 opacity-75">Toko ini sudah selesai dikunjungi hari ini bolo. Mantap!</p>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- LOGIKA TOGGLE FORM & AUTO-FILL TOKO TUTUP ---
        const selectStatus = document.getElementById('rkm_order_status');
        const wrapperTrx = document.getElementById('wrapper-trx');
        const inputKeterangan = document.querySelector('textarea[name="rkm_keteranganmember"]');

        if (selectStatus && wrapperTrx) {
            selectStatus.addEventListener('change', function() {
                if (this.value === 'Order') {
                    wrapperTrx.style.display = 'block';
                    if (inputKeterangan.value === 'Toko Tutup / Gembok') {
                        inputKeterangan.value = '';
                    }
                } else if (this.value === 'Tutup') {
                    wrapperTrx.style.display = 'none';
                    inputKeterangan.value = 'Toko Tutup / Gembok';
                } else {
                    wrapperTrx.style.display = 'none';
                    if (inputKeterangan.value === 'Toko Tutup / Gembok') {
                        inputKeterangan.value = '';
                    }
                }
            });
        }

        // --- RUMUS MURNI HITUNG JARAK (HAVERSINE FORMULA) ---
        function hitungJarakMeter(lat1, lon1, lat2, lon2) {
            const R = 6371000; // Radius Bumi dalam satuan Meter
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c; // Hasil akhir berbentuk Meter
        }

        // --- SENSOR GPS AKTIF MURNI ---
        const btnCheckIn = document.getElementById('btn-checkin');
        if (btnCheckIn) {
            const batasRadius = Number(btnCheckIn.dataset.radius);

            btnCheckIn.addEventListener('click', function() {
                const targetTikor = document.getElementById('target-koordinat').innerText.trim();
                if (targetTikor === 'kosong' || !targetTikor.includes(',')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Waduh...',
                        text: 'Toko ini belum memiliki data koordinat GPS bawaan!'
                    });
                    return;
                }
                const [targetLat, targetLng] = targetTikor.split(',').map(Number);

                // Ubah status tombol jadi loading
                btnCheckIn.disabled = true;
                btnCheckIn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Menembak Satelit GPS...`;

                // Panggil Sensor Hardware HP
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const mrLat = position.coords.latitude;
                        const mrLng = position.coords.longitude;

                        // Hitung Jarak Asli antara HP MR dan Titik Toko
                        const jarakMeter = hitungJarakMeter(mrLat, mrLng, targetLat, targetLng);
                        const jarakFix = jarakMeter.toFixed(1);

                        if (jarakMeter > batasRadius) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Check-In Ditolak!',
                                html: `Jarak lu saat ini <b>${jarakFix} Meter</b> dari toko.<br>Sedangkan batas maksimal admin adalah <b>${batasRadius} Meter</b>.<br>Mendekat lagi bolo!`,
                            });
                            resetButton();
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Lokasi Valid!',
                                text: `Jarak lu ${jarakFix} Meter dari toko. Menyiapkan absen masuk...`,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // Masukkan koordinat asli HP ke input hidden form
                            document.getElementById('lat_mr').value = mrLat;
                            document.getElementById('lng_mr').value = mrLng;

                            setTimeout(() => {
                                document.getElementById('form-checkin').submit();
                            }, 1500);
                        }
                    },
                    function(error) {
                        let pesanError = "Gagal mengunci lokasi lu bolo.";
                        if (error.code === error.TIMEOUT) {
                            pesanError = "<b>Waktu pencarian habis (Timeout)!</b><br>Sinyal hardware GPS lemah di dalam ruangan.";
                        } else if (error.code === error.POSITION_UNAVAILABLE) {
                            pesanError = "<b>Lokasi tidak tersedia!</b><br>Sistem HP gagal mendeteksi koordinat.";
                        } else if (error.code === error.PERMISSION_DENIED) {
                            pesanError = "<b>Izin lokasi ditolak!</b>";
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Masalah GPS',
                            html: pesanError
                        });
                        resetButton();
                    }, {
                        enableHighAccuracy: false, // Gunakan false dulu biar dibantu jaringan internet/BTS pas di dalem ruangan
                        timeout: 15000, // Kita kasih nafas sensor nyari posisi selama 15 detik
                        maximumAge: 0
                    }
                );
            });
        }

        function resetButton() {
            btnCheckIn.disabled = false;
            btnCheckIn.innerHTML = `<i class="fa-solid fa-right-to-bracket me-2"></i> MASUK TOKO (CHECK-IN)`;
        }
    });
</script>
@endpush