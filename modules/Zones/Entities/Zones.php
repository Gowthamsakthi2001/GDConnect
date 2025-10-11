<?php

namespace Modules\Zones\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Zones\Database\factories\ZonesFactory;
use App\Models\EVState;
use Modules\City\Entities\City;

class Zones extends Model
{
    protected $table = 'zones';

    // Mass assignable fields
    protected $fillable = [
        'name',
        'state_id',
        'city_id',
        'address',
        'lat',
        'long',
        'coordinates',
        'status',
        'delete_status'
    ];

    // Casts
    protected $casts = [
        'status' => 'boolean',
    ];
    
    public function state()
    {
        return $this->belongsTo(EVState::class,'state_id');
    }
    
    public function city()
    {
        return $this->belongsTo(City::class,'city_id');
    }
}
