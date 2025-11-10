<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvOtpVerification extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_otp_verification';

    protected $fillable = [
        'otp',
        'mobile_number',
    ];

    // Cast attributes to specific data types
    protected $casts = [
        'otp' => 'string',       // Casting OTP to string
        'mobile_number'=> 'string',// Casting type to string
    ];
}
