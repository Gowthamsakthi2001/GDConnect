<?php

namespace Modules\B2B\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\B2B\Entities\B2BVehicleAssignmentLog;

class B2BServiceRequest extends Model
{
    use HasFactory;
    
    protected $table = 'b2b_tbl_service_request';
    public $timestamps = true;
    protected $fillable = [
        'assign_id',
        'vehicle_number',
        'city',
        'ticket_id',
        'zone_id',
        'state',
        'vehicle_type',
        'driver_name',
        'driver_number',
        'poc_name',
        'poc_number',
        'status',
        'current_status',
        'contact_number',
        'description',
        'address',
        'repair_type',
        'latitude',
        'longitude',
        'gps_pin_address',
        'image',
        'created_at',
        'updated_at',
        'created_by',
        'type'
    ];
    
    public function assignment()
    {
        return $this->belongsTo(B2BVehicleAssignment::class, 'assign_id', 'id');
    }
    
    public function logs()
    {
        return $this->hasMany(B2BVehicleAssignmentLog::class, 'request_type_id')
            ->where('request_type', 'service_request');
    }
}