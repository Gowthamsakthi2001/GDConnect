<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvPancardVerifyLog extends Model
{
    use HasFactory;

    protected $table = 'ev_pancard_verify_log'; 

    protected $fillable = [
        'request_id',
        'pan_no',
        'registered_name',
        'message',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true; 
}
