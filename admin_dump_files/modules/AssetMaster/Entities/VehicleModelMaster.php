<?php

namespace Modules\AssetMaster\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AssetMaster\Database\factories\AssetMasterVehicleFactory;
use Modules\AssetMaster\Entities\QualityCheckReinitiate;
use Modules\VehicleManagement\Entities\VehicleType;
use Modules\AssetMaster\Entities\BrandModelMaster;

class VehicleModelMaster extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_vehicle_models';
    protected $primaryKey = 'id';

    protected $fillable = [
        'brand',
        'vehicle_type',
        'vehicle_model',
        'make',
        'variant',
        'color' ,
        'status',
        'created_at' ,
        'updated_at'
    ];
    
    public function brand()
    {
        return $this->belongsTo(BrandModelMaster::class, 'brand');
    }
    
    public function vehicle_type()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type');
    }

  
}
