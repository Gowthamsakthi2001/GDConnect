<?php

namespace Modules\Purchase\Entities;

use App\Traits\FormatTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Entities\InventoryCategory;
use Modules\Inventory\Entities\InventoryParts;

class PurchaseDetail extends Model
{
    use FormatTimestamps;
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'category_id',
        'parts_id',
        'qty',
        'price',
        'total',
    ];

    /**
     * Get the purchase that owns the PurchaseDetail
     *
     * @return \Modules\Purchase\Database\factories\PurchaseDetailFactory
     */
    protected static function newFactory()
    {
        return \Modules\Purchase\Database\factories\PurchaseDetailFactory::new();
    }

    /**
     * Get the purchase that owns the PurchaseDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    /**
     * Get the category that owns the PurchaseDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    /**
     * Get the part that owns the PurchaseDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parts()
    {
        return $this->belongsTo(InventoryParts::class, 'parts_id');
    }
}
