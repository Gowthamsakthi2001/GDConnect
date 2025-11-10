<?php

namespace Modules\VehicleRefueling\Entities;

use App\Traits\FormatTimestamps;
use App\Traits\GenerateCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\VehicleManagement\Entities\Vehicle;

class FuelRequisition extends Model
{
    use FormatTimestamps;
    use GenerateCode;
    use HasFactory;

    protected $fillable = [
        'code',
        'vehicle_id',
        'station_id',
        'type_id',
        'qty',
        'current_qty',
        'date',
        'status',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'current_qty' => 'decimal:2',
    ];

    // boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->code = $model->generateCode();
        });
    }

    public static function newFactory()
    {
        return \Modules\VehicleRefueling\Database\factories\FuelRequisitionFactory::new();
    }

    /**
     * Get the code.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getCodeAttribute($value)
    {
        return setting('fuel.requisition_code_prefix').$value;
    }

    /**
     * Get the statues.
     */
    public static function getStatues(): array
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
    }

    /**
     * Get the vehicle that owns the FuelRequisition
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the station that owns the FuelRequisition
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function station()
    {
        return $this->belongsTo(FuelStation::class);
    }

    /**
     * Get the type that owns the FuelRequisition
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(FuelType::class);
    }
}
