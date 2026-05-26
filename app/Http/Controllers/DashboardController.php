<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JalurMr;
use Carbon\Carbon;
use App\Models\TrRkm;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Panggil paksa relasi role 
        $user->load('role');

        // Cek jika levelnya 4 (MR)
        if ($user->role && $user->role->role_level == 4) {

            $hariIni = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d');

            $tugasHariIni = \App\Models\JalurMr::with('member')
                ->where('jlr_user_id', $user->user_id)
                ->where('jlr_tanggal_rkm', $hariIni)
                ->get();

            return view('mr.dashboard', compact('tugasHariIni', 'hariIni'));
        }

        // Kalau Admin / SPV, masuk ke sini
        return view('dashboard');
    }
    public function detailToko($id)
    {
        // 1. Ambil data master jalur tugas si MR beserta relasi member/tokonya
        $tugas = \App\Models\JalurMr::with('member')->findOrFail($id);

        // 2. Ambil parameter radius & menit dari settingan admin
        $setting = \App\Models\Setting::first();

        // 3. 🔎 CARI DATA KUNJUNGAN HARI INI (INI DIA YANG KETINGGALAN BOLO!)
        $kunjungan = \App\Models\TrRkm::where('rkm_jlr_id', $tugas->getKey())
            ->where('rkm_tanggal', now()->toDateString())
            ->first();

        // 4. Kirim semua variabel ke view lewat compact()
        // Pastiin ada tulisan 'kunjungan' di dalem compact ya!
        return view('mr.detail', compact('tugas', 'setting', 'kunjungan'));
    }
    // Fungsi 1: Tampilkan Form Pengaturan
    public function settingForm()
    {
        // Proteksi: Kalau level 4 (MR) gak boleh masuk, tendang!
        if (Auth::user()->role && Auth::user()->role->role_level == 4) {
            abort(403, 'Halaman ini khusus Admin bolo!');
        }

        // Ambil baris pertama dari tabel setting
        $setting = Setting::first();

        return view('admin.setting', compact('setting'));
    }

    // Fungsi 2: Proses Simpan Hasil Update Form
    public function settingUpdate(Request $request)
    {
        $request->validate([
            'radius_meter' => 'required|numeric|min:1',
            'minimal_menit' => 'required|numeric|min:1',
        ]);

        $setting = Setting::first();
        $setting->update([
            'radius_meter' => $request->radius_meter,
            'minimal_menit' => $request->minimal_menit,
        ]);

        return redirect()->back()->with('success', 'Pengaturan Validasi MR Berhasil Diperbarui!');
    }
    public function checkInStore(Request $request, $id)
    {
        $request->validate([
            'lat_mr' => 'required',
            'lng_mr' => 'required',
        ]);

        // 1. Ambil data master jalur dan relasi member-nya
        $tugas = \App\Models\JalurMr::with('member')->findOrFail($id);

        if ($tugas->jlr_user_id != Auth::user()->user_id) {
            abort(403, 'Akses ilegal bolo!');
        }

        // 2. Eksekusi simpan/update ke tabel tbtr_rkm
        \App\Models\TrRkm::updateOrCreate(
            [
                'rkm_jlr_id'   => $tugas->getKey(),
                'rkm_tanggal'  => now()->toDateString(), // Mengunci tanggal hari ini
            ],
            [
                'rkm_user_id'       => Auth::user()->user_id,
                'rkm_nama_member'   => $tugas->member ? $tugas->member->nama : 'Toko Tanpa Nama',
                'rkm_kodemember'    => $tugas->jlr_kodemember,
                'waktu_checkin'     => now(), // Jam check-in saat ini
                'lat_kunjungan'     => $request->lat_mr,
                'lng_kunjungan'     => $request->lng_mr,
                'status_kunjungan'  => 'CHECKIN', // Mengubah status bawaan 'BELUM' jadi 'CHECKIN'
            ]
        );

        return redirect()->back()->with('success', 'Berhasil Check-In! Selamat bekerja bolo! 🔥');
    }
    public function checkOutStore(Request $request, $id)
    {
        //dd($request->all(), $id);
        // 1. Validasi murni tipe file gambar biasa
        $request->validate([
            'rkm_order_status'     => 'required',
            'rkm_keteranganmember' => 'required',
            'foto_kunjungan'       => 'required|image|mimes:jpg,jpeg,png|max:10240'
        ]);

        $kunjungan = \App\Models\TrRkm::findOrFail($id);
        $setting = \App\Models\Setting::first();

        // ⏱️ Hitung durasi diam (dwell time)
        $waktuCheckin = \Carbon\Carbon::parse($kunjungan->waktu_checkin);
        $durasiMenit = $waktuCheckin->diffInMinutes(now());

        if ($request->rkm_order_status != 'Tutup' && $durasiMenit < ($setting->minimal_menit ?? 15)) {
            $sisaMenit = ($setting->minimal_menit ?? 15) - $durasiMenit;
            return redirect()->back()->with('error', "Gagal Check-Out! Harus nunggu {$sisaMenit} menit lagi bolo.");
        }

        // 📸 PROSES UPLOAD FILE (BAIK DARI KAMERA ATAUPUN GALERI)
        $namaFoto = null;
        if ($request->hasFile('foto_kunjungan')) {
            $file = $request->file('foto_kunjungan');
            $namaFoto = 'RKM_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kunjungan'), $namaFoto); // Pindah ke folder laptop
        }

        // 📝 Update laporan akhir ke database
        $kunjungan->update([
            'rkm_order_status'     => $request->rkm_order_status == 'Tutup' ? 'Tidak' : $request->rkm_order_status,
            'rkm_keteranganmember' => $request->rkm_keteranganmember,
            'rkm_trx'              => $request->rkm_trx,
            'waktu_checkout'       => now(),
            'foto_kunjungan'       => $namaFoto,
            'status_kunjungan'     => 'CHECKOUT'
        ]);

        return redirect()->route('dashboard')->with('success', 'Kunjungan toko berhasil dilaporkan bolo! Mantap! 🚀');
    }
}
