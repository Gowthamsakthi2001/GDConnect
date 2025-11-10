<?php

namespace Modules\MasterManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MasterManagement\Database\factories\AssetMasterVehicleFactory;

class RecoveryUpdatesMaster extends Model
{
    use HasFactory;
    protected $table = 'ev_tbl_recovery_updates_master';
    protected $fillable = [
        'label_name',
        'status',
        'created_at' ,
        'updated_at'
    ];
}
