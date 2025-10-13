<?php

namespace Modules\VehicleRefueling\Entities;

use App\Traits\FormatTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelType extends Model
{
    use FormatTimestamps,
        HasFactory;

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
