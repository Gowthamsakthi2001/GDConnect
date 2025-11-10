<?php

namespace Modules\MasterManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MasterManagement\Database\factories\AssetMasterVehicleFactory;

class AssetOwnershipMaster extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_asset_ownership_master';

    protected $fillable = [
        'name',
        'status',
        'created_at' ,
        'updated_at'
    ];
}
