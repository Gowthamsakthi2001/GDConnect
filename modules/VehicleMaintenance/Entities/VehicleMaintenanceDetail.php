<?php

namespace Modules\VehicleMaintenance\Entities;

use App\Traits\FormatTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Entities\InventoryCategory;
use Modules\Inventory\Entities\InventoryParts;
use Modules\VehicleMaintenance\Database\factories\VehicleMaintenanceDetailFactory;

class VehicleMaintenanceDetail extends Model
{
    use FormatTimestamps;
    use HasFactory;

    // fillable attributes
    protected $fillable = [
        'maintenance_id',
        'category_id',
        'parts_id',
        'qty',
        'price',
        'total',
    ];

    // casted attributes
    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected static function newFactory()
    {
        return VehicleMaintenanceDetailFactory::new();
    }

    /**
     * Get the maintenance that owns the Vehicle Maintenance Detail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function maintenance()
    {
        return $this->belongsTo(VehicleMaintenance::class);
    }

    /**
     * Get the category that owns the Vehicle Maintenance Detail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(InventoryCategory::class);
    }

    /**
     * Get the part that owns the Vehicle Maintenance Detail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parts()
    {
        return $this->belongsTo(InventoryParts::class);
    }
}
