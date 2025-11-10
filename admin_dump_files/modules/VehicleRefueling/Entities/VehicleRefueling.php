<?php

namespace Modules\VehicleRefueling\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Entities\Driver;
use Modules\VehicleManagement\Entities\Vehicle;
use Modules\VehicleRefueling\Database\factories\VehicleRefuelingFactory;

class VehicleRefueling extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'fuel_type_id',
        'fuel_station_id',
        'refueled_at',
        'place',
        'budget',
        'km_per_unit',
        'last_reading',
        'last_unit',
        'refuel_limit',
        'max_unit',
        'unit_taken',
        'odometer_day_end',
        'odometer_refuel_time',
        'consumption_percent',
        'slip_path',
        'strict_policy',
    ];

    protected $casts = [
        'refueled_at' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function fuel_type()
    {
        return $this->belongsTo(FuelType::class);
    }

    public function fuel_station()
    {
        return $this->belongsTo(FuelStation::class);
    }

    protected static function newFactory()
    {
        return VehicleRefuelingFactory::new();
    }

    /**
     * Get the slip url attribute.
     *
     * @return ?string
     */
    public function getSlipUrlAttribute(): ?string
    {
        return $this->slip_path ? storage_asset($this->slip_path) : null;
    }
}
