<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Deliveryman\Entities\Deliveryman;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\BusinessSetting;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Mail;
class LiveOrderController extends Controller
{
   public function live_order_create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|unique:orders,store_order_id',
            'order_date' => 'required|date',
            'pickup_location' => 'required|string|max:255',
        
            'billing_customer_name' => 'required|string|max:255',
            'billing_last_name' => 'nullable|string|max:255',
            
            'billing_phone' => [
                'required',
                'string',
                'regex:/^[0-9]{10}$/'
            ],
        
            'billing_email' => 'required|email|max:255',
            'billing_address' => 'required|string|max:500',
            'billing_address2' => 'nullable|string|max:255',
        
            'billing_city' => 'required|string|max:100',
            'billing_pincode' => ['required','digits:6','regex:/^[1-9][0-9]{5}$/'],
            'billing_state' => 'required|string|max:100',
            'billing_country' => 'required|string|max:100',
            'billing_latitude' => 'required|numeric|between:-90,90',
            'billing_longitude' => 'required|numeric|between:-180,180',
        
            'shipping_is_billing' => 'required|boolean',
        
            'order_items' => 'required|array',
            'order_items.*' => 'required|array',
            'order_items.*.name' => 'required|string|max:255',
            'order_items.*.sku' => 'required|integer',
            'order_items.*.units' => 'required|integer|min:1',
            'order_items.*.selling_price' => 'required|numeric',
            'order_items.*.hsn' => 'required|max:50',

            'payment_method' => 'required|in:prepaid,cod',

            'length' => 'required|numeric|min:0.1',
            'breadth' => 'required|numeric|min:0.1',
            'height' => 'required|numeric|min:0.1',
            'weight' => 'required|numeric|min:0.1',
            'sub_total' => 'required|numeric|min:0.01',
            'collect_shipping_fees' => 'nullable|numeric|min:0',
            'shipping_method' => 'required|string|max:100|in:HL',
        
            
        ]);


        if ($validator->fails()) {
            return response()->json(['success' => false,'errors' => $validator->errors(),], 422); 
        }

        try {
            DB::beginTransaction();
        
            $customer_info = [
                'billing_customer_name' => $request->billing_customer_name,
                'billing_last_name'     => $request->billing_last_name ?? '',
                'billing_phone'         => $request->billing_phone,
                'billing_email'         => $request->billing_email,
                'billing_address'       => $request->billing_address,
                'billing_address2'      => $request->billing_address2 ?? '',
                'billing_city'          => $request->billing_city,
                'billing_pincode'       => $request->billing_pincode,
                'billing_state'         => $request->billing_state,
                'billing_country'       => $request->billing_country,
                'billing_latitude'      => $request->billing_latitude,
                'billing_longitude'     => $request->billing_longitude
            ];
        
            $order = new Order();
            $order->id = 100000 + Order::count() + 1;
            if (Order::find($order->id)) {
                $order->id = Order::orderBy('id', 'desc')->first()->id + 1;
            }

            $order->store_order_id = $request->order_id;
            $order->order_date = $request->order_date;
            $order->order_amount = $request->sub_total;
            $order->order_status = 'confirmed';
            $order->payment_status = $request->payment_method == 'cod' ? 'unpaid' : 'paid';
            $order->pickup_location = $request->pickup_location;
            $order->customer_info = json_encode($customer_info);
            $order->shipping_is_billing = $request->shipping_is_billing ? 1 : 0;
            $order->payment_method = $request->payment_method;
            $order->length = $request->length;
            $order->breadth = $request->breadth;
            $order->height = $request->height;
            $order->weight = $request->weight;
            $order->collect_shipping_fees = $request->collect_shipping_fees ?? 0;
            $order->shipping_method = $request->shipping_method;
            $order->save();

            foreach ($request->order_items as $item) {
                OrderDetail::create([
                    'order_id'     => $order->id,
                    'name' => $item['name'],
                    'sku'      => $item['sku'],
                    'units'     => $item['units'],
                    'price'        => $item['selling_price'],
                     'hsn'  => $item['hsn']
                ]);
            }
        
            DB::commit();
        
            return response()->json(['success' => true,'message' => 'Order created successfully','gdm_order_id' => $order->id,'channel_order_id'=>$order->store_order_id],200);
        
        } catch (\Exception $e) {
            DB::rollBack();
        
            return response()->json([ 'success' => false,'message' => 'Order creation failed','error' => $e->getMessage(),], 500);
        }
    
       
    }
}