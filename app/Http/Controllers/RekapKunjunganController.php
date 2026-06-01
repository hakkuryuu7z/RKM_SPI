<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrRkm;
use App\Models\JalurMr;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RekapKunjunganController extends Controller
{
    /**
     * Menampilkan Halaman Indeks Rekap Kunjungan Terbagi Atas 2 Tab (Akurasi & Log)
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $userId = $request->get('user_id');

        // =================================================================
        // DATA TAB 1: RANGKUMAN AKURASI PER MR (GROUPING AGGREGATION)
        // =================================================================
        $mrQuery = User::whereHas('role', function ($q) {
            $q->where('role_level', 4);
        });

        if (!empty($userId)) {
            $mrQuery->where('user_id', $userId);
        }
        $listMr = $mrQuery->get();

        // Mengambil akumulasi data target dari tabel master jalur
        $targetData = JalurMr::whereBetween('jlr_tanggal_rkm', [$startDate, $endDate])
            ->select('jlr_user_id', DB::raw('count(*) as total_target'))
            ->groupBy('jlr_user_id')
            ->get()
            ->keyBy('jlr_user_id');

        // Mengambil akumulasi data realisasi dari tabel transaksi rkm
        $realisasiData = TrRkm::whereBetween('rkm_tanggal', [$startDate, $endDate])
            ->select(
                'rkm_user_id',
                DB::raw("SUM(CASE WHEN UPPER(status_kunjungan) = 'CHECKOUT' THEN 1 ELSE 0 END) as total_checkout"),
                DB::raw("SUM(CASE WHEN UPPER(status_kunjungan) = 'CHECKIN' THEN 1 ELSE 0 END) as total_checkin")
            )
            ->groupBy('rkm_user_id')
            ->get()
            ->keyBy('rkm_user_id');

        $reportAkurasi = [];
        foreach ($listMr as $mr) {
            $target = $targetData->get($mr->user_id)?->total_target ?? 0;
            $checkout = $realisasiData->get($mr->user_id)?->total_checkout ?? 0;
            $checkin = $realisasiData->get($mr->user_id)?->total_checkin ?? 0;
            $missed = max(0, $target - ($checkout + $checkin));

            $persentase = $target > 0 ? round(($checkout / $target) * 100, 1) : 0;

            $reportAkurasi[] = [
                'user_id'    => $mr->user_id,
                'username'   => $mr->user_username,
                'target'     => $target,
                'checkout'   => $checkout,
                'checkin'    => $checkin,
                'missed'     => $missed,
                'persentase' => $persentase
            ];
        }

        // =================================================================
        // DATA TAB 2: LOG AKTIVITAS KUNJUNGAN (RAW LOG PAGINATION)
        // =================================================================
        $logQuery = TrRkm::with(['user'])->whereBetween('rkm_tanggal', [$startDate, $endDate]);
        if (!empty($userId)) {
            $logQuery->where('rkm_user_id', $userId);
        }
        $allRekap = $logQuery->orderBy('rkm_tanggal', 'desc')
            ->orderBy('waktu_checkin', 'desc')
            ->paginate(10)
            ->appends(['start_date' => $startDate, 'end_date' => $endDate, 'user_id' => $userId]);

        // Daftar master MR utuh untuk opsi dropdown form filter
        $masterMr = User::whereHas('role', function ($q) {
            $q->where('role_level', 4);
        })->get();

        return view('admin.rekap', compact('allRekap', 'masterMr', 'reportAkurasi', 'startDate', 'endDate', 'userId'));
    }

    /**
     * Mengambil Informasi Rinci Transaksi Baris tbtr_rkm (AJAX Respon)
     */
    public function show($id)
    {
        $detail = TrRkm::with(['user'])->where('rkm_id', $id)->first();

        if (!$detail) {
            return response()->json(['message' => 'Arsip dokumentasi realisasi kunjungan tidak ditemukan.'], 404);
        }

        return response()->json([
            'tanggal'        => Carbon::parse($detail->rkm_tanggal)->locale('id')->isoFormat('DD MMMM YYYY'),
            'mr'             => $detail->user->user_username ?? 'User ID: ' . $detail->rkm_user_id,
            'kode'           => $detail->rkm_kodemember,
            'nama_toko'      => $detail->rkm_nama_member ?? 'Tanpa Nama Outlet',
            'checkin'        => $detail->waktu_checkin ? Carbon::parse($detail->waktu_checkin)->format('H:i:s') : '-',
            'checkout'       => $detail->waktu_checkout ? Carbon::parse($detail->waktu_checkout)->format('H:i:s') : '-',
            'status'         => strtoupper($detail->status_kunjungan) ?? 'BELUM KUNJUNGAN',
            'order_status'   => $detail->rkm_order_status ?? 'Tidak',
            'foto'           => $detail->foto_kunjungan ? asset('uploads/kunjungan/' . $detail->foto_kunjungan) : null,
            'catatan'        => $detail->rkm_keteranganmember ?? 'Tidak ada catatan khusus dari lapangan.'
        ]);
    }

    /**
     * Export Rangkuman Akurasi Performa ke Format Excel / CSV Stream
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Logika rekonstruksi hitung ulang data penarikan rangkuman akurasi formal
        $listMr = User::whereHas('role', function ($q) {
            $q->where('role_level', 4);
        })->get();
        $targetData = JalurMr::whereBetween('jlr_tanggal_rkm', [$startDate, $endDate])->select('jlr_user_id', DB::raw('count(*) as total_target'))->groupBy('jlr_user_id')->get()->keyBy('jlr_user_id');
        $realisasiData = TrRkm::whereBetween('rkm_tanggal', [$startDate, $endDate])->select('rkm_user_id', DB::raw("SUM(CASE WHEN UPPER(status_kunjungan) = 'CHECKOUT' THEN 1 ELSE 0 END) as total_checkout"))->groupBy('rkm_user_id')->get()->keyBy('rkm_user_id');

        $fileName = 'Laporan_Akurasi_KPI_MR_' . date('Ymd_His') . '.csv';
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Nama Personil (MR)', 'Total Target Rute', 'Realisasi Kunjungan (Selesai)', 'Persentase Akurasi KPI'];

        $callback = function () use ($listMr, $targetData, $realisasiData, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($listMr as $index => $mr) {
                $target = $targetData->get($mr->user_id)?->total_target ?? 0;
                $checkout = $realisasiData->get($mr->user_id)?->total_checkout ?? 0;
                $persen = $target > 0 ? round(($checkout / $target) * 100, 1) : 0;

                fputcsv($file, [$index + 1, $mr->user_username, $target, $checkout, $persen . '%'], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Rangkuman Akurasi Performa ke Tampilan Cetak Tab Baru PDF
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $listMr = User::whereHas('role', function ($q) {
            $q->where('role_level', 4);
        })->get();
        $targetData = JalurMr::whereBetween('jlr_tanggal_rkm', [$startDate, $endDate])->select('jlr_user_id', DB::raw('count(*) as total_target'))->groupBy('jlr_user_id')->get()->keyBy('jlr_user_id');
        $realisasiData = TrRkm::whereBetween('rkm_tanggal', [$startDate, $endDate])->select('rkm_user_id', DB::raw("SUM(CASE WHEN UPPER(status_kunjungan) = 'CHECKOUT' THEN 1 ELSE 0 END) as total_checkout"), DB::raw("SUM(CASE WHEN UPPER(status_kunjungan) = 'CHECKIN' THEN 1 ELSE 0 END) as total_checkin"))->groupBy('rkm_user_id')->get()->keyBy('rkm_user_id');

        $reportData = [];
        foreach ($listMr as $index => $mr) {
            $target = $targetData->get($mr->user_id)?->total_target ?? 0;
            $checkout = $realisasiData->get($mr->user_id)?->total_checkout ?? 0;
            $checkin = $realisasiData->get($mr->user_id)?->total_checkin ?? 0;
            $missed = max(0, $target - ($checkout + $checkin));
            $persen = $target > 0 ? round(($checkout / $target) * 100, 1) : 0;

            $reportData[] = [
                'no' => $index + 1,
                'name' => $mr->user_username,
                'target' => $target,
                'checkout' => $checkout,
                'checkin' => $checkin,
                'missed' => $missed,
                'persen' => $persen . '%'
            ];
        }

        $tanggalCetak = now()->locale('id')->isoFormat('D MMMM YYYY H:i:s');
        $periode = Carbon::parse($startDate)->format('d/m/Y') . ' s.d ' . Carbon::parse($endDate)->format('d/m/Y');

        return view('admin.rekap_pdf', compact('reportData', 'tanggalCetak', 'periode'));
    }
}
