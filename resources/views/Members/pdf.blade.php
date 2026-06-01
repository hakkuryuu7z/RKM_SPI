<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan_Master_Member_SPI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
        }

        .table th {
            background-color: #f8f9fa !important;
            color: #000 !important;
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .table td {
            border-bottom: 0.5px solid #dee2e6;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 0;
                margin: 0;
            }

            @page {
                size: A4 landscape;
                margin: 15mm;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid my-3 no-print text-end">
        <button onclick="window.print()" class="btn btn-sm btn-dark fw-semibold px-4 shadow-sm">
            <i class="fa-solid fa-print me-2"></i> Konfirmasi Simpan PDF / Cetak
        </button>
        <button onclick="window.close()" class="btn btn-sm btn-light border ms-1 px-3">Tutup Halaman</button>
    </div>

    <div class="container-fluid mb-4 text-center">
        <h4 class="fw-bold mb-1 text-uppercase">Laporan Data Master Member RKM SPI</h4>
        <p class="text-muted small mb-0">Waktu Cetak Dokumen: {{ $tanggalCetak }}</p>
        <hr style="border-top: 2px solid #000; margin-top: 10px;">
    </div>

    <div class="container-fluid">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th style="width: 5%">No.</th>
                    <th style="width: 12%">Kode Member</th>
                    <th style="width: 23%">Nama Pelanggan / Member</th>
                    <th style="width: 20%">Nama Outlet Terdaftar</th>
                    <th style="width: 25%">Alamat Distribusi & Kota</th>
                    <th style="width: 15%">No. Salesman</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $index => $member)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $member->kode }}</td>
                    <td>{{ $member->nama }}</td>
                    <td>{{ $member->nama_outlet ?? '-' }}</td>
                    <td>{{ $member->alamat }}, {{ $member->kota }}</td>
                    <td>{{ $member->cus_nosalesman ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Tidak ada data member yang tersedia untuk dicetak.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        window.onload = function() {
            // Membuka jendela dialog printer / save as PDF secara otomatis saat halaman selesai dimuat
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>