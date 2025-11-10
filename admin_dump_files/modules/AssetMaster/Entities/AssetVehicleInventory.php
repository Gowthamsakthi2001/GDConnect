<?php

namespace Modules\AssetMaster\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AssetMaster\Database\factories\AssetMasterVehicleFactory;
use Modules\AssetMaster\Entities\AssetStatus;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\MasterManagement\Entities\InventoryLocationMaster;

class AssetVehicleInventory extends Model
{
    use HasFactory;

    protected $table = 'asset_vehicle_inventories';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id' ,
        'asset_vehicle_id',
        'asset_vehicle_status',
        'transfer_status',
        'is_status',
        'user_id',
        'created_by',
        'created_at',
        'updated_at'
    ];
    
    public function assetVehicle()
    {
        return $this->belongsTo(AssetMasterVehicle::class, 'asset_vehicle_id', 'id');
    }
    
     public function inventory_location()
    {
        return $this->belongsTo(InventoryLocationMaster::class, 'transfer_status', 'id');
    }
    
    

    
}