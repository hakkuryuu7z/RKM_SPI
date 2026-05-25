<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // --- GANTI NAMA TABELNYA JADI INI BRO ---
    protected $table = 'tbmaster_role';

    // Pastikan primary key-nya juga bener
    protected $primaryKey = 'role_id';

    // (Kalau kolom rkm_create_dt / rkm_modify_dt di tabel ini gak pake format standar Laravel created_at/updated_at, tambahin ini biar aman:)
    public $timestamps = false;
}
