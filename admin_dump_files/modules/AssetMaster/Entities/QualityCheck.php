<?php

namespace Modules\AssetMaster\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AssetMaster\Database\factories\AssetMasterVehicleFactory;
use Modules\AssetMaster\Entities\QualityCheckReinitiate;
use Modules\VehicleManagement\Entities\VehicleType;
use Modules\AssetMaster\Entities\VehicleModelMaster;
use Modules\AssetMaster\Entities\LocationMaster;
use App\Models\User;
use Modules\MasterManagement\Entities\CustomerMaster;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\Zones\Entities\Zones;

class QualityCheck extends Model
{
    use HasFactory;

    protected $table = 'vehicle_qc_check_lists';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'vehicle_type',
        'vehicle_model',
        'location',
        'zone_id',
        'accountability_type',
        'chassis_number',
        'customer_id' ,
        'is_recoverable',
        'battery_number',
        'telematics_number',
        'motor_number',
        'datetime',
        'status',
        'technician',
        'dm_id' ,
        'role',
        'image',
        'remarks',
        'created_at' ,
        'delete_status',
        'delete_remarks',
        'check_lists',
        'updated_at'
    ];
    
    public function vehicle_model_relation()
    {
        return $this->belongsTo(VehicleModelMaster::class, 'vehicle_model', 'id');
    }
    
    public function customer_relation()
    {
        return $this->belongsTo(CustomerMaster::class, 'customer_id', 'id');
    }

    
    public function vehicle_type_relation()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type','id');
    }
    
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician');
    }

    public function qc_reinitiate()
    {
        return $this->hasMany(QualityCheckReinitiate::class, 'qc_id', 'id');
    }
    
     public function location_relation()
    {
        return $this->belongsTo(LocationMaster::class, 'location' , 'id');
    }

    public function zone()
    {
        return $this->belongsTo(Zones::class, 'zone_id' , 'id');
    }

    public function delivery_man()
    {
        return $this->belongsTo(Deliveryman::class, 'dm_id' ,'id');
    }


}
