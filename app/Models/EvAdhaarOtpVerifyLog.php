<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvAdhaarOtpVerifyLog extends Model
{
    use HasFactory;

    protected $table = 'ev_adhaar_verify_log'; 
    protected $primaryKey = 'id'; 
    public $timestamps = true; 

    protected $fillable = [
        'request_id',
        'response' ,
        'adhaar_no',
        'dm_id'
    ];

    protected $casts = [
        'response' => 'array',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
