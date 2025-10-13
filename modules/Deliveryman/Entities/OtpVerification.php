<?php

namespace Modules\Deliveryman\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Deliveryman\Database\factories\OtpVerificationFactory;

class OtpVerification extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_otp_verification';

    protected $fillable = [
        'otp',
        'type_id',
        'type',
    ];

    // Cast attributes to specific data types
    protected $casts = [
        'otp' => 'string',       // Casting OTP to string
        'type_id' => 'integer',  // Casting type_id to integer
        'type' => 'string',      // Casting type to string
    ];
    
    protected static function newFactory(): OtpVerificationFactory
    {
        //return OtpVerificationFactory::new();
    }
}
