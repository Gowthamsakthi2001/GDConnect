<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateKycUpdate extends Model
{
    protected $table = 'candidate_kyc_updates';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = true;
    protected $fillable = [
        'dm_id',
        'aadhaar_front_verified',
        'aadhaar_front_verified_at',
        'aadhaar_front_approved_by',
        'aadhaar_back_verified',
        'aadhaar_back_verified_at',
        'aadhaar_back_approved_by',
        'pan_verified',
        'pan_verified_at',
        'pan_approved_by',
        'dl_front_verified',
        'dl_front_verified_at',
        'dl_front_approved_by',
        'dl_back_verified',
        'dl_back_verified_at',
        'dl_back_approved_by',
    ];

    protected $casts = [
        'aadhaar_front_verified_at' => 'datetime',
        'aadhaar_back_verified_at' => 'datetime',
        'pan_verified_at' => 'datetime',
        'dl_front_verified_at' => 'datetime',
        'dl_back_verified_at' => 'datetime',
    ];
}
