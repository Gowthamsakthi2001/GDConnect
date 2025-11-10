<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvGlobalAadhaarResponse extends Model
{
    use HasFactory;

    protected $table = 'ev_global_aadhaar_responses';
    protected $fillable = [
        'aadhar_id',
        'ref_id',
        'response_data',
        'request_id',
    ];

    protected $casts = [
        'aadhar_id' => 'integer',
        'response_data' => 'array', 
    ];
}
