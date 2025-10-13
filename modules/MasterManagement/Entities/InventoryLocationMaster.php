<?php

namespace Modules\MasterManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MasterManagement\Database\factories\AssetMasterVehicleFactory;

class InventoryLocationMaster extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_inventory_location_master';

    protected $fillable = [
        'name',
        'status',
        'created_at' ,
        'updated_at'
    ];
}
