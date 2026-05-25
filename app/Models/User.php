<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Tambahkan baris ini di dalam class User
    // public $incrementing = false;
    public $timestamps = false;

    // Arahin ke tabel custom lu
    protected $table = 'tbmaster_users';

    // Custom primary key
    protected $primaryKey = 'user_id';

    // Kolom apa aja yang boleh diisi
    protected $fillable = [
        // 'user_id',
        'user_username',
        'user_password',
        'user_role_id',
    ];

    // Sembunyiin password kalau datanya ditarik
    protected $hidden = [
        'user_password',
    ];

    // Kasih tau Laravel kalau password lu ada di kolom 'user_password' 
    // (Biar fitur Auth bawaan laravel tetep jalan)
    public function getAuthPassword()
    {
        return $this->user_password;
    }

    // Bikin Relasi ke tabel Role (1 User punya 1 Role)
    public function role()
    {
        // belongsTo(NamaModel, 'kolom_di_tabel_user', 'kolom_primary_di_tabel_role')
        return $this->belongsTo(Role::class, 'user_role_id', 'role_id');
    }
}
