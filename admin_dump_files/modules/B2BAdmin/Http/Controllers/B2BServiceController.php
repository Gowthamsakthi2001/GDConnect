<?php

namespace Modules\B2BAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\City\Entities\City;
use App\Models\BusinessSetting;
use Modules\B2B\Entities\B2BServiceRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\B2BAdminServiceRequestExport;
use Modules\MasterManagement\Entities\RepairTypeMaster;
use Illuminate\Http\Response;

class B2BServiceController extends Controller
{
    public function list(Request $request)
    {
        
            if ($request->ajax()) {
        try {
            $start  = $request->input('start', 0);
            $length = $request->input('length', 25);
            $search = $request->input('search.value');
            $from   = $request->input('from_date'); 
            $to     = $request->input('to_date');   
            $zone   = $request->input('zone_id');
            $city   = $request->input('city_id');
            
            $query = B2BServiceRequest::with([
                'assignment.VehicleRequest.city',
                'assignment.VehicleRequest.zone',
                'assignment.vehicle',
                'assignment.rider.customerlogin.customer_relation'
            ]);

           
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('assignment.VehicleRequest', function($qr) use ($search) {
                        $qr->where('req_id', 'like', "%{$search}%");
                    })
                    ->orWhereHas('assignment.vehicle', function($qr) use ($search) {
                        $qr->where('permanent_reg_number', 'like', "%{$search}%")
                           ->orWhere('chassis_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('assignment.rider', function($qr) use ($search) {
                        $qr->where('name', 'like', "%{$search}%")
                           ->orWhere('mobile_no', 'like', "%{$search}%");
                    })
                    ->orWhereHas('assignment.rider.customerlogin.customer_relation', function($qr) use ($search) {
                        $qr->where('trade_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('assignment.VehicleRequest.city', function($qr) use ($search) {
                        $qr->where('city_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('assignment.VehicleRequest.zone', function($qr) use ($search) {
                        $qr->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('ticket_id', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
                });
            }
            

            if (!empty($from)) {
                $query->whereDate('created_at', '>=', $from);
            }
            if (!empty($to)) {
                $query->whereDate('created_at', '<=', $to);
            }
            
            if ($city) {
                $query->whereHas('assignment.VehicleRequest', function ($q) use ($city) {
                    $q->where('city_id', $city); // column inside VehicleRequest table
                });
            }
            
            if ($zone) {
                $query->whereHas('assignment.VehicleRequest', function ($q) use ($zone) {
                    $q->where('zone_id', $zone); // column inside VehicleRequest table
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
                           
        

           $formattedData = $datas->map(function ($service, $index) use ($start) {
                $idEncode = encrypt($service->id);
            
                // Status column
                $statusColumn = '';
                if ($service->status === 'unassigned') {
                    $statusColumn = '
                        <span style="background-color:#EECACB; color:#A61D1D; border:#A61D1D 1px solid" 
                              class="px-2 py-1 rounded-pill">
                            Unassigned
                        </span>';
                } 
                 else if ($service->status === 'inprogress') {
                    $statusColumn = '
                      <span style="background-color:#D9CAED; color:#7E25EB; border:#7E25EB 1px solid" class="px-2 py-1 rounded-pill">
                         In Progress
                      </span>';
                }
              else if ($service->status === 'closed') {
                    $statusColumn = '
                    <span style="background-color:#CAEDCE; color:#005D27; border:#005D27 1px solid" class="px-2 py-1 rounded-pill">
                        Closed
                      </span>';
                }
                
                else {
                    $statusColumn = '
                        <span style="background-color:#EEE9CA; color:#947B14; border:#947B14 1px solid" 
                              class="px-2 py-1 rounded-pill">
                            Unknown
                        </span>';
                }
            
                // Action buttons
                $actionButtons = '
                    <div class="d-flex align-items-center gap-1">
                        <a href="'.route('b2b.admin.service_request.view', ['id' => $idEncode]).'" 
                           title="View Rider Details"
                           class="d-flex align-items-center justify-content-center border-0"
                           style="background-color:#CAEDCE; color:#155724; border-radius:8px; width:35px; height:35px;">
                            <i class="bi bi-eye fs-5"></i>
                        </a>
                    </div>
                ';
            
                return [
                    '<div class="form-check">
                                    <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" 
                                           name="is_select[]" type="checkbox" value="'.$service->id.'">
                                </div>',
            
                    // Request Id
                    e($service->assignment->VehicleRequest->req_id ?? ''),
                    
                    e($service->ticket_id ?? ''),
                    
                    // Vehicle No
                    e($service->assignment->vehicle->permanent_reg_number ?? ''),
            
                    // Chassis No
                    e($service->assignment->vehicle->chassis_number ?? ''),
            
                    // Rider Name
                    e($service->assignment->rider->name ?? ''),
            
                    // Contact Details
                    e($service->assignment->rider->mobile_no ?? ''),
            
                    // Client
                    e($service->assignment->rider->customerlogin->customer_relation->trade_name ?? ''),
            
                    // City
                    e($service->assignment->VehicleRequest->city->city_name ?? ''),
            
                    // Zone
                    e($service->assignment->VehicleRequest->zone->name ?? ''),
            
                    // Created Date and Time
                    $service->created_at ? $service->created_at->format('d M Y h:i A') : '',
            
                    // Updated Date and Time
                    $service->updated_at ? $service->updated_at->format('d M Y h:i A') : '',
            
                    // Created By (Type - first letter capital)
                    ucfirst($service->type ?? ''),
            
                    // Status
                    $statusColumn,
            
                    // Action Buttons
                    $actionButtons
                ];
            });
            
            return response()->json([
                'draw'            => intval($request->input('draw')),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data'            => $formattedData
            ]);

        } catch (\Exception $e) {
            \Log::error('Rider List Error: '.$e->getMessage());

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    $cities = City::where('status',1)->get();
    
        return view('b2badmin::service.list' ,compact('cities'));
    }
    
    
    public function view(Request $request , $id)
    {
        $service_id = decrypt($id);
    
        
        
        $data = B2BServiceRequest::where('id' ,$service_id)->first();
        
        
        $repair_types = RepairTypeMaster::where('status',1)->get();
        $apiKey = BusinessSetting::where('key_name', 'google_map_api_key')->value('value');

        return view('b2badmin::service.view' , compact('apiKey' ,'data' , 'repair_types'));
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
            new B2BAdminServiceRequestExport($from_date, $to_date, $selectedIds, $fields,$city,$zone),
            'service-request-list-' . date('d-m-Y') . '.xlsx'
        );
    }
        



}
