<?php

namespace Modules\AssetMaster\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AssetMaster\Database\factories\AssetMasterVehicleFactory;
use Modules\VehicleManagement\Entities\VehicleType;

class QualityCheckMaster extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_qc_list_master';


    protected $fillable = [
        'label_name',
        'vehicle_type_id',
        'status',
        'created_at' ,
        'updated_at'
    ];
    
    public function vehicle_type()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

}
