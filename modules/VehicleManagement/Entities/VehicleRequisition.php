<?php

namespace Modules\VehicleManagement\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Entities\Driver;
use Modules\Employee\Entities\Employee;

class VehicleRequisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'vehicle_type_id',
        'where_from',
        'where_to',
        'pickup',
        'requisition_date',
        'time_from',
        'time_to',
        'tolerance',
        'number_of_passenger',
        'driver_id',
        'purpose',
        'details',
        'status',
    ];

    /**
     * statuses of maintenance
     *
     * @return string[]
     */
    public static function getStatues()
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
    }

    /**
     * The model's default values for attributes.
     *
     * @return \Modules\VehicleManagement\Database\factories\VehicleRequisitionFactory
     */
    protected static function newFactory()
    {
        return \Modules\VehicleManagement\Database\factories\VehicleRequisitionFactory::new();
    }

    /**
     * Get the employee that owns the VehicleRequisition
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the vehicleType that owns the VehicleRequisition
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    /**
     * Get the driver that owns the VehicleRequisition
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get the purpose that owns the VehicleRequisition
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purpose()
    {
        return $this->belongsTo(VehicleRequisitionPurpose::class);
    }
}
