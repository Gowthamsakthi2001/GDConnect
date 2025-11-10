<?php

namespace Modules\Inventory\Entities;

use App\Models\User;
use App\Traits\FormatTimestamps;
use App\Traits\GenerateCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Database\factories\InventoryPartsUsageFactory;
use Modules\VehicleManagement\Entities\Vehicle;

class InventoryPartsUsage extends Model
{
    use FormatTimestamps;
    use GenerateCode;
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var array
     */
    protected $fillable = [
        'created_by',
        'code',
        'vehicle_id',
        'date',
        'remarks',
        'status',
    ];

    /**
     * The table associated with the model.
     *
     * @return InventoryPartsUsageFactory
     */
    protected static function newFactory()
    {
        return InventoryPartsUsageFactory::new();
    }

    /**
     * Get the code.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendor) {
            $vendor->code = $vendor->generateCode();
        });
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
     * Get the code.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getCodeAttribute($value)
    {
        return setting('inventory.parts_code_prefix').$value;
    }

    /**
     * Get the details for the usage.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(InventoryPartsUsageDetail::class, 'parts_usage_id');
    }

    /**
     * Get the vehicle that owns the usage.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the user that owns the usage.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
