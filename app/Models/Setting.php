<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'tbmaster_setting';

    protected $fillable = ['radius_meter', 'minimal_menit'];

    // Matiin timestamps karena kita gak pake kolom created_at / updated_at
    public $timestamps = false;
}
