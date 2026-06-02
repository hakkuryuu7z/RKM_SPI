<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Member;

class MemberController extends Controller
{
    /**
     * Menampilkan Indeks Data Member disertai Fitur Pencarian Global
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Member::query();

        // Kondisional Pencarian Berdasarkan Kode, Nama, Wilayah, atau No Salesman
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'LIKE', "%{$search}%")
                    ->orWhere('nama', 'LIKE', "%{$search}%")
                    ->orWhere('kota', 'LIKE', "%{$search}%")
                    ->orWhere('cus_nosalesman', 'LIKE', "%{$search}%");
            });
        }

        // Menggunakan appends() agar keyword pencarian tidak hilang saat berpindah halaman pagination
        $members = $query->paginate(10)->appends(['search' => $search]);

        return view('members.index', compact('members', 'search'));
    }

    /**
     * Sinkronisasi Data Member dari API Eksternal
     */
    public function syncApi()
    {
        set_time_limit(300);

        try {
            $url_api = 'http://100.75.72.31:8080/rkm_api/api_get_member_koordinat.php';
            $response = Http::timeout(60)->get($url_api);

            if ($response->successful()) {
                $dataApi = $response->json();
                $jumlahData = count($dataApi);

                foreach ($dataApi as $data) {
                    Member::updateOrCreate(
                        ['kode' => $data['kode']],
                        [
                            'cus_kodeigr'        => $data['cus_kodeigr'],
                            'status'             => $data['status'],
                            'nama'               => $data['nama'],
                            'alamat'             => $data['alamat'],
                            'kota'               => $data['kota'],
                            'kode_pos'           => $data['kode_pos'],
                            'kelurahan'          => $data['kelurahan'],
                            'telepon'            => !empty($data['telepon']) ? substr($data['telepon'], 0, 20) : null,
                            'hp'                 => !empty($data['hp']) ? substr($data['hp'], 0, 20) : null,
                            'contact_person1'    => !empty($data['contact_person1']) ? substr($data['contact_person1'], 0, 20) : null,
                            'contact_person2'    => $data['contact_person2'],
                            'alamat_2'           => $data['alamat_2'],
                            'kota_2'             => $data['kota_2'],
                            'kode_pos_2'         => $data['kode_pos_2'],
                            'kelurahan_2'        => $data['kelurahan_2'],
                            'kecamatan'          => $data['kecamatan'],
                            'flag_member_khusus' => $data['flag_member_khusus'],
                            'kode_outlet'        => $data['kode_outlet'],
                            'nama_outlet'        => $data['nama_outlet'],
                            'nama_sub_outlet'    => $data['nama_sub_outlet'],
                            'tgl_registrasi'     => $data['tgl_registrasi'] ?: null,
                            'kunjungan_pertama'  => $data['kunjungan_pertama'] ?: null,
                            'kunjungan_terakhir' => $data['kunjungan_terakhir'] ?: null,
                            'jumlah_kunjungan'   => $data['jumlah_kunjungan'],
                            'segmen_id'          => $data['segmen_id'],
                            'nama_segmen'        => $data['nama_segmen'],
                            'koordinat'          => $data['koordinat'] ?? null,
                            'lat'                => $data['lat'] ?? null,
                            'lng'                => $data['lng'] ?? null,
                            'cus_nosalesman'     => $data['no_salesman'],
                            'jarak'              => $data['jarak'] ?? null,
                        ]
                    );
                }

                return back()->with('success', 'Sebanyak ' . $jumlahData . ' data member berhasil disinkronisasi ke sistem lokal.');
            } else {
                return back()->with('error', 'Gagal terhubung dengan server API. Kode Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Kegagalan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Export Data Member ke Format Excel (Mengikuti Filter Pencarian Aktif)
     */
    public function exportExcel(Request $request)
    {
        $search = $request->get('search');
        $query = Member::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'LIKE', "%{$search}%")
                    ->orWhere('nama', 'LIKE', "%{$search}%");
            });
        }

        $members = $query->get();
        $fileName = 'Data_Master_Member_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Kode Member', 'Nama Member', 'Nama Outlet', 'Alamat', 'Kecamatan', 'Kota', 'No Salesman', 'Status'];

        $callback = function () use ($members, $columns) {
            $file = fopen('php://output', 'w');
            // Menambahkan BOM agar karakter khusus terbaca dengan benar di Microsoft Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($members as $index => $member) {
                fputcsv($file, [
                    $index + 1,
                    $member->kode,
                    $member->nama,
                    $member->nama_outlet ?? '-',
                    $member->alamat,
                    $member->kecamatan ?? '-',
                    $member->kota,
                    $member->cus_nosalesman ?? '-',
                    $member->status
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Menampilkan Tampilan Cetak Khusus PDF (Mengikuti Filter Pencarian Aktif)
     */
    public function exportPdf(Request $request)
    {
        $search = $request->get('search');
        $query = Member::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'LIKE', "%{$search}%")
                    ->orWhere('nama', 'LIKE', "%{$search}%");
            });
        }

        $members = $query->get();
        $tanggalCetak = now()->locale('id')->isoFormat('D MMMM YYYY H:i:s');

        return view('members.pdf', compact('members', 'tanggalCetak'));
    }
}
