<?php

namespace Modules\MasterManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MasterManagement\Database\factories\AssetMasterVehicleFactory;

class RecoveryReasonMaster extends Model
{
    use HasFactory;
    protected $table = 'ev_tbl_recovery_reason_master';
    protected $fillable = [
        'type',
        'label_name',
        'status',
        'created_at' ,
        'updated_at'
    ];
}
