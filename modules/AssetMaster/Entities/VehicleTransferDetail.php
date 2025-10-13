<?php

namespace Modules\AssetMaster\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AssetMaster\Database\factories\AssetMasterVehicleFactory;
use Modules\AssetMaster\Entities\VehicleTransfer;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\MasterManagement\Entities\InventoryLocationMaster;

class VehicleTransferDetail extends Model
{
    protected $table = 'ev_tbl_vehicle_transfer_details';
    protected $primaryKey = 'id';
    public $incrementing = false; 
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'transfer_id',
        'inventory_id',
        'vehicle_id',
        'chassis_number',
        'dm_id',
        'from_location_source',
        'to_location_destination',
        'return_location',
        'return_remarks',
        'return_transfer_date',
        'created_at',
        'updated_at',
    ];
    
    public function asset_vehicle()
    {
        return $this->belongsTo(AssetMasterVehicle::class, 'vehicle_id');
    }
    
    public function FromLocation()
    {
        return $this->belongsTo(InventoryLocationMaster::class, 'from_location_source');
    }
    
       public function ToLocation()
    {
        return $this->belongsTo(InventoryLocationMaster::class, 'to_location_destination');
    }


    public function transfer()
    {
        return $this->belongsTo(VehicleTransfer::class, 'transfer_id', 'id');
    }
    
    public function deliveryman()
    {
        return $this->belongsTo(Deliveryman::class, 'dm_id', 'id');
    }
    
}