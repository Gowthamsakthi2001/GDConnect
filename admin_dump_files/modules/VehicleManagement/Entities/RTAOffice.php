<?php

namespace Modules\VehicleManagement\Entities;

use App\Traits\FormatTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RTAOffice extends Model
{
    use FormatTimestamps,
        HasFactory;

    // set table name
    protected $table = 'rta_offices';

    // The mass assignable attributes.
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    // cast attributes
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
