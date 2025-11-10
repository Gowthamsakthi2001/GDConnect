<?php

namespace Modules\B2B\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\B2B\Entities\B2BVehicleAssignmentLog;

class B2BReportAccident extends Model

{
    use HasFactory;
    
    protected $table = 'b2b_tbl_report_accident';
    public $timestamps = true;
     protected $fillable = [
        'accident_report_id',
        'assign_id',
        'date_of_accident',
        'time_of_accident',
        'location_of_accident',
        'accident_type',
        'description',
        'vehicle_id',
        'chassis_number',
        'rider_id',
        'rider_name',
        'rider_contact_number',
        'rider_license_number',
        'vehicle_damage',
        'rider_injury_description',
        'third_party_injury_description',
        'accident_attachments',     
        'police_report',   
        'client_business_name',
        'contact_person_name',
        'contact_number',
        'contact_email',
        'terms_condition',
        'created_by'
    ];
    
    public function assignment()
    {
        return $this->belongsTo(B2BVehicleAssignment::class, 'assign_id', 'id');
    }
    
    public function rider()
    {
        return $this->belongsTo(B2BRider::class, 'rider_id', 'id');
    }
    
    public function logs()
    {
        return $this->hasMany(B2BVehicleAssignmentLog::class, 'request_type_id')
            ->where('request_type', 'accident');
    }

}