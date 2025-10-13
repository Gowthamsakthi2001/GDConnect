<?php

namespace Modules\B2BAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\B2B\Entities\B2BVehicleRequests; //updated by Mugesh.B
use Modules\B2B\Entities\B2BReportAccident;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\B2BAdminDeploymentRequestExport;
use App\Exports\B2BAdminDeployedAssetExport;
use Modules\City\Entities\City;
use Modules\VehicleManagement\Entities\VehicleType; //updated by Mugesh.B
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\AssetMaster\Entities\LocationMaster;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\AssetMaster\Entities\AssetVehicleInventory;
use Modules\MasterManagement\Entities\FinancingTypeMaster;//updated by Mugesh.B
use Modules\B2B\Entities\B2BServiceRequest;
use Modules\AssetMaster\Entities\VehicleModelMaster; //updated by Mugesh.B
use Modules\MasterManagement\Entities\AssetOwnershipMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\InsurerNameMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\InsuranceTypeMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\HypothecationMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\RegistrationTypeMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\TelemetricOEMMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\InventoryLocationMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\ColorMaster;//updated by Mugesh.B
use Modules\Leads\Entities\leads;
use Modules\LeadSource\Entities\LeadSource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\B2B\Entities\B2BVehicleAssignment;
use Modules\B2B\Entities\B2BVehicleAssignmentLog;//updated by Mugesh.B

