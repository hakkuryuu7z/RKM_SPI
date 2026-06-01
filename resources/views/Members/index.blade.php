@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Data Master Member</h4>
            <small class="text-muted">Manajemen basis data outlet, segmentasi, dan koordinat posisi member retail</small>
        </div>
        <div>
            <a href="{{ route('members.sync') }}" id="btn-sync-api" class="btn btn-primary fw-semibold shadow-sm px-3 py-2">
                <i class="fa-solid fa-rotate me-2"></i> Sinkronisasi API Pusat
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('members.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-secondary">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 ps-0 text-dark"
                            placeholder="Cari kode, nama, kota, atau salesman..." value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-dark fw-semibold px-3">
                        <i class="fa-solid fa-filter me-1"></i> Terapkan Filter
                    </button>
                    @if(!empty($search))
                    <a href="{{ route('members.index') }}" class="btn btn-light border fw-semibold ms-1">
                        Reset
                    </a>
                    @endif
                </div>
                <div class="col-12 col-md-5 text-md-end">
                    <span class="text-muted small me-2 d-none d-xl-inline-block">Format Ekspor Dokumen:</span>
                    <a href="{{ route('members.export.excel', ['search' => $search]) }}" class="btn btn-sm btn-success fw-semibold px-3 py-2 shadow-sm rounded-2">
                        <i class="fa-solid fa-file-excel me-2"></i> Unduh Excel
                    </a>
                    <a href="{{ route('members.export.pdf', ['search' => $search]) }}" target="_blank" class="btn btn-sm btn-danger fw-semibold px-3 py-2 shadow-sm rounded-2 ms-1">
                        <i class="fa-solid fa-file-pdf me-2"></i> Cetak PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap" id="table-master-members">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-3">No.</th>
                            <th>Kode Member</th>
                            <th>Nama Member / Outlet</th>
                            <th>Wilayah & Alamat</th>
                            <th>No. Salesman</th>
                            <th class="text-center">Koordinat GPS</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($members as $index => $member)
                        <tr>
                            <td class="ps-3 text-secondary fw-medium">
                                {{ $members->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1 rounded">
                                    {{ $member->kode }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $member->nama }}</div>
                                <small class="text-muted d-block">
                                    {{ $member->nama_outlet ?? 'Sub Outlet: ' . ($member->nama_sub_outlet ?? '-') }}
                                </small>
                            </td>
                            <td>
                                <div class="text-wrap text-secondary" style="max-width: 260px;">{{ $member->alamat }}</div>
                                <small class="text-muted fw-semibold">{{ $member->kecamatan ?? '-' }}, {{ $member->kota }}</small>
                            </td>
                            <td>
                                <span class="text-dark fw-medium">{{ $member->cus_nosalesman ?? '-' }}</span>
                            </td>
                            <td class="text-center">
                                @if($member->lat && $member->lng)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $member->lat }},{{ $member->lng }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1">
                                    <i class="fa-solid fa-location-dot me-1"></i> Terpeta ({{ $member->jarak ?? 0 }}m)
                                </a>
                                @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1">
                                    <i class="fa-solid fa-unlink me-1"></i> Belum Ada Titik
                                </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(strtoupper($member->status) == 'AKTIF' || strtoupper($member->status) == 'A')
                                <span class="badge bg-success rounded-pill px-3 py-1 fw-bold">AKTIF</span>
                                @else
                                <span class="badge bg-secondary rounded-pill px-3 py-1 fw-bold">NON-AKTIF</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button"
                                    class="btn btn-sm btn-light border rounded-pill px-3 btn-view-detail"
                                    data-kode="{{ $member->kode }}"
                                    data-nama="{{ $member->nama }}"
                                    data-status="{{ $member->status }}"
                                    data-outlet="{{ $member->nama_outlet ?? '-' }}"
                                    data-suboutlet="{{ $member->nama_sub_outlet ?? '-' }}"
                                    data-alamat="{{ $member->alamat }}"
                                    data-alamat2="{{ $member->alamat_2 ?? '-' }}"
                                    data-kota="{{ $member->kota }}"
                                    data-kecamatan="{{ $member->kecamatan ?? '-' }}"
                                    data-kelurahan="{{ $member->kelurahan ?? '-' }}"
                                    data-telepon="{{ $member->telepon ?? '-' }}"
                                    data-hp="{{ $member->hp ?? '-' }}"
                                    data-cp1="{{ $member->contact_person1 ?? '-' }}"
                                    data-cp2="{{ $member->contact_person2 ?? '-' }}"
                                    data-salesman="{{ $member->cus_nosalesman ?? '-' }}"
                                    data-segmen="{{ $member->nama_segmen ?? '-' }}"
                                    data-registrasi="{{ $member->tgl_registrasi ? \Carbon\Carbon::parse($member->tgl_registrasi)->format('d-m-Y H:i:s') : '-' }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDetailMember">
                                    <i class="fa-solid fa-circle-info text-primary me-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users-slash d-block fa-3x mb-3 text-secondary"></i>
                                Tidak ditemukan data member yang sesuai dengan parameter pencarian Anda.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 px-2">
                <div class="text-muted small">
                    Menampilkan {{ $members->firstItem() ?? 0 }} sampai {{ $members->lastItem() ?? 0 }} dari total {{ $members->total() }} data member.
                </div>
                <div>
                    {{ $members->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalDetailMember" tabindex="-1" aria-labelledby="modalDetailMemberLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold text-dark" id="modalDetailMemberLabel">
                    <i class="fa-solid fa-store text-primary me-2"></i>Informasi Rinci Member / Outlet
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold d-block mb-1">Kode Member Master</label>
                        <div class="fw-bold text-primary p-2 bg-light rounded" id="md-kode">-</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold d-block mb-1">Status Keaktifan</label>
                        <div class="fw-bold p-2 bg-light rounded" id="md-status">-</div>
                    </div>
                    <div class="col-md-12">
                        <label class="text-muted small fw-semibold d-block mb-1">Nama Member / Pelanggan</label>
                        <div class="fw-bold text-dark border-bottom pb-2 fs-6" id="md-nama">-</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold d-block mb-1">Nama Struktur Outlet</label>
                        <div class="text-secondary" id="md-outlet">-</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold d-block mb-1">Nama Sub-Outlet</label>
                        <div class="text-secondary" id="md-suboutlet">-</div>
                    </div>

                    <hr class="text-muted my-3">

                    <div class="col-md-12">
                        <label class="text-muted small fw-semibold d-block mb-1">Alamat Utama (Lokasi Distribusi)</label>
                        <div class="text-dark fw-medium" id="md-alamat">-</div>
                    </div>
                    <div class="col-md-12">
                        <label class="text-muted small fw-semibold d-block mb-1">Alamat Sekunder (Alternatif)</label>
                        <div class="text-secondary" id="md-alamat2">-</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small fw-semibold d-block mb-1">Kelurahan</label>
                        <div class="text-dark" id="md-kelurahan">-</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small fw-semibold d-block mb-1">Kecamatan</label>
                        <div class="text-dark" id="md-kecamatan">-</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small fw-semibold d-block mb-1">Kota / Kabupaten</label>
                        <div class="text-dark" id="md-kota">-</div>
                    </div>

                    <hr class="text-muted my-3">

                    <div class="col-md-4">
                        <label class="text-muted small fw-semibold d-block mb-1">No. Telepon Rumah</label>
                        <div class="text-dark" id="md-telepon">-</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small fw-semibold d-block mb-1">No. Handphone (HP)</label>
                        <div class="text-dark fw-bold" id="md-hp">-</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small fw-semibold d-block mb-1">No. Salesman Terkait</label>
                        <div class="text-dark" id="md-salesman">-</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small fw-semibold d-block mb-1">Contact Person 1</label>
                        <div class="text-secondary" id="md-cp1">-</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small fw-semibold d-block mb-1">Contact Person 2</label>
                        <div class="text-secondary" id="md-cp2">-</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small fw-semibold d-block mb-1">Klasifikasi Segmen</label>
                        <div class="badge bg-secondary-subtle text-secondary px-2 py-1" id="md-segmen">-</div>
                    </div>
                    <div class="col-md-12">
                        <label class="text-muted small fw-semibold d-block mb-1">Waktu Hubung Registrasi Sistem</label>
                        <div class="text-muted small" id="md-registrasi">-</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Tutup Jendela</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // INTERSEPTOR LOADING ANIMATION UNTUK SINKRONISASI API
        const btnSync = document.getElementById('btn-sync-api');
        if (btnSync) {
            btnSync.addEventListener('click', function(e) {
                e.preventDefault();
                const targetUrl = this.getAttribute('href');

                Swal.fire({
                    title: 'Proses Sinkronisasi',
                    text: 'Sedang mengunduh dan memperbarui data master koordinat member dari API pusat. Mohon tidak menutup jendela atau memuat ulang halaman ini.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                window.location.href = targetUrl;
            });
        }

        // POPUP ALERT BERHASIL
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sinkronisasi Berhasil',
            text: "{!! session('success') !!}",
            confirmButtonColor: '#198754',
            confirmButtonText: 'Selesai'
        });
        @endif

        // POPUP ALERT GAGAL
        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Sinkronisasi Gagal',
            text: "{!! session('error') !!}",
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Kembali'
        });
        @endif

        // MAPPING DATA ATTRIBUTES KE MODAL DETAIL
        const modalDetail = document.getElementById('modalDetailMember');
        if (modalDetail) {
            modalDetail.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                document.getElementById('md-kode').textContent = button.getAttribute('data-kode');
                document.getElementById('md-nama').textContent = button.getAttribute('data-nama');
                document.getElementById('md-outlet').textContent = button.getAttribute('data-outlet');
                document.getElementById('md-suboutlet').textContent = button.getAttribute('data-suboutlet');
                document.getElementById('md-alamat').textContent = button.getAttribute('data-alamat');
                document.getElementById('md-alamat2').textContent = button.getAttribute('data-alamat2');
                document.getElementById('md-kelurahan').textContent = button.getAttribute('data-kelurahan');
                document.getElementById('md-kecamatan').textContent = button.getAttribute('data-kecamatan');
                document.getElementById('md-kota').textContent = button.getAttribute('data-kota');
                document.getElementById('md-telepon').textContent = button.getAttribute('data-telepon');
                document.getElementById('md-hp').textContent = button.getAttribute('data-hp');
                document.getElementById('md-salesman').textContent = button.getAttribute('data-salesman');
                document.getElementById('md-cp1').textContent = button.getAttribute('data-cp1');
                document.getElementById('md-cp2').textContent = button.getAttribute('data-cp2');
                document.getElementById('md-segmen').textContent = button.getAttribute('data-segmen');
                document.getElementById('md-registrasi').textContent = button.getAttribute('data-registrasi');

                const statusValue = button.getAttribute('data-status').toUpperCase();
                const mdStatus = document.getElementById('md-status');
                if (statusValue === 'AKTIF' || statusValue === 'A') {
                    mdStatus.textContent = 'AKTIF';
                    mdStatus.className = 'fw-bold p-2 rounded bg-success-subtle text-success d-inline-block';
                } else {
                    mdStatus.textContent = 'NON-AKTIF';
                    mdStatus.className = 'fw-bold p-2 rounded bg-secondary-subtle text-secondary d-inline-block';
                }
            });
        }
    });
</script>
@endsection