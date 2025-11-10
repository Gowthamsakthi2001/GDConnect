<?php

namespace Modules\Purchase\Entities;

use App\Traits\FormatTimestamps;
use App\Traits\GenerateCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Entities\Vendor;

class Purchase extends Model
{
    use FormatTimestamps;
    use GenerateCode;
    use HasFactory;

    protected $fillable = [
        'code',
        'vendor_id',
        'date',
        'total',
        'status',
        'req_img_path',
        'order_path',
    ];

    /**
     * Get the purchase that owns the PurchaseDetail
     *
     * @return \Modules\Purchase\Database\factories\PurchaseFactory
     */
    protected static function newFactory()
    {
        return \Modules\Purchase\Database\factories\PurchaseFactory::new();
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
        return setting('purchase.code_prefix').$value;
    }

    /**
     * Get the vendor that owns the Purchase
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the purchaseDetail for the Purchase
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}
