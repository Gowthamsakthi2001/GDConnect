<?php

namespace Modules\Inventory\Entities;

use App\Traits\FormatTimestamps;
use App\Traits\GenerateCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Entities\Employee;
use Modules\VehicleManagement\Entities\Vehicle;

class Expense extends Model
{
    use FormatTimestamps;
    use GenerateCode;
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'employee_id',
        'vendor_id',
        'vehicle_id',
        'trip_type_id',
        'trip_number',
        'odometer_millage',
        'vehicle_rent',
        'total',
        'date',
        'remarks',
        'status',
    ];

    // casted attributes
    protected $casts = [

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

    public static function newFactory()
    {
        return \Modules\Inventory\Database\factories\ExpenseFactory::new();
    }

    /**
     * Type of maintenance
     *
     * @return string[]
     */
    public static function getTypes()
    {
        return [
            'fuel' => 'Fuel',
            'maintenance' => 'Maintenance',
            'others' => 'Others',
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
        return setting('expense.code_prefix').$value;
    }

    /**
     * Get the employee that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the vendor that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Get the vehicle that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Get the trip type that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tripType()
    {
        return $this->belongsTo(TripType::class, 'trip_type_id');
    }

    /**
     * Get the details for the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(ExpenseDetail::class, 'expense_id');
    }
}
