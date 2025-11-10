<?php

namespace Modules\VehicleManagement\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleRouteDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_name',
        'description',
        'starting_point',
        'destination_point',
        'is_active',
        'create_pick_drop_point',
    ];
}
