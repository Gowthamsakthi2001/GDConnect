<?php

namespace Modules\AssetMaster\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AssetMaster\Database\factories\AssetMasterVehicleFactory;

class AssetStatus extends Model
{
    use HasFactory;

    protected $table = 'ev_asset_status';

    protected $fillable = [
        'status_name',
        'status',
        'created_at',
        'updated_at',
    ];


}
