<?php

namespace Modules\AssetMaster\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AssetMaster\Database\factories\AssetMasterVehicleFactory;

class BrandModelMaster extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_brands';

    protected $fillable = [
        'brand_name',
        'status',
        'created_at' ,
        'updated_at'
    ];
}
