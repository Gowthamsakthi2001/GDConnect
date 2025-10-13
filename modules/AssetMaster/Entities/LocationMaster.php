<?php

namespace Modules\AssetMaster\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AssetMaster\Entities\LocationMasterHub;
use Modules\AssetMaster\Entities\QualityCheck;
use App\Models\EVState;//updated by Mugesh.B

class LocationMaster extends Model
{
    use HasFactory;
    
    protected $table = 'ev_tbl_location_master';

    protected $fillable = [
        'name',
        'city',
        'state',
        'status',
        'city_code' ,
        'created_at',
        'updated_at'
    ];
    
    public function location_hubs()
    {
        return $this->hasMany(LocationMasterHub::class, 'location_id');
    }
    
     public function state_relation()
    {
        return $this->belongsTo(EVState::class, 'state', 'id');
    }

}