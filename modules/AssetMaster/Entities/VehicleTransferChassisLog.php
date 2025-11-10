<?php

namespace Modules\AssetMaster\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\AssetMaster\Database\factories\AssetMasterVehicleFactory;
use Modules\MasterManagement\Entities\VehicleTransfer;
use Modules\MasterManagement\Entities\VehicleTransferType;
use Modules\MasterManagement\Entities\InventoryLocationMaster;
use App\Models\User;

class VehicleTransferChassisLog extends Model
{
    protected $table = 'ev_tbl_chassis_transfer_logs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'transfer_id',
        'transfer_type',
        'transfer_date',
        'chassis_number',
        'vehicle_id',
        'from_location_source',
        'to_location_destination',
        'is_status',
        'status',
        'dm_id',
        'remarks',
        'created_by',
        'created_at',
        'updated_at',
        'type',
    ];
    
    public function vehicle_transfer(){
        return $this->belongsTo(VehicleTransfer::class,'transfer_id');
    }
    
    public function transferType()
    {
        return $this->belongsTo(VehicleTransferType::class, 'transfer_type', 'id');
    }
    
    public function FromLocation()
    {
        return $this->belongsTo(InventoryLocationMaster::class, 'from_location_source');
    }
    
    public function ToLocation()
    {
        return $this->belongsTo(InventoryLocationMaster::class, 'to_location_destination');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}