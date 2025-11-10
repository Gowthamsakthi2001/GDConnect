<?php

namespace Modules\VehicleManagement\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Entities\Department;
use Modules\Employee\Entities\Driver;
use Modules\Inventory\Entities\Vendor;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department_id',
        'registration_date',
        'license_plate',
        'alert_cell_no',
        'alert_email',
        'ownership_id',
        'vehicle_type_id',
        'vehicle_division_id',
        'rta_circle_office_id',
        'driver_id',
        'vendor_id',
        'seat_capacity',
    ];

    protected static function newFactory()
    {
        return \Modules\VehicleManagement\Database\factories\VehicleFactory::new();
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function vehicle_type()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function ownership()
    {
        return $this->belongsTo(VehicleOwnershipType::class);
    }

    public function vehicle_division()
    {
        return $this->belongsTo(VehicleDivision::class);
    }

    public function rta_circle_office()
    {
        return $this->belongsTo(RTAOffice::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
