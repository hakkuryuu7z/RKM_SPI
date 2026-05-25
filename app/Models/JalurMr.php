<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JalurMr extends Model
{
    protected $table = 'tbtr_jalur_mr';
    protected $primaryKey = 'jlr_id';

    // Karena lu pakai nama kolom custom buat timestamp
    public $timestamps = false;

    protected $fillable = [
        'jlr_user_id',
        'jlr_kodemember',
        'jlr_tanggal_rkm',
        'jlr_create_dt',
        'jlr_modify_dt',

    ];

    // Relasi ke tabel User (Siapa MR-nya)
    public function user()
    {
        return $this->belongsTo(User::class, 'jlr_user_id', 'user_id');
    }

    // Relasi ke tabel Member (Toko mana yang dikunjungi)
    public function member()
    {
        return $this->belongsTo(Member::class, 'jlr_kodemember', 'kode');
    }
}
