<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDetail;
class Order extends Model
{
    use HasFactory;

    protected $table = 'orders'; 
    public $timestamps = true; 
   
    protected $fillable = [
        'store_order_id', 'order_date', 'pickup_location', 'order_amount',
        'customer_info', 'coupon_discount_amount', 'payment_status', 'order_status',
        'total_tax_amount', 'payment_method', 'order_note', 'order_type',
        'store_id', 'delivery_charge', 'schedule_at', 'callback', 'otp',
        'pending', 'accepted', 'confirmed', 'processing', 'handover', 'picked_up',
        'delivered', 'canceled', 'refund_requested', 'refunded', 'delivery_address',
        'scheduled', 'store_discount_amount', 'original_delivery_charge', 'failed',
        'adjusment', 'edited', 'delivery_time', 'order_attachment', 'receiver_details',
        'charge_payer', 'distance', 'dm_tips', 'free_delivery_by', 'refund_request_canceled',
        'prescription_order', 'tax_status', 'dm_vehicle_id', 'cancellation_reason',
        'canceled_by', 'coupon_created_by', 'processing_time', 'unavailable_item_note',
        'cutlery', 'delivery_instruction', 'tax_percentage', 'additional_charge',
        'is_guest', 'shipping_is_billing', 'length', 'breadth', 'height', 'weight',
        'collect_shipping_fees', 'whatsapp_message', 'dm_whatsapp_message',
        'invoice_whatsapp_message', 'invoice_message_name', 'status_sent_at'
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'schedule_at' => 'datetime',
        'pending' => 'datetime',
        'accepted' => 'datetime',
        'confirmed' => 'datetime',
        'processing' => 'datetime',
        'handover' => 'datetime',
        'picked_up' => 'datetime',
        'delivered' => 'datetime',
        'canceled' => 'datetime',
        'refund_requested' => 'datetime',
        'refunded' => 'datetime',
        'refund_request_canceled' => 'datetime',
        'failed' => 'datetime',
        'status_sent_at' => 'datetime',

        'order_amount' => 'float',
        'coupon_discount_amount' => 'float',
        'total_tax_amount' => 'float',
        'delivery_charge' => 'float',
        'store_discount_amount' => 'float',
        'original_delivery_charge' => 'float',
        'adjusment' => 'float',
        'distance' => 'float',
        'dm_tips' => 'float',
        'tax_percentage' => 'float',
        'additional_charge' => 'float',
        'shipping_is_billing' => 'float',
        'length' => 'float',
        'breadth' => 'float',
        'height' => 'float',
        'weight' => 'float',
        'collect_shipping_fees' => 'float',

        'customer_info' => 'array',
        'receiver_details' => 'array',

        'scheduled' => 'boolean',
        'edited' => 'boolean',
        'cutlery' => 'boolean',
        'prescription_order' => 'boolean',
        'is_guest' => 'boolean',

        'dm_whatsapp_message' => 'integer',
        'invoice_whatsapp_message' => 'integer',
    ];
    
    public function order_details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }


}
