<?php

namespace Modules\B2BAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\City\Entities\City;
use Modules\B2B\Entities\B2BRecoveryRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\B2BAdminRecoveryRequestExport;

class B2BRecoveryController extends Controller
{
            public function list(Request $request)
        {
            if ($request->ajax()) {
                try {
                    $start  = $request->input('start', 0);
                    $length = $request->input('length', 25);
                    $search = $request->input('search.value');
        
        
                
                    $query = B2BRecoveryRequest::with([
                        'rider',
                        'assignment',
                        'assignment.vehicle',
                        'assignment.VehicleRequest',
                        'assignment.VehicleRequest.city',
                        'assignment.VehicleRequest.zone',
                        'rider.customerlogin.customer_relation'
                    ]);
        
                    // Filter by status
                    if ($request->filled('status') && $request->status !== 'all') {
                        $query->where('status', $request->status);
                    }
        
                    // Filter by date range
                    if ($request->filled('from_date') && $request->filled('to_date')) {
                        $query->whereDate('created_at', '>=', $request->from_date)
                              ->whereDate('created_at', '<=', $request->to_date);
                    }
        
        
                    if ($request->filled('city_id')) {
                        $query->whereHas('assignment.VehicleRequest.city', function($ct) use ($request) {
                            $ct->where('id', $request->city_id);
                        });
                    }
                    
                    // Filter by zone_id
                    if ($request->filled('zone_id')) {
                        $query->whereHas('assignment.VehicleRequest.zone', function($zn) use ($request) {
                            $zn->where('id', $request->zone_id);
                        });
                    }
                    

                    // Search filters
                    if (!empty($search)) {
                        $query->where(function($q) use ($search) {
                            // Status, dates
                            $q->where('status', 'like', "%{$search}%")
                              ->orWhereDate('created_at', $search)
                              ->orWhereDate('updated_at', $search);
                    
                            // Vehicle Request (req_id)
                            $q->orWhereHas('assignment.VehicleRequest', function($vr) use ($search) {
                                $vr->where('req_id', 'like', "%{$search}%");
                            });
                    
                            // Vehicle details
                            $q->orWhereHas('assignment.vehicle', function($v) use ($search) {
                                $v->where('permanent_reg_number', 'like', "%{$search}%")
                                  ->orWhere('chassis_number', 'like', "%{$search}%");
                            });
                    
                            // Rider details
                            $q->orWhereHas('rider', function($r) use ($search) {
                                $r->where('name', 'like', "%{$search}%")
                                  ->orWhere('mobile_no', 'like', "%{$search}%");
                            });
                    
                            // Client details
                            $q->orWhereHas('rider.customerlogin.customer_relation', function($c) use ($search) {
                                $c->where('trade_name', 'like', "%{$search}%");
                            });
                            
                            $q->orWhereHas('assignment.VehicleRequest.city', function($ct) use ($search) {
                                $ct->where('city_name', 'like', "%{$search}%");
                            });
                    
                            // Zone
                            $q->orWhereHas('assignment.VehicleRequest.zone', function($zn) use ($search) {
                                $zn->where('name', 'like', "%{$search}%");
                            });
        
                        });
                    }
        
                    $totalRecords = $query->count();
                    if ($length == -1) {
                        $length = $totalRecords;
                    }
        
                    $datas = $query->orderBy('id', 'desc')
                                   ->skip($start)
                                   ->take($length)
                                   ->get();
        
                    $formattedData = $datas->map(function ($item) {
                        // Status display
                        $statusColumn = '';
                        if ($item->status === 'opened') {
                            $statusColumn = '
                                <span style="background-color:#CAEDCE; color:#155724;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-x-circle me-1"></i> Opened
                                </span>';
                        } elseif ($item->status === 'closed') {
                            $statusColumn = '
                                <span style="background-color:#EECACB; color:#721c24;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> Closed
                                </span>';
                        }
        
                        // Aging
                        if ($item->status === 'closed' && $item->closed_at) {
                            $aging = \Carbon\Carbon::parse($item->created_at)
                                        ->diffForHumans(\Carbon\Carbon::parse($item->closed_at), true);
                        } else {
                            $aging = \Carbon\Carbon::parse($item->created_at)
                                        ->diffForHumans(now(), true);
                        }
        
                        // NULL-safe values using data_get
                        $requestId  = data_get($item, 'assignment.VehicleRequest.req_id', 'N/A');
                        $regNumber  = data_get($item, 'assignment.vehicle.permanent_reg_number', '');
                        $chassis    = data_get($item, 'assignment.vehicle.chassis_number', '');
                        $riderName  = data_get($item, 'rider.name', '');
                        $riderPhone = data_get($item, 'rider.mobile_no', '');
                        $clientName = data_get($item, 'rider.customerlogin.customer_relation.trade_name', '');
                        $cityName = data_get($item, 'assignment.VehicleRequest.city.city_name', 'N/A');
                        $zoneName = data_get($item, 'assignment.VehicleRequest.zone.name', 'N/A');
        
                        $createdAt  = $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y, h:i A') : '';
                        $updatedAt  = $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('d M Y, h:i A') : '';
        
                        $idEncode = encrypt($item->id);
        
                        return [
                            '<div class="form-check">
                                <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" 
                                    name="is_select[]" type="checkbox" value="'.$item->id.'">
                            </div>',
                            e($requestId),
                            e($regNumber),
                            e($chassis),
                            e($riderName),
                            e($riderPhone),
                            e($clientName),
                            e($cityName),
                            e($zoneName),
                            $createdAt,
                            $updatedAt,
                            $aging,
                            $statusColumn,
                            '<a href="'.route('b2b.admin.recovery_request.view', $idEncode).'"
                                class="d-flex align-items-center justify-content-center border-0" title="View"
                                style="background-color:#CAEDCE;color:#155724;border-radius:8px;width:35px;height:31px;">
                                <i class="bi bi-eye fs-5"></i>
                            </a>'
                        ];
                    });
                    
                 
        
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $formattedData
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Return Request List Error: '.$e->getMessage().' on line '.$e->getLine());
        
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => 0,
                        'recordsFiltered' => 0,
                        'data' => [],
                        'error' => 'Something went wrong: '.$e->getMessage()
                    ], 500);
                }
            }
            
             $cities = City::where('status',1)->get();
        
            return view('b2badmin::recovery.list' , compact('cities'));
        }

    
    
    public function view(Request $request , $id)
    {
       $recovery_id = decrypt($id);
       
       $data = B2BRecoveryRequest::where('id', $recovery_id)
                ->first();
                
        
        return view('b2badmin::recovery.view' , compact('data'));
    }

     public function export(Request $request)
        {

            $fields    = $request->input('fields', []);  
            $from_date = $request->input('from_date');
            $to_date   = $request->input('to_date');
            $zone = $request->input('zone_id')?? null;
            $city = $request->input('city_id')?? null;
             $selectedIds = $request->input('selected_ids', []);
    
        
            if (empty($fields)) {
                return back()->with('error', 'Please select at least one field to export.');
            }
        
            return Excel::download(
                new B2BAdminRecoveryRequestExport($from_date, $to_date, $selectedIds, $fields,$city,$zone),
                'recovery-request-list-' . date('d-m-Y') . '.xlsx'
            );
        }
    

}
