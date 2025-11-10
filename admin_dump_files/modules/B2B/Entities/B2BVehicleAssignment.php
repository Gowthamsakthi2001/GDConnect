<?php

namespace Modules\B2B\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\B2B\Entities\B2BRider;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\AssetMaster\Entities\LocationMaster;
use Modules\B2B\Entities\B2BVehicleRequests;
use Modules\B2B\Entities\B2BServiceRequest;
use Modules\B2B\Entities\B2BVehicleAssignmentLog;

class B2BVehicleAssignment extends Model

{
    use HasFactory;
    
    protected $table = 'b2b_tbl_vehicle_assignments';
    public $timestamps = true;
    protected $fillable = [
        'req_id',
        'rider_id',
        'asset_vehicle_id',
        'handover_type',
        'assigned_agent_id',
        'zone_id',
        'status',
        'kilometer_value',
        'odometer_value',
        'kilometer_image',
        'odometer_image',
        'vehicle_front',
        'vehicle_back',
        'vehicle_top',
        'vehicle_bottom',
        'vehicle_left',
        'vehicle_right',
        'vehicle_battery',
        'vehicle_charger',
        'created_at',
        'updated_at',
    ];
    
    public function rider()
    {
        return $this->belongsTo(B2BRider::class, 'rider_id', 'id');
    }
    
    
    public function vehicle()
    {
        return $this->belongsTo(AssetMasterVehicle::class, 'asset_vehicle_id', 'id');
    }
    
    public function zone()
    {
        return $this->belongsTo(LocationMaster::class, 'zone_id', 'id');
    }
    
    public function VehicleRequest()
    {
        return $this->belongsTo(B2BVehicleRequests::class, 'req_id', 'req_id');
    }
    
    
    public function serviceRequest()
    {
        return $this->belongsTo(B2BServiceRequest::class, 'assign_id');
    }
    
    
    public function logs()
    {
        return $this->hasMany(B2BVehicleAssignmentLog::class, 'assignment_id');
    }
}