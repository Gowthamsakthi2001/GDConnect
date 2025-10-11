<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
class OrderDetail extends Model
{
    protected $table = 'order_details';

    protected $fillable = [
        'order_id',
        'name',
        'sku',
        'units',
        'hsn',
        'price',
        'discount_type',
    ];

    protected $casts = [
        'units' => 'integer',
        'price' => 'float',
    ];

    /**
     * Relationship: OrderDetail belongs to an Order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
