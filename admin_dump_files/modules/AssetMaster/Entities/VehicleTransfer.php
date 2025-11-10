<?php

namespace Modules\AssetMaster\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\AssetMaster\Database\factories\AssetMasterVehicleFactory;
use Modules\AssetMaster\Entities\VehicleTransferLog;
use Modules\AssetMaster\Entities\VehicleTransferDetail;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\MasterManagement\Entities\VehicleTransferType;
use Modules\MasterManagement\Entities\CustomerMaster;


class VehicleTransfer extends Model
{
    protected $table = 'ev_tbl_vehicle_transfers';
    protected $primaryKey = 'id';
    public $incrementing = false; 
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'transfer_type',
        'transfer_date',
        'custom_master_id',
        'from_location_source',
        'to_location_destination',
        'initial_status',
        'return_status',
        'remarks',
        'status',
        'created_by',
        'created_at',
        'updated_at',
    ];

    public function transfer_details()
    {
        return $this->hasMany(VehicleTransferDetail::class, 'transfer_id', 'id');
    }
    
    public function transferLogs()
    {
        return $this->hasMany(VehicleTransferLog::class, 'transfer_id', 'id');
    }
    
    public function transferType()
    {
        return $this->belongsTo(VehicleTransferType::class, 'transfer_type', 'id');
    }
    
    public function customerMaster()
    {
        return $this->belongsTo(CustomerMaster::class, 'custom_master_id', 'id');
    }
    
  
}