<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan Halaman Utama Dashboard Berdasarkan Hak Akses Pengguna
     */
    public function index()
    {
        $hariIni = now()->toDateString();
        $user = Auth::user();

        // 📋 VALIDASI ROLE LEVEL: Memeriksa tingkat otoritas level 4 (Marketing Representative)
        if ($user->role && $user->role->role_level == 4) {

            // =================================================================
            // PANEL UTAMA: MARKETING REPRESENTATIVE (MR) - LEVEL 4
            // =================================================================
            $tugasHariIni = \App\Models\JalurMr::with('member')
                ->where('jlr_user_id', $user->user_id)
                ->where('jlr_tanggal_rkm', $hariIni)
                ->get();

            $kunjungan = \App\Models\TrRkm::where('rkm_user_id', $user->user_id)
                ->where('rkm_tanggal', $hariIni)
                ->get()
                ->keyBy('rkm_jlr_id');

            foreach ($tugasHariIni as $tugas) {
                $statusData = $kunjungan->get($tugas->jlr_id);

                $tugas->status_hari_ini = $statusData ? $statusData->status_kunjungan : 'BELUM';
                $tugas->jam_masuk_hari_ini = $statusData ? $statusData->waktu_checkin : null;
            }

            return view('mr.dashboard', compact('tugasHariIni', 'hariIni'));
        } else {

            // =================================================================
            // PANEL UTAMA: MANAGEMENT / MONITORING (ADMIN, SPV, MONITOR - LEVEL 1, 2, 3)
            // =================================================================

            // 1. Menarik seluruh data rute perencanaan dari semua personil MR pada hari berjalan
            $allTugas = \App\Models\JalurMr::with(['member', 'user'])
                ->where('jlr_tanggal_rkm', $hariIni)
                ->get();

            // 2. Menarik seluruh data realisasi kunjungan pada hari berjalan dari tabel tbtr_rkm
            $allKunjungan = \App\Models\TrRkm::where('rkm_tanggal', $hariIni)
                ->get()
                ->keyBy('rkm_jlr_id');

            // 3. Rekapitulasi metrik statistik pemantauan
            $metrics = [
                'total_tugas' => $allTugas->count(),
                'total_checkout' => 0,
                'total_checkin' => 0,
                'total_belum' => 0,
            ];

            // 4. Proses pemetaan status pergerakan personil lapangan
            foreach ($allTugas as $tugas) {
                $statusData = $allKunjungan->get($tugas->jlr_id);
                $status = $statusData ? $statusData->status_kunjungan : 'BELUM';

                $tugas->status_hari_ini = $status;
                $tugas->jam_masuk_hari_ini = $statusData ? $statusData->waktu_checkin : null;
                $tugas->jam_keluar_hari_ini = $statusData ? $statusData->waktu_checkout : null;

                if ($status === 'CHECKOUT') {
                    $metrics['total_checkout']++;
                } elseif ($status === 'CHECKIN') {
                    $metrics['total_checkin']++;
                } else {
                    $metrics['total_belum']++;
                }
            }

            // Mengarahkan pengguna manajemen ke halaman dashboard monitoring global
            return view('admin.dashboard', compact('allTugas', 'hariIni', 'metrics'));
        }
    }

    /**
     * Tampilan Detail Formulir Kunjungan Toko (Khusus Akses MR)
     */
    public function detail($id)
    {
        $tugas = \App\Models\JalurMr::with('member')->findOrFail($id);
        $setting = DB::table('tbmaster_setting')->first();

        $kunjungan = \App\Models\TrRkm::where('rkm_jlr_id', $tugas->jlr_id)
            ->where('rkm_tanggal', now()->toDateString())
            ->first();

        return view('mr.detail', compact('tugas', 'setting', 'kunjungan'));
    }

    /**
     * Menyimpan Data Realisasi Check-In Lokasi Toko
     */
    public function checkInStore(Request $request, $id)
    {
        $request->validate([
            'lat_mr' => 'required',
            'lng_mr' => 'required',
        ]);

        $tugas = \App\Models\JalurMr::with('member')->findOrFail($id);

        if ($tugas->jlr_user_id != Auth::user()->user_id) {
            abort(403, 'Akses tidak sah.');
        }

        \App\Models\TrRkm::updateOrCreate(
            [
                'rkm_jlr_id'   => $tugas->jlr_id,
                'rkm_tanggal'  => now()->toDateString(),
            ],
            [
                'rkm_user_id'       => Auth::user()->user_id,
                'rkm_nama_member'   => $tugas->member ? $tugas->member->nama : 'Toko Tanpa Nama',
                'rkm_kodemember'    => $tugas->jlr_kodemember,
                'waktu_checkin'     => now(),
                'lat_kunjungan'     => $request->lat_mr,
                'lng_kunjungan'     => $request->lng_mr,
                'status_kunjungan'  => 'CHECKIN',
            ]
        );

        return redirect()->back()->with('success', 'Proses Check-In berhasil tercatat.');
    }

    /**
     * Menyimpan Data Laporan Akhir Kunjungan (Check-Out) disertai Kompresi Citra
     */
    public function checkOutStore(Request $request, $id)
    {
        $request->validate([
            'rkm_order_status'     => 'required',
            'rkm_keteranganmember' => 'required',
            // 💡 Validasi disesuaikan untuk menerima tipe data struktur array
            'foto_kunjungan'       => 'required|array',
            'foto_kunjungan.*'     => 'image|mimes:jpg,jpeg,png|max:10240'
        ]);

        $kunjungan = \App\Models\TrRkm::findOrFail($id);
        $setting = DB::table('tbmaster_setting')->first();

        $waktuCheckin = Carbon::parse($kunjungan->waktu_checkin);
        $durasiMenit = $waktuCheckin->diffInMinutes(now());

        if ($request->rkm_order_status != 'Tutup' && $durasiMenit < ($setting?->minimal_menit ?? 15)) {
            $sisaMenit = ($setting?->minimal_menit ?? 15) - $durasiMenit;
            return redirect()->back()->with('error', "Batas minimal durasi kunjungan belum terpenuhi. Silakan tunggu sekitar {$sisaMenit} menit lagi.");
        }

        // Logika pemrosesan perulangan file foto massal
        $arrayNamaFoto = [];
        if ($request->hasFile('foto_kunjungan')) {
            $destinationPath = public_path('uploads/kunjungan');

            foreach ($request->file('foto_kunjungan') as $index => $file) {
                $namaFoto = 'RKM_' . time() . '_' . $index . '.jpg';
                $this->compressAndSaveImage($file, $destinationPath, $namaFoto, 40);
                $arrayNamaFoto[] = $namaFoto;
            }
        }

        $kunjungan->update([
            'rkm_order_status'     => $request->rkm_order_status == 'Tutup' ? 'Tidak' : $request->rkm_order_status,
            'rkm_keteranganmember' => $request->rkm_keteranganmember,
            'rkm_trx'              => $request->rkm_trx,
            'waktu_checkout'       => now(),
            // 💡 Menyimpan nama-nama file sebagai teks JSON murni atau teks pisahan koma ke database
            'foto_kunjungan'       => json_encode($arrayNamaFoto),
            'status_kunjungan'     => 'CHECKOUT'
        ]);

        return redirect()->route('dashboard')->with('success', 'Laporan kunjungan lapangan berhasil diunggah.');
    }
    /**
     * Menampilkan Halaman Parameter Pengaturan Validasi (Akses Manajemen)
     */
    public function settingForm()
    {
        $user = Auth::user();

        // 📋 VALIDASI ROLE LEVEL: Proteksi akses halaman berdasarkan tingkatan level 4
        if ($user->role && $user->role->role_level == 4) {
            abort(403, 'Akses ditolak. Halaman ini memerlukan hak akses Administrator.');
        }

        $setting = DB::table('tbmaster_setting')->first();
        return view('admin.setting', compact('setting'));
    }

    /**
     * Memperbarui Data Parameter Validasi Radius & Durasi Waktu
     */
    public function settingUpdate(Request $request)
    {
        $request->validate([
            'radius_meter' => 'required|numeric|min:1',
            'minimal_menit' => 'required|numeric|min:1',
        ]);

        DB::table('tbmaster_setting')->where('id', 1)->update([
            'radius_meter' => $request->radius_meter,
            'minimal_menit' => $request->minimal_menit,
        ]);

        return redirect()->back()->with('success', 'Konfigurasi validasi berhasil diperbarui.');
    }

    /**
     * Fungsi Pendukung: Reduksi Ukuran Berkas Gambar Berbasis Ekstensi GD Native
     */
    private function compressAndSaveImage($file, $destinationPath, $namaFoto, $quality = 40)
    {
        $sourcePath = $file->getRealPath();
        $targetFile = $destinationPath . '/' . $namaFoto;

        if (!function_exists('imagecreatefromjpeg')) {
            $file->move($destinationPath, $namaFoto);
            return;
        }

        $info = getimagesize($sourcePath);

        if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/jpg') {
            $image = imagecreatefromjpeg($sourcePath);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($sourcePath);

            $background = imagecreatetruecolor(imagesx($image), imagesy($image));
            $white = imagecolorallocate($background, 255, 255, 255);
            imagefill($background, 0, 0, $white);
            imagecopy($background, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
            $image = $background;
        } else {
            $file->move($destinationPath, $namaFoto);
            return;
        }

        imagejpeg($image, $targetFile, $quality);
        imagedestroy($image);
    }
}
