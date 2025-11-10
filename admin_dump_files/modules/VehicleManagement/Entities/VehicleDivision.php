<?php

namespace Modules\VehicleManagement\Entities;

use App\Traits\FormatTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDivision extends Model
{
    use FormatTimestamps;
    use HasFactory;

    // The mass assignable attributes.
    protected $fillable = [
        'name',
        'is_active',
    ];

    // cast attributes
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
