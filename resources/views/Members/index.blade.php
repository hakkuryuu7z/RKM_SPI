@extends('layouts.app')
<style>
    /* Spasi atas bawah untuk Search dan Pagination */
    div.dataTables_wrapper div.dataTables_filter,
    div.dataTables_wrapper div.dataTables_length {
        margin-bottom: 1.5rem;
    }

    div.dataTables_wrapper div.dataTables_info,
    div.dataTables_wrapper div.dataTables_paginate {
        margin-top: 1.5rem;
    }

    /* Percantik Input Search & Select Length */
    .dataTables_wrapper .form-control,
    .dataTables_wrapper .form-select {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 0.4rem 1rem;
        box-shadow: none;
    }

    .dataTables_wrapper .form-control:focus,
    .dataTables_wrapper .form-select:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 0.25rem rgba(14, 165, 233, 0.15);
    }

    /* Percantik Tombol Pagination (Warna Ocean) */
    .page-item.active .page-link {
        background-color: #0c4a6e !important;
        border-color: #0c4a6e !important;
        color: white !important;
        border-radius: 6px;
    }

    .page-link {
        color: #0c4a6e;
        border-radius: 6px;
        margin: 0 3px;
        border: 1px solid #f1f5f9;
    }

    .page-link:hover {
        background-color: #f0f9ff;
        color: #0ea5e9;
    }
</style>
@section('title', 'Data Member Relasi')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color: #0c4a6e;">Data Member</h4>
            <p class="text-muted small mb-0">Sinkronisasi data otomatis dari server pusat.</p>
        </div>

        <a href="{{ route('member.sync') }}" id="btnSync" data-turbo="false" wire:navigate.hover="false" wire:navigate="false" class="btn btn-primary d-flex align-items-center gap-2 px-4 shadow-sm" style="background-color: #0c4a6e; border: none; border-radius: 8px;">
            <span>🔄</span> Tarik Data API
        </a>
    </div>

    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="tableMember" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th width="10%">KODE</th>
                            <th width="30%">NAMA TOKO / MEMBER</th>
                            <th width="20%">KOTA</th>
                            <th width="20%">KONTAK (HP)</th>
                            <th width="10%">STATUS</th>
                            <th width="10%" class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $m)
                        <tr>
                            <td class="text-muted fw-bold">{{ $m->kode }}</td>
                            <td class="fw-semibold text-dark">{{ $m->nama }}</td>
                            <td class="text-muted">{{ $m->kota }}</td>
                            <td class="text-muted">{{ $m->hp }}</td>
                            <td>
                                @if(strtoupper($m->status) == 'AKTIF')
                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1">AKTIF</span>
                                @else
                                <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">{{ $m->status }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-light text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#detailModal{{ $m->kode }}">
                                    Detail
                                </button>

                                <div class="modal fade" id="detailModal{{ $m->kode }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow-lg rounded-4 text-start">
                                            <div class="modal-header border-0 pt-4 px-4 pb-0">
                                                <h5 class="fw-bold" style="color: #0c4a6e;">Detail Member</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <p class="text-muted small mb-1 text-uppercase fw-semibold">Nama Toko / Member</p>
                                                        <h6 class="fw-bold text-dark">{{ $m->nama }}</h6>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="text-muted small mb-1 text-uppercase fw-semibold">Kode Member</p>
                                                        <h6 class="fw-bold text-dark">{{ $m->kode }} <span class="badge bg-light text-dark ms-2">{{ $m->status }}</span></h6>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <p class="text-muted small mb-1 text-uppercase fw-semibold">No. KTP</p>
                                                        <h6 class="fw-bold text-dark">{{ $m->no_ktp ?? '-' }}</h6>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="text-muted small mb-1 text-uppercase fw-semibold">Kontak (HP)</p>
                                                        <h6 class="fw-bold text-dark">{{ $m->hp ?? '-' }}</h6>
                                                    </div>

                                                    <div class="col-12">
                                                        <hr class="text-muted opacity-25 my-2">
                                                    </div>

                                                    <div class="col-md-12">
                                                        <p class="text-muted small mb-1 text-uppercase fw-semibold">Alamat Lengkap</p>
                                                        <h6 class="fw-bold text-dark lh-base">
                                                            {{ $m->alamat }}, Kel. {{ $m->kelurahan }}, Kec. {{ $m->kecamatan }}<br>
                                                            {{ $m->kota }} - {{ $m->kode_pos }}
                                                        </h6>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <p class="text-muted small mb-1 text-uppercase fw-semibold">Tipe Outlet</p>
                                                        <h6 class="fw-bold text-dark">{{ $m->nama_outlet ?? '-' }} ({{ $m->nama_sub_outlet ?? '-' }})</h6>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="text-muted small mb-1 text-uppercase fw-semibold">No. Salesman</p>
                                                        <h6 class="fw-bold text-dark">
                                                            <span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1">
                                                                {{ $m->cus_nosalesman ?? 'TIDAK ADA' }}
                                                            </span>
                                                        </h6>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="text-muted small mb-1 text-uppercase fw-semibold">Radius Jarak</p>
                                                        <h6 class="fw-bold text-dark">
                                                            {{ $m->jarak ? $m->jarak . ' KM' : '-' }}
                                                        </h6>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <p class="text-muted small mb-1 text-uppercase fw-semibold">Titik Koordinat</p>
                                                        <h6 class="fw-bold text-dark">
                                                            <a href="https://www.google.com/maps/search/?api=1&query={{ $m->lat }},{{ $m->lng }}" target="_blank" class="text-decoration-none" style="color: #0ea5e9;">
                                                                📍 {{ $m->lat }}, {{ $m->lng }}
                                                            </a>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pb-4 px-4 pt-0">
                                                <button type="button" class="btn btn-light rounded-3 px-4 fw-medium" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    // 1. Gabungin semua ke satu fungsi sakti
    function jalankanSemuaFitur() {

        // --- A. URUSAN DATATABLES ---
        // Kalau tabel udah pernah jadi DataTables sebelumnya, hancurin dulu biar gak error, baru bikin ulang
        if ($.fn.DataTable.isDataTable('#tableMember')) {
            $('#tableMember').DataTable().destroy();
        }

        $('#tableMember').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            pageLength: 10,
            ordering: true,
            responsive: true
        });

        // --- B. URUSAN SWEETALERT ---
        // Ganti bagian SweetAlert lu jadi begini:
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Mantap!',
            text: @json(session('success')),
            timer: 3000,
            showConfirmButton: false
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: @json(session('error'))
        });
        @endif
    }

    // 2. Panggil fungsi sakti tadi di SEMUA KONDISI BROWSER

    // Kondisi 1: Pas pertama kali buka web / Refresh F5
    $(document).ready(function() {
        jalankanSemuaFitur();
    });

    // Kondisi 2: Pas pindah menu pakai Livewire / Turbolinks / Turbo
    document.addEventListener('livewire:navigated', jalankanSemuaFitur);
    document.addEventListener('turbo:load', jalankanSemuaFitur);
    document.addEventListener('turbolinks:load', jalankanSemuaFitur);

    // 3. Efek Loading Tombol Sync tetep biarin di sini
    const btnSync = document.getElementById('btnSync');
    if (btnSync) {
        btnSync.addEventListener('click', function() {
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menarik Data...';
            this.classList.add('disabled');
        });
    }
</script>
@endpush