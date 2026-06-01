<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan_KPI_Akurasi_Performa_MR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .table th {
            background-color: #f8f9fa !important;
            color: #000 !important;
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            text-transform: uppercase;
            font-size: 11px;
        }

        .table td {
            border-bottom: 0.5px solid #dee2e6;
            padding: 8px;
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
                size: A4 portrait;
                margin: 20mm;
            }
        }
    </style>
</head>

<body>

    <div class="container my-3 no-print text-end">
        <button onclick="window.print()" class="btn btn-sm btn-dark fw-semibold px-4 shadow-sm">Konfirmasi Cetak / Simpan PDF</button>
        <button onclick="window.close()" class="btn btn-sm btn-light border ms-1 px-3">Tutup</button>
    </div>

    <div class="container text-center mb-4 mt-4">
        <h4 class="fw-bold mb-1">LAPORAN AKURASI PERFORMA KUNJUNGAN MR</h4>
        <p class="text-muted small mb-0">Periode Evaluasi: {{ $periode }} | Waktu Cetak: {{ $tanggalCetak }}</p>
        <hr style="border-top: 2px solid #000; margin-top: 15px;">
    </div>

    <div class="container">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th style="width: 8%">No.</th>
                    <th style="width: 32%">Nama Personil (MR)</th>
                    <th style="width: 15%" class="text-center">Total Target</th>
                    <th style="width: 15%" class="text-center">Selesai (Trx)</th>
                    <th style="width: 15%" class="text-center">Tertunda/Miss</th>
                    <th style="width: 15%" class="text-center">Rasio Akurasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $row)
                <tr>
                    <td>{{ $row['no'] }}</td>
                    <td class="fw-bold text-dark">{{ strtoupper($row['name']) }}</td>
                    <td class="text-center">{{ $row['target'] }} Toko</td>
                    <td class="text-center">{{ $row['checkout'] }} Toko</td>
                    <td class="text-center">{{ $row['checkin'] + $row['missed'] }} Toko</td>
                    <td class="text-center fw-bold text-primary">{{ $row['persen'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>