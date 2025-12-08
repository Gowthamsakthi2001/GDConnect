<?php

namespace Modules\MasterManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MasterManagement\Database\factories\AssetMasterVehicleFactory;

class LeasingPartnerMaster extends Model
{
    use HasFactory;
    

    protected $table = 'ev_tbl_leasing_partner_master';
    
    protected $primaryKey = 'id'; 

    public $incrementing = true; 

    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'status',
        'created_at' ,
        'updated_at'
    ];
}
