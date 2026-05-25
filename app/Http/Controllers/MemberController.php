<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Member;

class MemberController extends Controller
{
    public function index()
    {
        // Ambil semua data member dari database
        $members = Member::all();

        // Tampilkan ke view members/index.blade.php
        return view('members.index', compact('members'));
    }
    public function syncApi()
    {
        set_time_limit(300); // Waktu eksekusi dilamain

        try {
            $url_api = 'http://100.85.26.66/rkm_api/api_get_member_koordinat.php';
            $response = Http::timeout(60)->get($url_api);

            if ($response->successful()) {
                $dataApi = $response->json();
                $jumlahData = count($dataApi);

                foreach ($dataApi as $data) {
                    Member::updateOrCreate(
                        ['kode' => $data['kode']], // Patokan update/simpan

                        [
                            'cus_kodeigr'        => $data['cus_kodeigr'],
                            'status'             => $data['status'],
                            'no_ktp'             => $data['no_ktp'],
                            'nama'               => $data['nama'],
                            'jenis_kelamin'      => $data['jenis_kelamin'],
                            'alamat'             => $data['alamat'],
                            'kota'               => $data['kota'],
                            'kode_pos'           => $data['kode_pos'],
                            'kelurahan'          => $data['kelurahan'],
                            'telepon' => !empty($data['telepon']) ? substr($data['telepon'], 0, 20) : null,
                            'hp'      => !empty($data['hp']) ? substr($data['hp'], 0, 20) : null,
                            'contact_person1' => !empty($data['contact_person1']) ? substr($data['contact_person1'], 0, 20) : null,
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
                            'tgl_lahir'          => $data['tgl_lahir'] ?: null,
                            'koordinat'          => $data['koordinat'] ?? null,
                            'lat'                => $data['lat'] ?? null,
                            'lng'                => $data['lng'] ?? null,
                            'cus_nosalesman'     => $data['no_salesman'],
                            'jarak'          => $data['jarak'] ?? null,
                        ]
                    );
                }

                return back()->with('success', $jumlahData . ' Data Member berhasil disinkronisasi!');
            } else {
                return back()->with('error', 'Gagal API. Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error sistem: ' . $e->getMessage());
        }
    }
}