class DeployedAssetController extends Controller
{
        public function list(Request $request)
    {
    if ($request->ajax()) {
        try {
           
            $start  = $request->input('start', 0);
            $length = $request->input('length', 25);
            $search = $request->input('search.value');

            $query = B2BVehicleAssignment::with(['vehicle', 'rider.customerlogin.customer_relation','VehicleRequest']);

            // 🔹 Filtering if needed (status, date, etc.)
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereDate('created_at', '>=', $request->from_date)
                      ->whereDate('created_at', '<=', $request->to_date);
            }
            
             if ($request->filled('city_id')) {
                        $query->whereHas('VehicleRequest.city', function($ct) use ($request) {
                            $ct->where('id', $request->city_id);
                        });
                    }
                    
                    // Filter by zone_id
                    if ($request->filled('zone_id')) {
                        $query->whereHas('VehicleRequest.zone', function($zn) use ($request) {
                            $zn->where('id', $request->zone_id);
                        });
                    }
                    
            // 🔹 Search across related fields
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('vehicle', function ($v) use ($search) {
                        $v->where('permanent_reg_number', 'like', "%{$search}%")
                          ->orWhere('chassis_number', 'like', "%{$search}%")
                          ->orWhere('vehicle_type', 'like', "%{$search}%")
                          ->orWhere('model', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('rider', function ($r) use ($search) {
                        $r->where('name', 'like', "%{$search}%")
                          ->orWhere('mobile_no', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('rider.customerlogin.customer_relation', function ($c) use ($search) {
                        $c->where('trade_name', 'like', "%{$search}%");
                    });
                });
            }
            
            

            $allIds = $query->pluck('asset_vehicle_id')->unique();
            $totalRecords = $allIds->count();


            if ($length == -1) {
                $length = $totalRecords;
            }

            $datas = $query->orderBy('id', 'desc')
                           ->skip($start)
                           ->take($length)
                           ->get();
            $uniqueDatas = $datas->unique('asset_vehicle_id')->values()->take($length);

            $formattedData = $uniqueDatas->map(function ($item) {
                $vehicle = $item->vehicle;
                $rider   = $item->rider;
                $vehicleRequest = $item->VehicleRequest;
                return [
                    '<div class="form-check">
                        <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" 
                               name="is_select[]" type="checkbox" value="'.$item->id.'">
                    </div>',
                    e($vehicle->permanent_reg_number ?? 'N/A'),
                    e($vehicle->chassis_number ?? 'N/A'),
                    e($vehicle->vehicle_type_relation->name ?? 'N/A'),
                    e($vehicle->vehicle_model_relation->vehicle_model ?? 'N/A'),
                    e($rider->name ?? 'N/A'),
                    e($rider->mobile_no ?? 'N/A'),
                    e($rider->customerlogin->customer_relation->trade_name ?? 'N/A'),
                    e($vehicleRequest->city->city_name ?? 'N/A'),
                    e($vehicleRequest->zone->name ?? 'N/A'),
                    $item->created_at ? \Carbon\Carbon::parse($item->assigned_at)->format('d M Y, h:i A') : 'N/A',
                    $item->VehicleRequest && $item->VehicleRequest->end_date
                        ? \Carbon\Carbon::parse(optional($item->VehicleRequest)->end_date)->format('d M Y')
                        : 'N/A',
                    '<a href="'.route('b2b.admin.deployed_asset.deployed_asset_view', encrypt($item->id)).'"
                        class="d-flex align-items-center justify-content-center border-0" title="View"
                        style="background-color:#CAEDE7;color:#0F5847;border-radius:8px;width:35px;height:31px;">
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
            \Log::error('Assigned Vehicle List Error: '.$e->getMessage());

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
    return view('b2badmin::deployed_asset.list', compact('cities'));
}
    
    
    public function deployed_asset_view(Request $request,$id)
    {
        $request_id = decrypt($id);
        $data = B2BVehicleAssignment::where('id',$request_id)->with(['vehicle', 'rider.customerlogin.customer_relation','VehicleRequest'])->orderBy('id', 'desc')->first();
        
        $current_status = AssetVehicleInventory::with('inventory_location')
            ->where('asset_vehicle_id', $data->vehicle->id)
            ->where('asset_vehicle_status', 'accepted')
            ->first()->inventory_location->name ?? null;
            
        
        
    
        
        return view('b2badmin::deployed_asset.view',compact('data' ,'current_status'));
    }

    public function deployment_list(Request $request)
    {
        if ($request->ajax()) {
            try {
            $start  = $request->input('start', 0);
            $length = $request->input('length', 25);
            $search = $request->input('search.value');


           
            $query = B2BVehicleRequests::with('rider','zone','city');
        

            if ($request->filled('status') && $request->status != 'all') {
                $query->where('status', $request->status);
            }

            
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereDate('created_at', '>=', $request->from_date)
                      ->whereDate('created_at', '<=', $request->to_date);
            }

            
            if ($request->filled('city_id')) {
                $query->where('city_id', $request->city_id);
            }

        
            if ($request->filled('zone_id')) {
                $query->where('zone_id', $request->zone_id);
            }
            
            
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    // Request ID, status, dates
                    $q->where('req_id', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%")
                      ->orWhereDate('created_at', $search)
                      ->orWhereDate('updated_at', $search);
            
                    // Rider fields
                    $q->orWhereHas('rider', function($r) use ($search) {
                        $r->where('name', 'like', "%{$search}%")
                          ->orWhere('mobile_no', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            
                    // Client
                    $q->orWhereHas('rider.customerlogin.customer_relation', function($c) use ($search) {
                        $c->where('trade_name', 'like', "%{$search}%");
                    });
            
                    // City
                    $q->orWhereHas('city', function($c) use ($search) {
                        $c->where('city_name', 'like', "%{$search}%");
                    });
            
                    // Zone
                    $q->orWhereHas('zone', function($z) use ($search) {
                        $z->where('name', 'like', "%{$search}%");
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
                $statusColumn = '';
                if ($item->status === 'pending') {
                    $statusColumn = '
                        <span style="background-color:#CAEDCE; color:#155724;" class="px-2 py-1 rounded-pill">
                            <i class="bi bi-x-circle me-1"></i> Opened
                        </span>';
                } elseif ($item->status === 'completed') {
                    $statusColumn = '
                        <span style="background-color:#EECACB; color:#721c24;" class="px-2 py-1 rounded-pill">
                            <i class="bi bi-check-circle me-1"></i> Closed
                        </span>';
                }


                if ($item->status === 'completed' && $item->completed_at) {
                    $created   = \Carbon\Carbon::parse($item->created_at);
                    $completed = \Carbon\Carbon::parse($item->completed_at);
                    $diffInDays = $created->diffInDays($completed);
                    $diffInHours = $created->diffInHours($completed);
                    $diffInMinutes = $created->diffInMinutes($completed);
                
                    if ($diffInDays > 0) {
                        $aging = $diffInDays . ' days';
                    } elseif ($diffInHours > 0) {
                        $aging = $diffInHours . ' hours';
                    } else {
                        $aging = $diffInMinutes . ' mins';
                    }
                } else {
                    $created   = \Carbon\Carbon::parse($item->created_at);
                    $now       = now();
                    $diffInDays = $created->diffInDays($now);
                    $diffInHours = $created->diffInHours($now);
                    $diffInMinutes = $created->diffInMinutes($now);
                
                    if ($diffInDays > 0) {
                        $aging = $diffInDays . ' days';
                    } elseif ($diffInHours > 0) {
                        $aging = $diffInHours . ' hours';
                    } else {
                        $aging = $diffInMinutes . ' mins';
                    }
                }

                
                
                $rider = $item->rider;
                $requestId = $item->req_id;
                $idEncode = encrypt($item->id); // for route link

                return [
                    '<div class="form-check">
                        <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox" value="'.$item->id.'">
                    </div>',
                    $requestId,
                    e($rider->name ?? ''),
                    e($rider->mobile_no ?? ''),
                    e($item->rider->customerlogin->customer_relation->trade_name ?? 'N/A'), 
                    e($item->city->city_name ?? ''),
                    e($item->zone->name ?? ''),
                    \Carbon\Carbon::parse($item->created_at)->format('d M Y, h:i A'),
                    \Carbon\Carbon::parse($item->completed_at)->format('d M Y, h:i A'),
                     $aging, 
                    $statusColumn,
                    '<a href="'.route('b2b.admin.deployment_request.deployment_view', $idEncode).'"
                        class="d-flex align-items-center justify-content-center border-0" title="view"
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
                \Log::error('Vehicle Request List Error: '.$e->getMessage());
    
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
        
        
        return view('b2badmin::deployed_asset.deployed_list' , compact('cities'));
    }
    
    
    public function deployment_view(Request $request ,$id)
    {
        $request_id = decrypt($id);
       
        $data = B2BVehicleRequests::where('id', $request_id)
                ->first();
                
        $vehicle_types = VehicleType::where('is_active', 1)->get();
        
    
        $locations = City::where('status',1)->get();
        $passed_chassis_numbers = AssetMasterVehicle::where('qc_status','pass')->get();
        $vehicle_models = VehicleModelMaster::where('status', 1)->get();
        
        $financing_types = FinancingTypeMaster::where('status',1)->get();
        $asset_ownerships = AssetOwnershipMaster::where('status',1)->get();
        $insurer_names = InsurerNameMaster::where('status',1)->get();
        $insurance_types = InsuranceTypeMaster::where('status',1)->get();
        $hypothecations = HypothecationMaster::where('status',1)->get();
        $registration_types = RegistrationTypeMaster::where('status',1)->get();
        $inventory_locations = InventoryLocationMaster::where('status',1)->get();
        $telematics = TelemetricOEMMaster::where('status',1)->get();
        $colors = ColorMaster::where('status',1)->get();
        
        $current_status = null;
        
        if ($data && $data->assignment && $data->assignment->vehicle) {
            $inventory = AssetVehicleInventory::with('inventory_location')
                ->where('asset_vehicle_id', $data->assignment->vehicle->id)
                ->where('asset_vehicle_status', 'accepted')
                ->first();
        
            if ($inventory) {
                $current_status = $inventory->transfer_status;
            }
        }

        
            
        return view('b2badmin::deployed_asset.deployed_view' , compact('data' , 'current_status' ,'vehicle_types','locations','passed_chassis_numbers' ,'financing_types' ,'asset_ownerships' ,'insurer_names' ,'insurance_types' ,'hypothecations' ,'registration_types' ,'inventory_locations', 'vehicle_models' ,'telematics' ,'colors'));
    }
    
    
    public function accident_view(Request $request , $id)
    {
        $accident_id = decrypt($id);
       
        $data = B2BReportAccident::with('rider','logs')->where('id', $accident_id)
                ->first();
            
        return view('b2badmin::deployed_asset.accident_view',compact('data','id'));
    }
    
         public function accident_list(Request $request, $id)
{
    if ($request->ajax()) {
        try {
            $start  = $request->input('start', 0);
            $length = $request->input('length', 25);
            $search = $request->input('search.value');

            // Base query
            $query = B2BReportAccident::where('assign_id', $id)->with([
                'rider',
                'assignment',
                'assignment.vehicle',
                'assignment.VehicleRequest',
                'assignment.VehicleRequest.city',
                'assignment.VehicleRequest.zone',
                'rider.customerlogin.customer_relation'
            ]);

            // Total count without filters
            $totalRecords = $query->count();

            // Apply filters
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereDate('created_at', '>=', $request->from_date)
                      ->whereDate('created_at', '<=', $request->to_date);
            }

            if ($request->filled('city_id')) {
                $query->whereHas('assignment.VehicleRequest.city', function ($ct) use ($request) {
                    $ct->where('id', $request->city_id);
                });
            }

            if ($request->filled('zone_id')) {
                $query->whereHas('assignment.VehicleRequest.zone', function ($zn) use ($request) {
                    $zn->where('id', $request->zone_id);
                });
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('status', 'like', "%{$search}%")
                      ->orWhereDate('created_at', $search)
                      ->orWhereDate('updated_at', $search);

                    $q->orWhereHas('assignment.VehicleRequest', function ($vr) use ($search) {
                        $vr->where('req_id', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('assignment.vehicle', function ($v) use ($search) {
                        $v->where('permanent_reg_number', 'like', "%{$search}%")
                          ->orWhere('chassis_number', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('rider', function ($r) use ($search) {
                        $r->where('name', 'like', "%{$search}%")
                          ->orWhere('mobile_no', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('rider.customerlogin.customer_relation', function ($c) use ($search) {
                        $c->where('trade_name', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('assignment.VehicleRequest.city', function ($ct) use ($search) {
                        $ct->where('city_name', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('assignment.VehicleRequest.zone', function ($zn) use ($search) {
                        $zn->where('name', 'like', "%{$search}%");
                    });
                });
            }

            // Count after filters
            $filteredRecords = $query->count();

            if ($length == -1) {
                $length = $filteredRecords;
            }

            // Pagination
            $datas = $query->orderBy('id', 'desc')
                           ->skip($start)
                           ->take($length)
                           ->get();

            // Format
            $formattedData = $datas->map(function ($item, $key) use ($start) {
                $statuses = [
                    'claim_initiated' => ['label' => 'Claim Initiated', 'colors' => ['#EDCACA', '#580F0F']],
                    'insurer_visit_confirmed' => ['label' => 'Insurer Visit Confirmed', 'colors' => ['#EDE0CA', '#58490F']],
                    'inspection_completed' => ['label' => 'Inspection Completed', 'colors' => ['#DEEDCA', '#56580F']],
                    'approval_pending' => ['label' => 'Approval Pending', 'colors' => ['#CAEDCE', '#1E580F']],
                    'repair_started' => ['label' => 'Repair Started', 'colors' => ['#CAEDE7', '#0F5847']],
                    'repair_completed' => ['label' => 'Repair Completed', 'colors' => ['#CAE7ED', '#0F4858']],
                    'invoice_submitted' => ['label' => 'Invoice Submitted', 'colors' => ['#CAD2ED', '#1A0F58']],
                    'payment_approved' => ['label' => 'Payment Approved', 'colors' => ['#EDCAE3', '#580F4B']],
                    'claim_closed' => ['label' => 'Claim Closed', 'colors' => ['#EDE9CA', '#584F0F']],
                ];

                $status = $item->status ?? 'N/A';
                $label  = $statuses[$status]['label'] ?? ucfirst(str_replace('_', ' ', $status));
                $colors = $statuses[$status]['colors'] ?? ['#ddd', '#333'];

                $statusColumn = '<span style="background-color:'.$colors[0].'; color:'.$colors[1].'; border:'.$colors[1].' 1px solid" class="px-2 py-1 rounded-pill">'
                                .e($label).'</span>';

                $requestId    = data_get($item, 'assignment.VehicleRequest.req_id', 'N/A');
                $description  = data_get($item, 'description', 'N/A');
                $accidentType = data_get($item, 'accident_type', 'N/A');
                $createdAt    = $item->created_at ? $item->created_at->format('d M Y, h:i A') : '';
                $idEncode     = encrypt($item->id);

                $actions = '
                    <div class="d-flex align-items-center gap-2">
                        <a title="View Ticket Details" href="'.route('b2b.admin.deployed_asset.accident_view', $idEncode).'"
                           class="d-flex align-items-center justify-content-center border-0"
                           style="background-color:#CAEDCE; color:#155724; border-radius:8px; width:40px; height:40px;">
                           <i class="bi bi-eye fs-5"></i>
                        </a>
                    </div>
                ';

                return [
                    $start + $key + 1,
                     e($requestId),
                    e($description),
                    e($accidentType),
                    e($item->assignment->VehicleRequest->city->city_name ?? ''),
                    e($item->assignment->VehicleRequest->zone->name ?? ''),
                    $createdAt,
                    $statusColumn,
                    $actions,
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            \Log::error('Accident List Error: '.$e->getMessage().' on line '.$e->getLine());

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Something went wrong: '.$e->getMessage()
            ], 500);
        }
    }

    $cities = City::where('status', 1)->get();
    return view('b2badmin::accident.list', compact('cities'));
}


      public function export_deploymet_request(Request $request)
    {
        
        $fields    = $request->input('fields', []);  
        $from_date = $request->input('from_date');
        $to_date   = $request->input('to_date');
        $zone = $request->input('zone')?? null;
        $status = $request->input('status')?? null;
        $city = $request->input('city')?? null;
         $selectedIds = $request->input('selected_ids', []);

    
        if (empty($fields)) {
            return back()->with('error', 'Please select at least one field to export.');
        }
    
        return Excel::download(
            new B2BAdminDeploymentRequestExport($from_date, $to_date, $selectedIds, $fields,$city,$zone ,$status),
            'Deployment-request-list-' . date('d-m-Y') . '.xlsx'
        );
    }
     
      public function export_deployed_list(Request $request)
    {
        
        $fields    = $request->input('fields', []);  
        $from_date = $request->input('from_date');
        $to_date   = $request->input('to_date');
        $zone = $request->input('zone')?? null;
        $status = $request->input('status')?? null;
        $city = $request->input('city')?? null;
         $selectedIds = $request->input('selected_ids', []);

    
        if (empty($fields)) {
            return back()->with('error', 'Please select at least one field to export.');
        }
    
        return Excel::download(
            new B2BAdminDeployedAssetExport($from_date, $to_date, $selectedIds, $fields,$city,$zone ,$status),
            'Deployed-asset-list-' . date('d-m-Y') . '.xlsx'
        );
    }  



    public function load_more_servicedata(Request $request)
    {
        $statuses = [
            'unassigned'  => 'Unassigned',
            'inprogress'  => 'Inprogress',
            'closed'      => 'Closed',
        ];
    
        $colors = ['#03a9f4', '#7cb342', '#f32f10'];
    
        $limit     = (int) ($request->limit  ?? 3);
        $offset    = (int) ($request->offset ?? 0);
        $statusReq = $request->status;       
        $assign_id = $request->assign_id;
        $append    = filter_var($request->append, FILTER_VALIDATE_BOOLEAN); 
        
        $fromDate = $request->from_date;
        $toDate   = $request->to_date;
    
    
        // Helper: render single item HTML
        $renderItem = function($val, $color) {
            $reqId = $val->assignment->VehicleRequest->req_id ?? '';
            $vehicleNo = $val->assignment->vehicle->permanent_reg_number ?? '';
            $createdAt = \Carbon\Carbon::parse($val->created_at)->format('d M, Y H:i A');
    
            return '<div class="kanban-items m-1" id="item' . $val->id . '" data-item_id="' . $val->id . '" draggable="true">
                <div class="card task-card bg-white m-2 task-body" style="border-top: 2px solid ' . $color . '">
                    <div class="card-body">
                        <p class="mb-0 small-para fw-medium" style="color:' . $color . ';"><span class="lead-heading">Request ID : </span>' . $reqId . '</p>
                        <p class="mb-0 small-para fw-medium phone-number"><span class="lead-heading">Vehicle No :</span> ' . $vehicleNo . '</p>
                        <p class="mb-0 small-para fw-medium"><span class="lead-heading">Created Date & Time :</span> ' . $createdAt . '</p>
                    </div>
                </div>
            </div>';
        };
    
        // If append (load more) and status provided -> return only new items for that status
        if ($append && $statusReq) {
            $items = B2BServiceRequest::with('assignment')
                        ->where('status', $statusReq)
                        ->where('assign_id', $assign_id)
                    ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                        $q->whereBetween('created_at', [
                            \Carbon\Carbon::parse($fromDate)->startOfDay(),
                            \Carbon\Carbon::parse($toDate)->endOfDay()
                        ]);
                    })
                        ->orderBy('id', 'desc')
                        ->skip($offset)
                        ->take($limit)
                        ->get();
    
            $itemsHtml = '';
            $colorIndex = array_search($statusReq, array_keys($statuses));
            // fallback color index by mapping statuses to colors consistently:
            $statusKeys = array_keys($statuses);
            $colorIndex = array_search($statusReq, $statusKeys);
            if ($colorIndex === false) $colorIndex = 0;
            $color = $colors[$colorIndex % count($colors)];
    
            foreach ($items as $val) {
                $itemsHtml .= $renderItem($val, $color);
            }
    
            // determine if more items remain
            $fetched = $items->count();
            $has_more = $fetched >= $limit;
    
            return response()->json([
                'items_html'  => $itemsHtml,           // only the new item cards
                'has_more'    => $has_more,
                'next_offset' => $offset + $fetched,
            ]);
        }
    
        // --- Initial load (or full refresh): render entire columns with header + initial items ---
        $html_data = '';
        $colorIndex = 0;
    
        foreach ($statuses as $key => $label) {
            // total count for header
            $count = B2BServiceRequest::where('status', $key)
                        ->where('assign_id', $assign_id)
                        ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                            $q->whereBetween('created_at', [
                                \Carbon\Carbon::parse($fromDate)->startOfDay(),
                                \Carbon\Carbon::parse($toDate)->endOfDay()
                            ]);
                        })
                        ->count();
    
            $list = B2BServiceRequest::with('assignment')
                        ->where('status', $key)
                        ->where('assign_id', $assign_id)
                     ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                            $q->whereBetween('created_at', [
                                \Carbon\Carbon::parse($fromDate)->startOfDay(),
                                \Carbon\Carbon::parse($toDate)->endOfDay()
                            ]);
                        })
                        ->orderBy('id', 'desc')
                        ->take($limit)
                        ->get();
    
            $color = $colors[$colorIndex % count($colors)];
    
            $html_data .= '<div class="col kanban-column card" id="' . $key . '">
                <p class="card-header" style="background-color: ' . $color . '">' . $label . ' - ' . $count . ' Services</p>
                <div class="card-body p-0">
                    <div class="kanban-cards kanban-cards-' . $key . '">';
    
            if ($list->isEmpty()) {
                $html_data .= '<div class="text-center mt-5 card-inside" id="no-lead-' . $key . '">
                        <h4><i class="bi bi-opencollective"></i></h4>
                        <h4>No Service Found</h4>
                    </div>';
            } else {
                foreach ($list as $val) {
                    $html_data .= $renderItem($val, $color);
                    $last_id = $val->id;
                }
    
                // include Lead More button with next offset
                $html_data .= '<div class="text-center card-inside" id="lead-more-' . $key . '">
                    <button class="btn btn-primary w-100 lead-more-btn" data-status="' . $key . '" data-offset="' . $limit . '">
                        <span class="spinner-border spinner-border-sm d-none"></span>
                        Lead More
                    </button>
                </div>';
            }
    
            $html_data .= '</div></div></div>';
    
            $colorIndex++;
        }
    
        return response()->json(['html_data' => $html_data]);
    }

    public function autoload_activity_logs(Request $request , $id){
        
    // Get all assignment IDs for the vehicle
    $assignmentIds = B2BVehicleAssignment::where('asset_vehicle_id', $id)
                        ->pluck('id'); // <-- only get the IDs

    // Get all logs for these assignments
    $logs = B2BVehicleAssignmentLog::whereIn('assignment_id', $assignmentIds)->get();
    
        
        return view('b2badmin::deployed_asset.activity_logs', compact('logs'))->render();
    }
    
}
