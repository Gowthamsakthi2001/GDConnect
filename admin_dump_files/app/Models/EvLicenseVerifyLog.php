<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvLicenseVerifyLog extends Model
{
    protected $table = 'ev_license_verify_log'; 
    protected $primaryKey = 'id'; 
    public $timestamps = true; 

    protected $fillable = [
        'request_id', 'license_number', 'dob', 'holder_name',
        'father_or_husband_name', 'gender', 'issue_date',
        'rto_code', 'rto', 'state', 'valid_from', 'valid_upto',
        'blood_group', 'vehicle_class', 'image', 'message',
        'permanent_address', 'permanent_zip', 'temporary_address',
        'temporary_zip', 'transport_validity', 'non_transport_validity' ,'dm_id'
    ];

    protected $casts = [
        'dob'                  => 'date',
        'issue_date'           => 'date',
        'valid_from'           => 'date',
        'valid_upto'           => 'date',
        'image'                => 'string',
        'vehicle_class'        => 'array', // JSON field
        'transport_validity'   => 'array', // JSON field
        'non_transport_validity' => 'array', // JSON field
    ];
    
    public function get_vehicle_class(){
        $classes = !empty($this->vehicle_class) ? $this->vehicle_class :[];
        $formattedClasses = [];
        foreach ($classes as $class) {
            $cov = $class['cov'] ?? 'N/A';
            $expiryDate = $class['expiryDate'] ?? 'N/A';
            $issueDate = $class['issueDate'] ?? 'N/A';
            $formattedClasses[] = "$cov - Expiry: $expiryDate, Issued: $issueDate";
        }
         return implode(', ', $formattedClasses);
    }
}
