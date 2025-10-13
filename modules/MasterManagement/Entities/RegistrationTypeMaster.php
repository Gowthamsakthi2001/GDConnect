<?php

namespace Modules\MasterManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MasterManagement\Database\factories\AssetMasterVehicleFactory;

class RegistrationTypeMaster extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_registration_types';

    protected $fillable = [
        'name',
        'status',
        'created_at' ,
        'updated_at'
    ];
}
