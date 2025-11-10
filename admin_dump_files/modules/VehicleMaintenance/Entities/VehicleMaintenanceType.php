<?php

namespace Modules\VehicleMaintenance\Entities;

use App\Traits\FormatTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleMaintenanceType extends Model
{
    use FormatTimestamps;
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];
}
