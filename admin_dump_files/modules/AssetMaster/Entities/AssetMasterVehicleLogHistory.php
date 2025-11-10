<?php

namespace Modules\AssetMaster\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AssetMaster\Database\factories\AssetMasterVehicleFactory;
use Modules\AssetMaster\Entities\AssetStatus;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use App\Models\User;

class AssetMasterVehicleLogHistory extends Model
{
    use HasFactory;

    protected $table = 'asset_master_vehicle_log_history';
    
    protected $fillable = [
        'user_id',
        'remarks',
        'status_type',
        'asset_vehicle_id'
    ];
    
    public function assetVehicle()
    {
        return $this->belongsTo(AssetMasterVehicle::class, 'asset_vehicle_id', 'id');
    }
    
    public function get_comment_user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    
}