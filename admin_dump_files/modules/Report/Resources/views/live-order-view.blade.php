<x-app-layout>
    <div class="main-content">
            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-12 mb-2">
                            <div class="card-title h4 fw-bold">Live Order View</div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">GDD - Orders</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">Live Order View</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

        <!-- End Page Header -->
         <div class="container my-5">
                <div class="card shadow-lg rounded-3">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Order Details</h4>
                        <span class="badge bg-light text-dark">Order ID: {{ $order['id'] }}</span>
                    </div>
            
                    <div class="card-body">
                        <div class="row g-4">
                            <!-- Basic Info -->
                            <div class="col-md-6">
                                <div class="card bg-light border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary mb-3">Basic Info</h5>
                                        <p><strong>Store Order ID:</strong> {{ $order['store_order_id'] }}</p>
                                        <p><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($order['order_date'])->format('d M Y h:i A') }}</p>
                                        <p><strong>Pickup Location:</strong> {{ $order['pickup_location'] }}</p>
                                        <p><strong>Order Amount:</strong> ₹{{ $order['order_amount'] }}</p>
                                        <p><strong>Payment Status:</strong> {{ ucfirst($order['payment_status']) }}</p>
                                        <p><strong>Payment Method:</strong> {{ ucfirst($order['payment_method']) }}</p>
                                        <p><strong>Shipping Method:</strong> {{ $order['shipping_method'] }}</p>
                                    </div>
                                </div>
                            </div>
            
                            <!-- Customer Info -->
                            <div class="col-md-6">
                                <div class="card bg-light border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary mb-3">Customer Info</h5>
                                        @php $customer = json_decode($order['customer_info'], true); @endphp
                                        <p><strong>Name:</strong> {{ $customer['billing_customer_name'] ?? '-' }}</p>
                                        <p><strong>Phone:</strong> {{ $customer['billing_phone'] ?? '-' }}</p>
                                        <p><strong>Email:</strong> {{ $customer['billing_email'] ?? '-' }}</p>
                                        <p><strong>Address:</strong> {{ $customer['billing_address'] ?? '-' }}</p>
                                        <p><strong>Address Line 2:</strong> {{ $customer['billing_address2'] ?? '-' }}</p>
                                        <p><strong>City:</strong> {{ $customer['billing_city'] ?? '-' }}</p>
                                        <p><strong>Pincode:</strong> {{ $customer['billing_pincode'] ?? '-' }}</p>
                                        <p><strong>State:</strong> {{ $customer['billing_state'] ?? '-' }}</p>
                                        <p><strong>Country:</strong> {{ $customer['billing_country'] ?? '-' }}</p>
                                        <p><strong>Latitude:</strong> {{ $customer['billing_latitude'] ?? '-' }}</p>
                                        <p><strong>Longitude:</strong> {{ $customer['billing_longitude'] ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
            
                            <!-- Shipping Charges -->
                            <div class="col-md-6">
                                <div class="card bg-light border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary mb-3">Shipping & Charges</h5>
                                        <p><strong>Delivery Charge:</strong> ₹{{ $order['delivery_charge'] }}</p>
                                        <p><strong>Tax Amount:</strong> ₹{{ $order['total_tax_amount'] }}</p>
                                        <p><strong>Store Discount:</strong> ₹{{ $order['store_discount_amount'] }}</p>
                                        <p><strong>Coupon Discount:</strong> ₹{{ $order['coupon_discount_amount'] }}</p>
                                        <p><strong>Additional Charge:</strong> ₹{{ $order['additional_charge'] }}</p>
                                    </div>
                                </div>
                            </div>
            
                            <!-- Parcel Dimensions -->
                            <div class="col-md-6">
                                <div class="card bg-light border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary mb-3">Parcel Dimensions</h5>
                                        <p><strong>Length:</strong> {{ $order['length'] }} cm</p>
                                        <p><strong>Breadth:</strong> {{ $order['breadth'] }} cm</p>
                                        <p><strong>Height:</strong> {{ $order['height'] }} cm</p>
                                        <p><strong>Weight:</strong> {{ $order['weight'] }} kg</p>
                                    </div>
                                </div>
                            </div>
                        </div>
            
                        <!-- Order Status -->
                        <div class="mt-4">
                            <div class="card bg-white border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="text-primary mb-3">Order Status</h5>
                                    <p><strong>Status:</strong> {{ ucfirst($order['order_status']) }}</p>
                                    <p><strong>Type:</strong> {{ ucfirst($order['order_type']) }}</p>
                                    <p><strong>Note:</strong> {{ $order['order_note'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
            
                        <!-- Ordered Items -->
                        @if($order->order_details && count($order->order_details))
                        <div class="mt-4">
                            <div class="card shadow-sm rounded">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Ordered Items</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Item Name</th>
                                                    <th>SKU</th>
                                                    <th>HSN</th>
                                                    <th>Units</th>
                                                    <th>Price (₹)</th>
                                                    <!--<th>Discount Type</th>-->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->order_details as $index => $item)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $item->name }}</td>
                                                        <td>{{ $item->sku }}</td>
                                                        <td>{{ $item->hsn }}</td>
                                                        <td>{{ $item->units }}</td>
                                                        <td>₹{{ number_format($item->price, 2) }}</td>
                                                        <!--<td>{{ ucfirst($item->discount_type) }}</td>-->
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

               <?php
            //   dd($order);
               ?>
           
    </div>
    
       
       

    
@section('script_js')
<script>
   
  
</script>

@endsection
</x-app-layout>
