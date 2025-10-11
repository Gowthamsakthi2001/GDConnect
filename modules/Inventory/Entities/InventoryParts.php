<?php

namespace Modules\Inventory\Entities;

use App\Traits\FormatTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Purchase\Entities\PurchaseDetail;
use Modules\VehicleMaintenance\Entities\VehicleMaintenanceDetail;

class InventoryParts extends Model
{
    use FormatTimestamps;
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'location_id',
        'name',
        'description',
        'qty',
        'remarks',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the category that owns the InventoryParts
     */
    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    /**
     * Get the location that owns the InventoryParts
     */
    public function location()
    {
        return $this->belongsTo(InventoryLocation::class, 'location_id');
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'parts_id');
    }

    public function vehicleMaintenanceDetails()
    {
        return $this->hasMany(VehicleMaintenanceDetail::class, 'parts_id');
    }
}
