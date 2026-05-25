<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JalurMr;
use App\Models\Member;
use App\Models\User;
use App\Models\Role; // Opsional kalau lu pakai role id

class JalurController extends Controller
{
    public function index()
    {
        $mrUsers = User::whereHas('role', function ($query) {
            $query->where('role_level', 4);
        })->get();

        $members = Member::whereNotNull('lat')->whereNotNull('lng')->get();

        $salesmanList = Member::select('cus_nosalesman')
            ->distinct()
            ->pluck('cus_nosalesman')
            ->map(fn($item) => $item ?? 'TIDAK ADA')
            ->unique()->values();

        // TAMBAHAN BARU: Ambil daftar Kecamatan
        $kecamatanList = Member::select('kecamatan')
            ->whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->distinct()
            ->pluck('kecamatan');
        // 1. Ambil semua data jalur untuk dilempar ke Javascript (Buat fitur Edit)
        $allJalur = JalurMr::all();

        // 2. Bikin rekap untuk tabel (Dikelompokkan per MR & Tanggal)
        $rekapJalur = JalurMr::with('user')->get()->groupBy(function ($item) {
            return $item->jlr_user_id . '_' . $item->jlr_tanggal_rkm;
        });

        return view('jalur.index', compact('mrUsers', 'members', 'salesmanList', 'kecamatanList', 'allJalur', 'rekapJalur'));
    }
    public function store(Request $request)
    {
        // 1. Validasi biar pasti datanya lengkap sebelum disimpen
        $request->validate([
            'jlr_user_id'     => 'required',
            'jlr_tanggal_rkm' => 'required|date',
            'jlr_kodemember'  => 'required|array|min:1', // Pastikan minimal ada 1 toko yang dipilih
        ], [
            'jlr_kodemember.required' => 'Pilih minimal 1 member untuk rute kunjungan!'
        ]);
        // Jadi nggak akan ada data double kalau lagi ngedit!
        JalurMr::where('jlr_user_id', $request->jlr_user_id)
            ->where('jlr_tanggal_rkm', $request->jlr_tanggal_rkm)
            ->delete();

        // Insert rute baru / hasil editan
        foreach ($request->jlr_kodemember as $kode) {
            JalurMr::create([
                'jlr_user_id'     => $request->jlr_user_id,
                'jlr_tanggal_rkm' => $request->jlr_tanggal_rkm,
                'jlr_kodemember'  => $kode,
                'jlr_create_dt'   => now(),
                'jlr_modify_dt'   => now(),
            ]);
        }

        return redirect()->route('jalur.index')->with('success', 'Rute Kunjungan berhasil disimpan/diupdate!');
    }

    // --- FUNGSI DELETE BARU ---
    public function destroy(Request $request)
    {
        JalurMr::where('jlr_user_id', $request->jlr_user_id)
            ->where('jlr_tanggal_rkm', $request->jlr_tanggal_rkm)
            ->delete();

        return redirect()->back()->with('success', 'Rute Kunjungan berhasil dihapus!');
    }
    public function downloadTemplate()
    {
        $fileName = 'Template_Upload_RKM.csv';

        // Header kolom diubah jadi username_mr
        $columns = ['kode_member', 'tanggal_rkm', 'username_mr'];

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Contoh datanya sekarang pakai Username, bukan angka lagi
            fputcsv($file, ['KL03F1', '2026-05-20', 'BAGAS']);
            fputcsv($file, ['KL03G2', '2026-05-20', 'BAGAS']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function import(Request $request)
    {
        // 1. Validasi pastiin yang diupload file CSV
        $request->validate([
            'file_excel' => 'required|file|mimes:csv,txt',
        ], [
            'file_excel.mimes' => 'Format file harus CSV! Silakan gunakan template yang didownload.'
        ]);

        $file = $request->file('file_excel');

        // 2. Buka file CSV-nya
        $handle = fopen($file->getPathname(), "r");

        // 3. Skip baris pertama (karena itu Header: kode_member, tanggal_rkm, username_mr)
        $header = fgetcsv($handle, 1000, ",");

        $sukses = 0;

        // 4. Looping baca data per baris
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

            // --- JURUS ANTI ERROR EXCEL ---
            // Kalau Excel nyatuin semua data di kolom pertama karena beda format regional
            if (count($data) == 1) {
                // Cek apakah sebenarnya dipisah pakai titik koma atau koma, lalu pecah paksa
                if (strpos($data[0], ';') !== false) {
                    $data = explode(';', $data[0]);
                } elseif (strpos($data[0], ',') !== false) {
                    $data = explode(',', $data[0]);
                }
            }

            // Kalau barisnya beneran kosong atau nggak lengkap (di bawah 3 kolom), baru lewati
            if (count($data) < 3) continue;

            $kodeMember = trim($data[0]);
            $tanggalRkm = date('Y-m-d', strtotime(trim($data[1])));
            $usernameMr = trim($data[2]);

            // Cari ID di tabel user berdasarkan username
            $user = User::where('user_username', $usernameMr)->first();

            // Kalau usernamenya ketemu, proses simpan...
            if ($user) {
                $exists = JalurMr::where('jlr_user_id', $user->user_id)
                    ->where('jlr_tanggal_rkm', $tanggalRkm)
                    ->where('jlr_kodemember', $kodeMember)
                    ->exists();

                if (!$exists) {
                    JalurMr::create([
                        'jlr_user_id'     => $user->user_id,
                        'jlr_tanggal_rkm' => $tanggalRkm,
                        'jlr_kodemember'  => $kodeMember,
                        'jlr_create_dt'   => now(),
                        'jlr_modify_dt'   => now(),
                    ]);
                    $sukses++;
                }
            }
        }

        // Tutup file
        fclose($handle);

        return redirect()->back()->with('success', "Import berhasil! $sukses toko sukses ditambahkan ke rute.");
    }
}
