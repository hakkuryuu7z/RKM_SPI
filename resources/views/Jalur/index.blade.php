@extends('layouts.app')

@section('title', 'Perencanaan Jalur RKM')


@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 70vh;
        min-height: 500px;
        width: 100%;
        border-radius: 12px;
        z-index: 1;
    }
</style>
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Rencana Jalur Kunjungan (RKM)</h4>
            <p class="text-muted small mb-0">Atur rute MR secara manual atau upload via Excel.</p>
        </div>
        <div>
            <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImport">
                <i class="fa-solid fa-file-excel me-2"></i> Upload Excel
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div style="width: 250px;">
                        <label class="form-label text-muted small fw-bold mb-1">Filter Peta by Salesman</label>
                        <select id="filterSalesman" class="form-select border-0 bg-light shadow-none">
                            <option value="">-- Pilih Salesman --</option>
                            @foreach($salesmanList as $sales)
                            <option value="{{ $sales }}">{{ $sales == 'TIDAK ADA' ? '❗ BELUM ADA SALESMAN' : 'Salesman: ' . $sales }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="legendColor" class="d-none align-items-center bg-light px-3 py-2 rounded-3 border">
                        <img id="legendIcon" src="" width="15" height="25" class="me-2">
                        <span id="legendText" class="fw-bold small text-dark"></span>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div id="map"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 rounded-4 shadow-sm h-100 d-flex flex-column">
                <div class="card-body p-4 flex-grow-1 d-flex flex-column">
                    <h6 class="fw-bold text-primary mb-3">Form Rute Manual</h6>

                    <form action="{{ route('jalur.store') }}" method="POST" id="formJalur" class="d-flex flex-column flex-grow-1">
                        @csrf
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label text-muted small fw-bold">Pilih MR</label>
                                <select name="jlr_user_id" class="form-select form-select-sm border-0 bg-light" required>
                                    <option value="">-- Pilih MR --</option>
                                    @foreach($mrUsers as $mr)
                                    <option value="{{ $mr->user_id }}">{{ $mr->user_username }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small fw-bold">Tanggal</label>
                                <input type="date" name="jlr_tanggal_rkm" class="form-control form-control-sm border-0 bg-light" required>
                            </div>
                        </div>

                        <hr class="my-2 opacity-25">

                        <label class="form-label text-muted small fw-bold mb-1">Cari & Filter Member</label>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <select id="filterSales" class="form-select form-select-sm bg-light border-0">
                                    <option value="">Semua Sales</option>
                                    @foreach($salesmanList as $sales)
                                    <option value="{{ $sales }}">{{ $sales }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <select id="filterKecamatan" class="form-select form-select-sm bg-light border-0">
                                    <option value="">Semua Kecamatan</option>
                                    @foreach($kecamatanList as $kec)
                                    <option value="{{ $kec }}">{{ $kec }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <input type="text" id="searchMember" class="form-control form-control-sm border-0 bg-light mb-2" placeholder="Cari nama / kode toko...">

                        <div class="mb-2">
                            <select id="availableMembers" class="form-select border-0 bg-light" multiple style="height: 120px; font-size:12px;">
                            </select>
                            <small class="text-muted" style="font-size: 10px;">*Tahan CTRL untuk pilih banyak.</small>
                        </div>

                        <button type="button" id="btnTambahkan" class="btn btn-sm btn-outline-primary fw-bold mb-3">
                            <i class="fa-solid fa-arrow-down me-1"></i> Tambahkan ke Rute
                        </button>

                        <hr class="my-0 opacity-25">

                        <div class="mt-3 flex-grow-1 d-flex flex-column">
                            <label class="form-label text-success small fw-bold mb-1">Daftar Rute (Preview Maps) : <span id="countRute">0</span> Toko</label>

                            <div id="selectedMembersList" class="border rounded bg-light p-2 flex-grow-1 overflow-auto" style="min-height: 150px; max-height: 150px;">
                                <p class="text-muted small text-center mt-4">Belum ada rute. Pilih dan tambahkan toko di atas.</p>
                            </div>

                            <div id="hiddenInputsArea"></div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold mt-3 shadow-sm" id="btnSimpan" disabled>
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Jalur RKM
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-end mb-3 flex-wrap gap-2">
                        <h6 class="fw-bold text-primary mb-0">
                            <i class="fa-solid fa-map-location-dot me-2"></i> Daftar Rencana Kunjungan (RKM) Tersimpan
                        </h6>

                        <div class="d-flex gap-2">
                            <div>
                                <label class="small text-muted fw-bold mb-1">Filter MR</label>
                                <select id="filterTableMr" class="form-select form-select-sm bg-light border-0" style="width: 150px;">
                                    <option value="">Semua MR</option>
                                    @foreach($mrUsers as $mr)
                                    <option value="{{ $mr->user_username }}">{{ $mr->user_username }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="small text-muted fw-bold mb-1">Filter Tanggal</label>
                                <input type="date" id="filterTableTanggal" class="form-control form-control-sm bg-light border-0" style="width: 150px;">
                            </div>
                            <div class="d-flex align-items-end">
                                <button class="btn btn-sm btn-secondary shadow-sm" onclick="resetFilterTable()">
                                    <i class="fa-solid fa-rotate-left me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tableRekapRute">
                            <thead class="table-light">
                                <tr>
                                    <th>MR / User</th>
                                    <th>Tanggal RKM</th>
                                    <th>Total Toko</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekapJalur as $key => $items)
                                @php
                                $firstItem = $items->first();
                                $namaMr = $firstItem->user ? $firstItem->user->user_username : 'User Terhapus';
                                // Bikin 2 versi tanggal: buat Javascript (Y-m-d) dan buat User (d F Y)
                                $rawDate = \Carbon\Carbon::parse($firstItem->jlr_tanggal_rkm)->format('Y-m-d');
                                $displayDate = \Carbon\Carbon::parse($firstItem->jlr_tanggal_rkm)->format('d F Y');
                                @endphp
                                <tr>
                                    <td class="fw-bold text-dark">{{ $namaMr }}</td>

                                    <td data-sort="{{ $rawDate }}">
                                        <span class="badge bg-secondary-subtle text-secondary border date-display" data-date="{{ $rawDate }}">
                                            {{ $displayDate }}
                                        </span>
                                    </td>

                                    <td><span class="badge bg-info shadow-sm">{{ $items->count() }} Toko</span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-warning fw-bold text-dark shadow-sm me-1"
                                            onclick="editRute('{{ $firstItem->jlr_user_id }}', '{{ $rawDate }}')">
                                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                        </button>

                                        <form action="{{ route('jalur.destroy') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="jlr_user_id" value="{{ $firstItem->jlr_user_id }}">
                                            <input type="hidden" name="jlr_tanggal_rkm" value="{{ $rawDate }}">
                                            <button type="button" class="btn btn-sm btn-danger fw-bold shadow-sm btn-hapus">
                                                <i class="fa-solid fa-trash-can me-1"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-file-import me-2"></i> Import Jalur RKM</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="alert alert-info small rounded-3 border-0 bg-info-subtle text-info-emphasis">
                    <i class="fa-solid fa-circle-info me-1"></i> Pastikan format kolom sesuai dengan sistem.
                    <a href="{{ route('jalur.template') }}" class="fw-bold text-decoration-none d-block mt-2">
                        <i class="fa-solid fa-download me-1"></i> Download Template CSV
                    </a>
                </div>

                <form action="{{ route('jalur.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="form-label small fw-bold text-muted mb-1">Pilih File (.csv / .xlsx)</label>
                    <input type="file" name="file_excel" class="form-control mb-3" accept=".csv, .xlsx" required>
                    <button type="submit" class="btn btn-success w-100 fw-bold">
                        <i class="fa-solid fa-cloud-arrow-up me-2"></i> Upload & Proses Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // ==========================================
        // A. INISIALISASI PETA & VARIABEL GLOBAL
        // ==========================================
        var map = L.map('map').setView([-6.82645430, 110.87612340], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Fix Peta Abu-abu
        setTimeout(function() {
            map.invalidateSize();
        }, 500);

        var allMembers = @json($members);
        var allSavedJalur = @json($allJalur);
        var selectedRouteMembers = []; // Nampung rute sementara

        var markersLayer = L.featureGroup().addTo(map);
        var polylineLayer = L.polyline([], {
            color: '#e11d48',
            weight: 3,
            dashArray: '5, 10',
            lineJoin: 'round'
        }).addTo(map);

        var routeIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });


        // ==========================================
        // B. FITUR FORM RUTE MANUAL (Kanan Atas)
        // ==========================================

        // 1. Filter Data Toko yang Bisa Dipilih
        function renderAvailableList() {
            var filterSales = document.getElementById('filterSales').value.toLowerCase();
            var filterKec = document.getElementById('filterKecamatan').value.toLowerCase();
            var searchTxt = document.getElementById('searchMember').value.toLowerCase();
            var availableBox = document.getElementById('availableMembers');

            availableBox.innerHTML = ''; // Kosongin dulu

            allMembers.forEach(function(m) {
                if (selectedRouteMembers.find(route => route.kode === m.kode)) return; // Kalo udah dipilih, jangan ditampilin

                var mSales = (m.cus_nosalesman || 'TIDAK ADA').toLowerCase();
                var mKec = (m.kecamatan || '').toLowerCase();
                var mNama = (m.nama || '').toLowerCase();
                var mKode = (m.kode || '').toLowerCase();

                var matchSales = (filterSales === '' || mSales === filterSales);
                var matchKec = (filterKec === '' || mKec === filterKec);
                var matchSearch = (searchTxt === '' || mNama.includes(searchTxt) || mKode.includes(searchTxt));

                if (matchSales && matchKec && matchSearch) {
                    var opt = document.createElement('option');
                    opt.value = m.kode;
                    opt.innerHTML = `${m.kode} - ${m.nama} (Jarak: ${m.jarak ? m.jarak+' KM' : '-'})`;
                    availableBox.appendChild(opt);
                }
            });
        }

        document.getElementById('filterSales').addEventListener('change', renderAvailableList);
        document.getElementById('filterKecamatan').addEventListener('change', renderAvailableList);
        document.getElementById('searchMember').addEventListener('input', renderAvailableList);

        // 2. Tombol Tambahkan ke Rute
        document.getElementById('btnTambahkan').addEventListener('click', function() {
            var selectBox = document.getElementById('availableMembers');
            var selectedOptions = Array.from(selectBox.selectedOptions);

            if (selectedOptions.length === 0) {
                return Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Pilih minimal 1 toko dulu!'
                });
            }

            selectedOptions.forEach(opt => {
                var foundMember = allMembers.find(m => m.kode === opt.value);
                if (foundMember) selectedRouteMembers.push(foundMember);
            });
            updateUI();
        });

        // 3. Fungsi Hapus Toko dari Rute Sementara
        window.removeRoute = function(kode) {
            selectedRouteMembers = selectedRouteMembers.filter(m => m.kode !== kode);
            updateUI();
        };

        // 4. Update UI: Form, Map Marker, dan Garis
        function updateUI() {
            renderAvailableList();
            var listUI = document.getElementById('selectedMembersList');
            var hiddenArea = document.getElementById('hiddenInputsArea');
            var countLabel = document.getElementById('countRute');
            var btnSimpan = document.getElementById('btnSimpan');

            listUI.innerHTML = '';
            hiddenArea.innerHTML = '';
            markersLayer.clearLayers();
            var mapRoutePoints = [];

            countLabel.innerText = selectedRouteMembers.length;

            if (selectedRouteMembers.length === 0) {
                listUI.innerHTML = '<p class="text-muted small text-center mt-4">Belum ada rute.</p>';
                polylineLayer.setLatLngs([]);
                btnSimpan.disabled = true;
                return;
            }

            btnSimpan.disabled = false;

            selectedRouteMembers.forEach(function(m, index) {
                // Tampilan List HTML
                listUI.innerHTML += `
                    <div class="d-flex justify-content-between align-items-center bg-white p-2 mb-1 rounded border shadow-sm" style="font-size: 12px;">
                        <div>
                            <span class="badge bg-primary rounded-pill me-1">${index + 1}</span>
                            <span class="fw-bold text-dark">${m.nama}</span><br>
                            <span class="text-muted">${m.kode} | Jarak: ${m.jarak ? m.jarak+' KM' : '-'}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-light text-danger p-1" onclick="removeRoute('${m.kode}')">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                `;

                // Input Hidden buat Controller
                hiddenArea.innerHTML += `<input type="hidden" name="jlr_kodemember[]" value="${m.kode}">`;

                // Render PIN ke Peta
                if (m.lat && m.lng) {
                    mapRoutePoints.push([m.lat, m.lng]);
                    var marker = L.marker([m.lat, m.lng], {
                        icon: routeIcon
                    });
                    marker.bindPopup(`<b>Urutan ke-${index + 1}</b><br>${m.nama}`);
                    markersLayer.addLayer(marker);
                }
            });

            // Tarik Garis Polyline & Auto Zoom Peta
            polylineLayer.setLatLngs(mapRoutePoints);
            if (mapRoutePoints.length > 0) {
                map.fitBounds(markersLayer.getBounds(), {
                    padding: [40, 40]
                });
            }
        }

        renderAvailableList(); // Panggil pertama kali


        // ==========================================
        // C. FITUR DATATABLES & TABEL BAWAH
        // ==========================================

        var tableRekap = $('#tableRekapRute').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            ordering: true,
            order: [
                [1, 'desc']
            ] // Urut berdasarkan tanggal
        });

        // Filter Custom DataTables (MR & Tanggal)
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var filterMr = $('#filterTableMr').val().toLowerCase();
            var filterTgl = $('#filterTableTanggal').val();
            var rowMr = data[0].toLowerCase();
            var rowTgl = $(tableRekap.row(dataIndex).node()).find('.date-display').data('date');

            var matchMr = (filterMr === "" || rowMr.includes(filterMr));
            var matchTgl = (filterTgl === "" || rowTgl === filterTgl);
            return matchMr && matchTgl;
        });

        $('#filterTableMr, #filterTableTanggal').on('change', function() {
            tableRekap.draw();
        });

        window.resetFilterTable = function() {
            $('#filterTableMr').val('');
            $('#filterTableTanggal').val('');
            tableRekap.draw();
        };


        // ==========================================
        // D. FITUR EDIT & DELETE RUTE
        // ==========================================

        // 1. Tombol Edit
        window.editRute = function(userId, tanggal) {
            document.querySelector('select[name="jlr_user_id"]').value = userId;
            document.querySelector('input[name="jlr_tanggal_rkm"]').value = tanggal;
            selectedRouteMembers = [];

            var ruteLama = allSavedJalur.filter(j => j.jlr_user_id == userId && j.jlr_tanggal_rkm.includes(tanggal));
            ruteLama.forEach(function(item) {
                var foundMember = allMembers.find(m => m.kode === item.jlr_kodemember);
                if (foundMember) selectedRouteMembers.push(foundMember);
            });

            updateUI();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });

            Swal.fire({
                icon: 'info',
                title: 'Mode Edit Aktif',
                text: 'Data rute ditarik ke form! Tambah/hapus toko lalu klik Simpan.',
                timer: 3000,
                showConfirmButton: false
            });
        };

        // 2. Tombol Hapus (Pakai Event Delegation)
        $('#tableRekapRute tbody').on('click', '.btn-hapus', function() {
            var form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus Rute RKM?',
                text: "Seluruh rute toko pada tanggal & MR ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });

        // ==========================================
        // E. ALERT SESSION BERHASIL / GAGAL DARI CONTROLLER
        // ==========================================
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: @json(session('success')),
            timer: 3000,
            showConfirmButton: false
        });
        @endif

        @if(count($errors) > 0)
        Swal.fire({
            icon: 'error',
            title: 'Gagal Menyimpan!',
            html: '{!! implode("<br>", $errors->all()) !!}'
        });
        @endif

    }); // Penutup DOMContentLoaded
</script>
@endpush