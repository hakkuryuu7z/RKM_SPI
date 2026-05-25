<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'tbmaster_member';

    // Set Primary Key ke kolom 'kode'
    protected $primaryKey = 'kode';

    // Kasih tau Laravel kalau primary key bukan angka berurut
    public $incrementing = false;
    protected $keyType = 'string';

    // Aktifkan timestamp karena lu punya created_at & updated_at
    public $timestamps = true;

    // Masukin semua nama kolom biar bisa diisi massal
    protected $fillable = [
        'kode',
        'cus_kodeigr',
        'status',
        'no_ktp',
        'nama',
        'jenis_kelamin',
        'alamat',
        'kota',
        'kode_pos',
        'kelurahan',
        'telepon',
        'hp',
        'contact_person1',
        'contact_person2',
        'alamat_2',
        'kota_2',
        'kode_pos_2',
        'kelurahan_2',
        'kecamatan',
        'flag_member_khusus',
        'kode_outlet',
        'nama_outlet',
        'nama_sub_outlet',
        'tgl_registrasi',
        'kunjungan_pertama',
        'kunjungan_terakhir',
        'jumlah_kunjungan',
        'segmen_id',
        'nama_segmen',
        'tgl_lahir',
        'koordinat',
        'lat',
        'lng',
        'cus_nosalesman',
        'jarak'
    ];
}
