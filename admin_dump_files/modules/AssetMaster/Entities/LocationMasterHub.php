<?php

namespace Modules\AssetMaster\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AssetMaster\Entities\LocationMaster;

class LocationMasterHub extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_location_master_hubs'; 

    protected $fillable = [
        'location_id',
        'hub_name',
        'status',
        'created_at',
        'updated_at'
    ];

    public function location()
    {
        return $this->belongsTo(LocationMaster::class, 'location_id');
    }
}