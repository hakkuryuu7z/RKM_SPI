<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrRkm extends Model
{
    protected $table = 'tbtr_rkm';
    protected $primaryKey = 'rkm_id'; // Karena PK lu bukan 'id' tapi 'rkm_id'

    protected $fillable = [
        'rkm_user_id',
        'rkm_jlr_id',
        'rkm_nama_member',
        'rkm_kodemember',
        'rkm_tanggal',
        'rkm_foto',
        'rkm_order_status',
        'rkm_keteranganmember',
        'rkm_trx',
        'waktu_checkin',
        'waktu_checkout',
        'lat_kunjungan',
        'lng_kunjungan',
        'foto_kunjungan',
        'status_kunjungan'
    ];

    // Karena lu pake custom nama untuk created_at & updated_at:
    const CREATED_AT = 'rkm_create_dt';
    const UPDATED_AT = 'rkm_modify_dt';
    public function user()
    {
        // Parameter: Model Target, Foreign Key di tbtr_rkm, Primary Key di tbmaster_users
        return $this->belongsTo(User::class, 'rkm_user_id', 'user_id');
    }
}
