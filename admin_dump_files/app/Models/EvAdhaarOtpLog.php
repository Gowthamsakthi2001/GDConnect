<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvAdhaarOtpLog extends Model
{
    use HasFactory;


    protected $table = 'ev_adhaar_otp_log'; 
    protected $primaryKey = 'id'; 
    public $timestamps = true; 

    protected $fillable = [
        'adhaar_no',
        'request_id',
        'ref_id',
        'message',
        'dm_id' ,
        'json_data',
    ];
     protected $casts = [
        'json_data' => 'array',
    ];


    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
