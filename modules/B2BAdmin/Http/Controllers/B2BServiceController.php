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
use Illuminate\Support\Facades\Auth;
use Modules\Zones\Entities\Zones;
use Illuminate\Http\Response;
use Modules\VehicleManagement\Entities\VehicleType; //updated by Mugesh.B
use Modules\AssetMaster\Entities\VehicleModelMaster; //updated by Mugesh.B
use Modules\MasterManagement\Entities\EvTblAccountabilityType; // updated by logesh
use Modules\MasterManagement\Entities\CustomerMaster; //updated by logesh

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
            $zone_id   = $request->input('zone_id',[]);
            $city_id   = $request->input('city_id',[]);
            $status   = $request->input('status',[]);
            $date_filter   = $request->input('date_filter',[]);
            $vehicle_type   = $request->input('vehicle_type',[]);
            $vehicle_model   = $request->input('vehicle_model',[]);
            $vehicle_make   = $request->input('vehicle_make',[]);
            $customer_id   = $request->input('customer_id',[]);
            $accountability_type   = $request->input('accountability_type',[]);
             

            $query = B2BServiceRequest::with([
                'assignment.VehicleRequest.city',
                'assignment.VehicleRequest.zone',
                'assignment.vehicle',
                'assignment.vehicle.quality_check',
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
                    ->orWhereHas('assignment.vehicle.quality_check.vehicle_model_relation', function($qr) use ($search) {
                        $qr->where('vehicle_model', 'like', "%{$search}%")
                           ->orWhere('make', 'like', "%{$search}%");
                    })
                    ->orWhereHas('assignment.vehicle.quality_check.vehicle_type_relation', function($qr) use ($search) {
                        $qr->where('name', 'like', "%{$search}%");
                         
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
                     ->orWhereHas('assignment.VehicleRequest.accountAbilityRelation', function($qr) use ($search) {
                        $qr->where('name', 'like', "%{$search}%");
                    })
                    
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('ticket_id', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
                });
            }
            
            
            if (!empty($date_filter)) {
                switch ($date_filter) {
            
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
            
                    case 'week':
                        $query->whereBetween('created_at', [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]);
                        break;
                    
                    case 'last_15_days':
                        $query->whereMonth('created_at', now()->subDays(14)->startOfDay())
                              ->whereYear('created_at', now()->endOfDay());
                        break;
            
                    case 'month':
                        $query->whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year);
                        break;
            
                    case 'year':
                        $query->whereYear('created_at', now()->year);
                        break;
            
                }
            }

            if (!empty($from)) {
                $query->whereDate('created_at', '>=', $from);
            }
            if (!empty($to)) {
                $query->whereDate('created_at', '<=', $to);
            }
            
            if (!empty($vehicle_type) && !in_array('all',$vehicle_type)) {
                $query->whereHas('assignment.vehicle.quality_check', function ($q) use ($vehicle_type) {
                    $q->whereIn('vehicle_type', $vehicle_type); 
                });
            }
            
            if (!empty($vehicle_model) && !in_array('all',$vehicle_model)) {
                $query->whereHas('assignment.vehicle.quality_check', function ($q) use ($vehicle_model) {
                    $q->whereIn('vehicle_model', $vehicle_model); // column inside VehicleRequest table
                });
            }
            
            if (!empty($vehicle_make) && !in_array('all',$vehicle_make)) {
                $query->whereHas('assignment.vehicle.quality_check', function ($q) use ($vehicle_make) {
                    $q->whereIn('make', $vehicle_make); // column inside VehicleRequest table
                });
            }
            
            if (!empty($city_id) && !in_array('all',$city_id)) {
                $query->whereHas('assignment.VehicleRequest', function ($q) use ($city_id) {
                    $q->whereIn('city_id', $city_id); // column inside VehicleRequest table
                });
            }
            
            if (!empty($zone_id) && !in_array('all',$zone_id)) {
                $query->whereHas('assignment.VehicleRequest', function ($q) use ($zone_id) {
                    $q->whereIn('zone_id', $zone_id); // column inside VehicleRequest table
                });
            }
            
            if (!empty($status) && !in_array('all',$status)) {
                $query->whereIn('status', $status);
            }
            //updated by logesh
            if (!empty($accountability_type) && !in_array('all',$accountability_type) ) {
                        $query->whereHas('assignment.VehicleRequest', function($zn) use ($request) {
                            $zn->whereIn('account_ability_type', $request->accountability_type);
                        });
                    }
            //updated by logesh
            if (!empty($customer_id) && !in_array('all',$customer_id)) {
                        $query->whereHas('assignment.rider.customerlogin.customer_relation', function($zn) use ($request) {
                            $zn->whereIn('id', $request->customer_id);
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
                
                if ($service->status === 'closed') {
                    $created   = \Carbon\Carbon::parse($service->created_at);
                    $completed = \Carbon\Carbon::parse($service->updated_at);
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
                    $created   = \Carbon\Carbon::parse($service->created_at);
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
                
                return [
                    '<div class="form-check">
                                    <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" 
                                           name="is_select[]" type="checkbox" value="'.$service->id.'">
                                </div>',
            
                    // Request Id
                    e($service->assignment->VehicleRequest->req_id ?? ''),
                    
                    e($service->ticket_id ?? ''),
                    
                    e($service->assignment->VehicleRequest->accountAbilityRelation->name ?? 'N/A'), //updated by logesh
                    // Vehicle No
                    e($service->assignment->vehicle->permanent_reg_number ?? ''),
            
                    // Chassis No
                    e($service->assignment->vehicle->chassis_number ?? ''),
                    
                    e($service->assignment->vehicle->quality_check->vehicle_type_relation->name ?? ''),
                    
                    e($service->assignment->vehicle->quality_check->vehicle_model_relation->vehicle_model ?? ''),
                    
                    e($service->assignment->vehicle->quality_check->vehicle_model_relation->make ?? ''),
            
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
                    
                    $aging,
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
    $accountability_types = EvTblAccountabilityType::where('status', 1) //updated by logesh
        ->orderBy('id', 'desc')
        ->get();
        
    $customers = CustomerMaster::select('id','trade_name')->where('status', 1) //updated by logesh
        ->orderBy('id', 'desc')
        ->get();
        
    $vehicle_types = VehicleType::where('is_active', 1) //updated by logesh
        ->orderBy('id', 'desc')
        ->get();
    $vehicle_models = VehicleModelMaster::where('status', 1) //updated by logesh
        ->orderBy('id', 'desc')
        ->get();
    
    $vehicle_makes = VehicleModelMaster::where('status', 1)->distinct()->pluck('make');   
    
        return view('b2badmin::service.list' ,compact('cities','accountability_types','customers' , 'vehicle_types' , 'vehicle_models','vehicle_makes'));
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

    $zone = (array) $request->input('zone_id', []);
    $city = (array) $request->input('city_id', []);
    $status = (array) $request->input('status', []);
    $accountability_type = (array) $request->input('accountability_type', []);
    $customer_id = (array) $request->input('customer_id', []);
    $selectedIds = (array) $request->input('selected_ids', []);

    $vehicle_type = (array) $request->input('vehicle_type', []);
    $vehicle_model = (array) $request->input('vehicle_model', []);
    $vehicle_make = (array) $request->input('vehicle_make', []);

    $date_filter = $request->input('date_filter');

    if (empty($fields)) {
        return back()->with('error', 'Please select at least one field to export.');
    }

    /* -------------------------------------
        FORMAT FIELD HEADERS
    -------------------------------------- */
    $formattedFields = [];

    foreach ($fields as $item) {
        $name = null;

        if (is_string($item) && trim($item) !== '') {
            $name = $item;
        } elseif (is_array($item)) {
            if (!empty($item['name'])) {
                $name = $item['name'];
            } elseif (!empty($item['field'])) {
                $name = $item['field'];
            } else {
                $first = reset($item);
                if (is_string($first) && trim($first) !== '') {
                    $name = $first;
                }
            }
        }

        if (!$name) continue;

        $clean = str_replace('_', ' ', $name);
        $clean = ucwords(strtolower($clean));

        $manual = [
            'Date Time' => 'Date & Time',
            'Qc Checklist' => 'QC Checklist',
            'Id' => 'ID',
        ];
        if (isset($manual[$clean])) {
            $clean = $manual[$clean];
        }

        $formattedFields[] = $clean;
    }

    $fieldsText = empty($formattedFields) ? 'ALL' : implode(', ', $formattedFields);


    /* -------------------------------------
        FETCH FRIENDLY NAMES FOR MULTIPLE VALUES
    -------------------------------------- */

    // Zone
    $zoneName = !empty($zone)
        ? implode(', ', Zones::whereIn('id', $zone)->pluck('name')->toArray())
        : null;

    // City
    $cityName = !empty($city)
        ? implode(', ', City::whereIn('id', $city)->pluck('city_name')->toArray())
        : null;

    // Accountability Type
    $accountabilityName = !empty($accountability_type)
        ? implode(', ', EvTblAccountabilityType::whereIn('id', $accountability_type)->pluck('name')->toArray())
        : null;

    // Customer
    $customerName = !empty($customer_id)
        ? implode(', ', CustomerMaster::whereIn('id', $customer_id)->pluck('trade_name')->toArray())
        : null;

    // Vehicle Type
    $vehicleTypeName = !empty($vehicle_type)
        ? implode(', ', VehicleType::whereIn('id', $vehicle_type)->pluck('name')->toArray())
        : null;

    // Vehicle Model
    $vehicleModelName = !empty($vehicle_model)
        ? implode(', ', VehicleModelMaster::whereIn('id', $vehicle_model)->pluck('vehicle_model')->toArray())
        : null;

    // Vehicle Make
    $vehicleMakeName = !empty($vehicle_make)
        ? implode(', ', VehicleModelMaster::whereIn('make', $vehicle_make)->pluck('make')->unique()->toArray())
        : null;


    /* -------------------------------------
        BUILD AUDIT LOG TEXT
    -------------------------------------- */
    $fileName = 'service-request-list-' . date('d-m-Y') . '.xlsx';
    $user = Auth::user();
    $roleName = optional(\Modules\Role\Entities\Role::find(optional($user)->role))->name ?? 'Unknown';

    $appliedFilters = [];

    if (!empty($status)) $appliedFilters[] = 'Status: ' . implode(', ', $status);
    if ($from_date) $appliedFilters[] = 'From: ' . $from_date;
    if ($to_date) $appliedFilters[] = 'To: ' . $to_date;
    if ($zoneName) $appliedFilters[] = 'Zone: ' . $zoneName;
    if ($cityName) $appliedFilters[] = 'City: ' . $cityName;
    if ($accountabilityName) $appliedFilters[] = 'Accountability Type: ' . $accountabilityName;
    if ($customerName) $appliedFilters[] = 'Customer: ' . $customerName;
    if ($vehicleTypeName) $appliedFilters[] = 'Vehicle Type: ' . $vehicleTypeName;
    if ($vehicleModelName) $appliedFilters[] = 'Vehicle Model: ' . $vehicleModelName;
    if ($vehicleMakeName) $appliedFilters[] = 'Vehicle Make: ' . $vehicleMakeName;
    if ($date_filter) $appliedFilters[] = 'Date Range: ' . $date_filter;

    $filtersText = empty($appliedFilters) ? 'No filters applied' : implode('; ', $appliedFilters);
    $selectedIdsText = empty($selectedIds) ? 'ALL' : implode(', ', $selectedIds);

    $longDesc = "User initiated Service Request export. File: {$fileName}. Selected Fields: {$fieldsText}. Filters: {$filtersText}. Selected IDs: {$selectedIdsText}.";

    audit_log_after_commit([
        'module_id'         => 5,
        'short_description' => 'B2B Admin Service Request Export Initiated',
        'long_description'  => $longDesc,
        'role'              => $roleName,
        'user_id'           => Auth::id(),
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => 'b2b_admin_service_request.export',
        'ip_address'        => $request->ip(),
        'user_device'       => $request->userAgent()
    ]);

    return Excel::download(
        new B2BAdminServiceRequestExport(
            $from_date, $to_date, $selectedIds, $fields, 
            $city, $zone, $status, $accountability_type,
            $customer_id, $vehicle_type, $vehicle_model, $vehicle_make,
            $date_filter
        ),
        $fileName
    );
}


    //  public function export(Request $request)
    // {
    
    //     $fields    = $request->input('fields', []);  
    //     $from_date = $request->input('from_date');
    //     $to_date   = $request->input('to_date');
    //     $zone = $request->input('zone_id')?? null;
    //     $city = $request->input('city_id')?? null;
    //     $status = $request->input('status')?? null;
    //      $accountability_type = $request->input('accountability_type')?? null;
    //     $customer_id = $request->input('customer_id')?? null;
    //      $selectedIds = $request->input('selected_ids', []);
         
    //       $vehicle_type = $request->input('vehicle_type')?? null;
    //      $vehicle_model = $request->input('vehicle_model')?? null;
    //      $date_filter = $request->input('date_filter')?? null;


    
    //     if (empty($fields)) {
    //         return back()->with('error', 'Please select at least one field to export.');
    //     }
        
    //     $formattedFields = [];
    // if (is_array($fields)) {
    //     foreach ($fields as $item) {
    //         $name = null;

    //         // plain string
    //         if (is_string($item) && trim($item) !== '') {
    //             $name = $item;
    //         }
    //         // associative array like ['name' => 'vehicle_type', 'value' => 'on']
    //         elseif (is_array($item)) {
    //             if (!empty($item['name']) && is_string($item['name'])) {
    //                 $name = $item['name'];
    //             } elseif (!empty($item['field']) && is_string($item['field'])) {
    //                 $name = $item['field'];
    //             } else {
    //                 // fallback: take first scalar value
    //                 $first = reset($item);
    //                 if (is_string($first) && trim($first) !== '') {
    //                     $name = $first;
    //                 }
    //             }
    //         }

    //         if (empty($name) || !is_string($name)) {
    //             continue;
    //         }

    //         $clean = str_replace('_', ' ', $name);
    //         $clean = ucwords(strtolower($clean));

    //         // manual friendly mappings
    //         $manual = [
    //             'Date Time' => 'Date & Time',
    //             'Qc Checklist' => 'QC Checklist',
    //             'Id' => 'ID',
    //         ];
    //         if (isset($manual[$clean])) {
    //             $clean = $manual[$clean];
    //         }

    //         $formattedFields[] = $clean;
    //     }
    // }

    // $fieldsText = empty($formattedFields) ? 'ALL' : implode(', ', $formattedFields);

    // // -----------------------
    // // Resolve friendly names for zone, city, accountability_type, customer
    // // -----------------------
    // $zoneName = $zone ? (optional(Zones::find($zone))->name ?? $zone) : null;
    // $cityName = $city ? (optional(City::find($city))->city_name ?? $city) : null;

    // // accountability_type lookup (adjust model name if different)
    // $accountability_name = null;
    // if (!is_null($accountability_type) && $accountability_type !== '') {
    //     $accountability_name = optional(EvTblAccountabilityType::find($accountability_type))->name ?? $accountability_type;
    // }

    // // customer name lookup (adjust model if your app uses a different model)
    // $customerName = null;
    // if (!is_null($customer_id) && $customer_id !== '') {
    //     $customerName = optional(CustomerMaster::find($customer_id))->name ?? $customer_id;
    // }
    
    // $vehicletypename = null;
    // if (!is_null($vehicle_type) && $vehicle_type !== '') {
    //     $vehicletypename = optional(VehicleType::find($vehicle_type))->name ?? $vehicle_type;
    // }
    
    //     $vehicle_modelname = null;
    // if (!is_null($vehicle_model) && $vehicle_model !== '') {
    //     $vehicle_modelname = optional(VehicleModelMaster::find($vehicle_model))->name ?? $vehicle_model;
    // }

    // // -----------------------
    // // Prepare audit log
    // // -----------------------
    // $fileName = 'service-request-list-' . date('d-m-Y') . '.xlsx';
    // $user = Auth::user();
    // $roleName = optional(\Modules\Role\Entities\Role::find(optional($user)->role))->name ?? 'Unknown';

    // $appliedFilters = [];
    // if (!is_null($status) && $status !== '') $appliedFilters[] = 'Status: ' . $status;
    // if (!is_null($from_date) && $from_date !== '') $appliedFilters[] = 'From: ' . $from_date;
    // if (!is_null($to_date) && $to_date !== '') $appliedFilters[] = 'To: ' . $to_date;
    // if (!is_null($zoneName) && $zoneName !== '') $appliedFilters[] = 'Zone: ' . $zoneName;
    // if (!is_null($cityName) && $cityName !== '') $appliedFilters[] = 'City: ' . $cityName;
    // if (!is_null($accountability_name) && $accountability_name !== '') $appliedFilters[] = 'Accountability Type: ' . $accountability_name;
    // if (!is_null($customerName) && $customerName !== '') $appliedFilters[] = 'Customer: ' . $customerName;
    // if (!is_null($vehicle_type) && $vehicletypename !== '') $appliedFilters[] = 'Vehicle Type : ' . $vehicletypename;
    // if (!is_null($vehicle_model) && $vehicle_modelname !== '') $appliedFilters[] = 'Vehicle Model: ' . $vehicle_modelname;
    // if (!is_null($date_filter) && $date_filter !== '') $appliedFilters[] = 'Date Range: ' . $date_filter;

    // $filtersText = empty($appliedFilters) ? 'No filters applied' : implode('; ', $appliedFilters);
    // $selectedIdsText = empty($selectedIds) ? 'ALL' : implode(', ', array_map('strval', $selectedIds));

    // $longDesc = "User initiated Service Request export. File: {$fileName}. Selected Fields: {$fieldsText}. Filters: {$filtersText}. Selected IDs: {$selectedIdsText}.";

    // audit_log_after_commit([
    //     'module_id'         => 5,
    //     'short_description' => 'B2B Admin Service Request Export Initiated',
    //     'long_description'  => $longDesc,
    //     'role'              => $roleName,
    //     'user_id'           => Auth::id(),
    //     'user_type'         => 'gdc_admin_dashboard',
    //     'dashboard_type'    => 'web',
    //     'page_name'         => 'b2b_admin_service_request.export',
    //     'ip_address'        => $request->ip(),
    //     'user_device'       => $request->userAgent()
    // ]);
    
    //     return Excel::download(
    //         new B2BAdminServiceRequestExport($from_date, $to_date, $selectedIds, $fields,$city,$zone,$status,$accountability_type,$customer_id , $vehicle_type , $vehicle_model, $date_filter),
    //         'service-request-list-' . date('d-m-Y') . '.xlsx'
    //     );
    // }
        



}
