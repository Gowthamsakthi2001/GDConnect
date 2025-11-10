<?php

namespace Modules\MasterManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MasterManagement\Database\factories\AssetMasterVehicleFactory;

class VehicleTransferType extends Model
{
    use HasFactory;

    protected $table = 'vehicle_transfer_types';

    protected $fillable = [
        'name',
        'created_at' ,
        'updated_at'
    ];
}
