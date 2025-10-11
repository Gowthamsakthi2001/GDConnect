<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvTelecaller extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_telecallers';

    protected $guarded = [];

    protected $casts = [
        'telecaller_name' => 'string',
        'telecaller_profile' => 'text',
        'mobile' => 'string',

    ];


}
