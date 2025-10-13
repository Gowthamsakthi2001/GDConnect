<x-app-layout>
    <div class="main-content">
            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-12 mb-2">
                            <div class="card-title h4 fw-bold">Live Orders</div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">GDD - Orders</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">Live Orders</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

        <!-- End Page Header -->

        <div class="table-responsive">
                    <table class="table custom-table text-center" style="width: 100%;">
                          <thead class="bg-success rounded">
                            <tr>
                              <th scope="col" class="text-white">Order ID</th>
                              <th scope="col" class="text-white">Store Order ID</th>
                              <th scope="col" class="text-white">Order Date & Time</th>
                              <th scope="col" class="text-white">Order Amount</th>
                              <th scope="col" class="text-white">Customer Name</th>
                              <th scope="col" class="text-white">Email ID</th>
                              <th scope="col" class="text-white">Phone</th>
                              <th scope="col" class="text-white">Order Status</th>
                              <th scope="col" class="text-white">Payment Status</th>
                              <th scope="col" class="text-white">Action</th>
                            </tr>
                          </thead>
                          
                        <tbody class="bg-white border border-white">
                           
                            @if(isset($orders))
                               @foreach($orders as $key => $order)
                                 <tr>
                                     <td>{{$order->id}}</td>
                                     <td>{{$order->store_order_id}}</td>
                                     <td>{{date('d M Y h:i:s',strtotime($order->order_date))}}</td>
                                     <?php
                                       $CustomerInfo = json_decode($order->customer_info);
                                     ?>
                                     <td>{{number_format($order->order_amount,2)}}</td>
                                     <td>{{$CustomerInfo->billing_customer_name ?? ''}}</td>
                                     <td>
                                         {{$CustomerInfo->billing_email ?? ''}}
                                     </td>
                                      <td>
                                         {{$CustomerInfo->billing_phone ?? ''}}
                                     </td>
                                     <td>
                                         {{ucfirst($order->order_status) ?? ''}}
                                     </td>
                                     <td>
                                         {{ucfirst($order->payment_status) ?? ''}}
                                     </td>
                                     <td>
                                         <a href="{{route('admin.report.live_order_view',$order->id)}}"
                                                class="me-1 icon-btn">
                                                
                                                <img src="{{asset('public/admin-assets/img/eye.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </a>
                                     </td>
                                 </tr>
                               @endforeach
                            @endif
                        </tbody>
                      
                        </table>
                </div>
           
    </div>
    
       
       

    
@section('script_js')
<script>
   
  
</script>

@endsection
</x-app-layout>
