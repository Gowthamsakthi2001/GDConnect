<?php

namespace Modules\VehicleMaintenance\Entities;

use App\Traits\FormatTimestamps;
use App\Traits\GenerateCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Entities\Employee;
use Modules\VehicleMaintenance\Database\factories\VehicleMaintenanceFactory;
use Modules\VehicleManagement\Entities\Vehicle;

class VehicleMaintenance extends Model
{
    use FormatTimestamps;
    use GenerateCode;
    use HasFactory;

    // fillable attributes
    protected $fillable = [
        'code',
        'employee_id',
        'vehicle_id',
        'maintenance_type_id',
        'title',
        'date',
        'remarks',
        'charge_bear_by',
        'charge',
        'total',
        'type',
        'priority',
        'status',
    ];

    // casted attributes
    protected $casts = [

        'charge' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->code = $model->generateCode();
        });
    }

    /**
     * Type of maintenance
     *
     * @return string[]
     */
    public static function getTypes()
    {
        return [
            'maintenance' => 'Maintenance',
            'general' => 'General',
        ];
    }

    /**
     * priorities of maintenance
     *
     * @return string[]
     */
    public static function getPriorities()
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
        ];
    }

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
     * Get the code.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getCodeAttribute($value)
    {
        return setting('maintenance.code_prefix').$value;
    }

    /**
     * Get the employee that owns the VehicleMaintenance
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(VehicleMaintenanceDetail::class, 'maintenance_id');
    }

    /**
     * Get the employee that owns the VehicleMaintenance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the vehicle that owns the VehicleMaintenance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Get the Vehicle Maintenance Type that owns the Vehicle Maintenance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function maintenanceType()
    {
        return $this->belongsTo(VehicleMaintenanceType::class, 'maintenance_type_id');
    }

    /**
     * Factory for the model
     *
     * @return VehicleMaintenanceFactory
     */
    protected static function newFactory()
    {
        return VehicleMaintenanceFactory::new();
    }
}
