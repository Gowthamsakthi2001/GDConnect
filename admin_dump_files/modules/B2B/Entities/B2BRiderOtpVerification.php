<?php

namespace Modules\B2B\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class B2BRiderOtpVerification extends Model
{
    use HasFactory;
    
    protected $table = 'b2b_tbl_rider_otp_verification';
    public $timestamps = true;

    protected $fillable = [
        'otp',
        'mobile_number'
        
    ];
    
}