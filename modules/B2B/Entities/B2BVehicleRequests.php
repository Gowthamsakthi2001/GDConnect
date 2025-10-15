<?php

namespace Modules\B2B\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\B2B\Entities\B2BRider;
use Modules\B2B\Entities\B2BAgent;
use Modules\B2B\Entities\B2BVehicleAssignment;
use Modules\MasterManagement\Entities\CustomerLogin;
use Modules\VehicleManagement\Entities\VehicleType;
use Modules\Zones\Entities\Zones; //updated by Gowtham.s
use Modules\City\Entities\City; //updated by Gowtham.s
class B2BVehicleRequests extends Model
{
    use HasFactory;
    
    protected $table = 'b2b_tbl_vehicle_requests';
public $timestamps = true;
    protected $fillable = [
        'req_id',
        'rider_id',
        'start_date',
        'end_date',
        'vehicle_type',
        'battery_type',
        'created_at',
        'status',
        'qrcode_image',
        'terms_condition',
        'city_id',
        'zone_id',
        'updated_at',
        'created_by',
    ];
    
    
    public function rider()
    {
        return $this->belongsTo(B2BRider::class, 'rider_id', 'id');
    }
    
    public function agent()
    {
        return $this->belongsTo(B2BAgent::class, 'closed_by', 'id');
    }
    
    public function customerLogin()
    {
        return $this->belongsTo(CustomerLogin::class, 'created_by', 'id');
    }
    
     public function city() //updated by Gowtham.s
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
    
    public function zone()  //updated by Gowtham.s
    {
        return $this->belongsTo(Zones::class, 'zone_id', 'id');
    }
    
        public function assignment()
    {
        return $this->belongsTo(B2BVehicleAssignment::class, 'req_id', 'req_id');
    }
    
    
    public function vehicle_type_relation()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type','id');
    }
}