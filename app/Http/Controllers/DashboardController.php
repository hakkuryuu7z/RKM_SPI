<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JalurMr;
use Carbon\Carbon;

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
}
