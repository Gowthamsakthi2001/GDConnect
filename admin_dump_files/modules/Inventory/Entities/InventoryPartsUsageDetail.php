<?php

namespace Modules\Inventory\Entities;

use App\Traits\FormatTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Database\factories\InventoryPartsUsageDetailFactory;

class InventoryPartsUsageDetail extends Model
{
    use FormatTimestamps;
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var array
     */
    protected $fillable = [
        'inventory_parts_usage_id',
        'category_id',
        'parts_id',
        'qty',
    ];

    /**
     * The table associated with the model.
     *
     * @return InventoryPartsUsageDetailFactory
     */
    protected static function newFactory()
    {
        return InventoryPartsUsageDetailFactory::new();
    }

    /**
     * Get the usage that owns the detail.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usage()
    {
        return $this->belongsTo(InventoryPartsUsage::class, 'parts_usage_id');
    }

    /**
     * Get the category that owns the detail.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(InventoryCategory::class);
    }

    /**
     * Get the parts that owns the detail.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parts()
    {
        return $this->belongsTo(InventoryParts::class);
    }
}
