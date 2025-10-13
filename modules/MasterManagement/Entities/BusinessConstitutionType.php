<?php

namespace Modules\MasterManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MasterManagement\Database\factories\AssetMasterVehicleFactory;

class BusinessConstitutionType extends Model
{
    use HasFactory;
    protected $table = 'business_constitution_types';
    protected $fillable = [
        'name',
        'status',
        'created_at' ,
        'updated_at'
    ];
}
