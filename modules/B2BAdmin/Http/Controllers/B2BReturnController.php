<?php

namespace Modules\B2BAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\B2B\Entities\B2BReturnRequest;
use Modules\City\Entities\City;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\B2BAdminReturnRequestExport;
use Illuminate\Support\Facades\Auth;
use Modules\Zones\Entities\Zones;
use Modules\VehicleManagement\Entities\VehicleType; //updated by Mugesh.B
use Modules\AssetMaster\Entities\VehicleModelMaster; //updated by Mugesh.B
use Modules\MasterManagement\Entities\EvTblAccountabilityType; // updated by logesh
use Modules\MasterManagement\Entities\CustomerMaster; //updated by logesh

class B2BReturnController extends Controller
{
        public function list(Request $request)
        {
            if ($request->ajax()) {
                try {
                    
                    $start  = $request->input('start', 0);
                    $length = $request->input('length', 25);
                    $search = $request->input('search.value');
                    
                    $zone_id   = $request->input('zone_id',[]);
                    $city_id   = $request->input('city_id',[]);
                    $status   = $request->input('status',[]);
                    $date_filter   = $request->input('date_filter',[]);
                    $vehicle_type   = $request->input('vehicle_type',[]);
                    $vehicle_model   = $request->input('vehicle_model',[]);
                    $vehicle_make   = $request->input('vehicle_make',[]);
                    $customer_id   = $request->input('customer_id',[]);
                    $accountability_type   = $request->input('accountability_type',[]);
        
        
                
                    $query = B2BReturnRequest::with([
                        'rider',
                        'assignment',
                        'assignment.vehicle',
                        'assignment.vehicle.quality_check',
                        'assignment.VehicleRequest',
                        'assignment.VehicleRequest.city',
                        'assignment.VehicleRequest.zone',
                        'rider.customerlogin.customer_relation'
                    ]);

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
                    // Filter by date range
                    if ($request->filled('from_date') && $request->filled('to_date')) {
                        $query->whereDate('created_at', '>=', $request->from_date)
                              ->whereDate('created_at', '<=', $request->to_date);
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
                            $q->orWhereHas('assignment.vehicle.quality_check.vehicle_model_relation', function($qr) use ($search) {
                                $qr->where('vehicle_model', 'like', "%{$search}%")
                                   ->orWhere('make', 'like', "%{$search}%");
                            })
                            ->orWhereHas('assignment.vehicle.quality_check.vehicle_type_relation', function($qr) use ($search) {
                                $qr->where('name', 'like', "%{$search}%");
                                 
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
                            
                             $q->orWhereHas('assignment.VehicleRequest.accountAbilityRelation', function($qr) use ($search) {
                                $qr->where('name', 'like', "%{$search}%");
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
                        $vehicle_type    = data_get($item, 'assignment.vehicle.vehicle_type_relation.name', '');
                        $vehicle_model    = data_get($item, 'assignment.vehicle.vehicle_model_relation.vehicle_model', '');
                        $vehicle_make    = data_get($item, 'assignment.vehicle.vehicle_model_relation.make', '');
                        $riderName  = data_get($item, 'rider.name', '');
                        $riderPhone = data_get($item, 'rider.mobile_no', '');
                        $clientName = data_get($item, 'rider.customerlogin.customer_relation.trade_name', 'N/A');
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
                            e($item->assignment->VehicleRequest->accountAbilityRelation->name ?? 'N/A'), //updated by logesh
                            e($regNumber),
                            e($chassis),
                            e($vehicle_type),
                            e($vehicle_model),
                            e($vehicle_make),
                            e($riderName),
                            e($riderPhone),
                            e($clientName),
                            e($cityName),      // City
                             e($zoneName),      // Zone
                            $createdAt,
                            $updatedAt,
                            $aging,
                            $statusColumn,
                            '<a href="'.route('b2b.admin.return_request.view', $idEncode).'"
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
            
            return view('b2badmin::return.list' , compact('cities','accountability_types','customers' , 'vehicle_types' , 'vehicle_models','vehicle_makes'));
        }

    
    
    public function view(Request $request , $id)
    {
       $return_id = decrypt($id);
       
       $data = B2BReturnRequest::where('id', $return_id)
                ->first();
                
        
        return view('b2badmin::return.view' , compact('data'));
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
    $date_filter = $request->input('date_filter')?? null;

    if (empty($fields)) {
        return back()->with('error', 'Please select at least one field to export.');
    }
        
    $formattedFields = [];
    foreach ((array) $fields as $item) {
        $name = null;

        if (is_string($item) && trim($item) !== '') {
            $name = $item;
        } elseif (is_array($item)) {
            if (!empty($item['name'])) $name = $item['name'];
            elseif (!empty($item['field'])) $name = $item['field'];
            else {
                $fallback = reset($item);
                if (is_string($fallback)) $name = $fallback;
            }
        }

        if (!$name) continue;

        $clean = ucwords(str_replace('_', ' ', strtolower($name)));

        $manual = [
            'Id' => 'ID',
            'Date Time' => 'Date & Time',
        ];
        if (isset($manual[$clean])) {
            $clean = $manual[$clean];
        }

        $formattedFields[] = $clean;
    }

    $fieldsText = empty($formattedFields) ? 'ALL' : implode(', ', $formattedFields);

    // --------------------------------
    // Resolve names for filters (ARRAY SAFE)
    // --------------------------------

    // ZONE
    if (is_array($zone)) {
        $zoneName = Zones::whereIn('id', $zone)->pluck('name')->implode(', ');
    } elseif (!empty($zone)) {
        $zoneName = optional(Zones::find($zone))->name ?? $zone;
    } else {
        $zoneName = null;
    }

    // CITY
    if (is_array($city)) {
        $cityName = City::whereIn('id', $city)->pluck('city_name')->implode(', ');
    } elseif (!empty($city)) {
        $cityName = optional(City::find($city))->city_name ?? $city;
    } else {
        $cityName = null;
    }

    // ACCOUNTABILITY TYPE
    if (is_array($accountability_type)) {
        $accountabilityName = EvTblAccountabilityType::whereIn('id', $accountability_type)
                            ->pluck('name')->implode(', ');
    } elseif (!empty($accountability_type)) {
        $accountabilityName = optional(EvTblAccountabilityType::find($accountability_type))->name ?? $accountability_type;
    } else {
        $accountabilityName = null;
    }

    // CUSTOMER
    if (is_array($customer_id)) {
        $customerName = CustomerMaster::whereIn('id', $customer_id)
                       ->pluck('name')->implode(', ');
    } elseif (!empty($customer_id)) {
        $customerName = optional(CustomerMaster::find($customer_id))->name ?? $customer_id;
    } else {
        $customerName = null;
    }

    // VEHICLE TYPE
    if (is_array($vehicle_type)) {
        $vehicletypename = VehicleType::whereIn('id', $vehicle_type)->pluck('name')->implode(', ');
    } elseif (!is_null($vehicle_type) && $vehicle_type !== '') {
        $vehicletypename = optional(VehicleType::find($vehicle_type))->name ?? $vehicle_type;
    } else {
        $vehicletypename = null;
    }

    // VEHICLE MODEL
    if (is_array($vehicle_model)) {
        $vehicle_modelname = VehicleModelMaster::whereIn('id', $vehicle_model)->pluck('vehicle_model')->implode(', ');
    } elseif (!is_null($vehicle_model) && $vehicle_model !== '') {
        $vehicle_modelname = optional(VehicleModelMaster::find($vehicle_model))->name ?? $vehicle_model;
    } else {
        $vehicle_modelname = null;
    }
    
    // VEHICLE MAKE
    if (is_array($vehicle_make)) {
        $vehicle_makename = VehicleModelMaster::whereIn('make', $vehicle_make)->pluck('make')->implode(', ');
    } elseif (!is_null($vehicle_make) && $vehicle_make !== '') {
        $vehicle_makename = optional(VehicleModelMaster::find('make',$vehicle_make))->make ?? $vehicle_make;
    } else {
        $vehicle_makename = null;
    }

    // --------------------------------
    // Build applied filters text
    // --------------------------------
    $appliedFilters = [];
    if (!empty($from_date)) $appliedFilters[] = "From: {$from_date}";
    if (!empty($to_date)) $appliedFilters[] = "To: {$to_date}";
    if (!empty($status)) $appliedFilters[] = "Status: {$status}";
    if (!empty($zoneName)) $appliedFilters[] = "Zone: {$zoneName}";
    if (!empty($cityName)) $appliedFilters[] = "City: {$cityName}";
    if (!empty($accountabilityName)) $appliedFilters[] = "Accountability Type: {$accountabilityName}";
    if (!empty($customerName)) $appliedFilters[] = "Customer: {$customerName}";
    if (!is_null($vehicle_type) && $vehicletypename !== '') $appliedFilters[] = 'Vehicle Type : ' . $vehicletypename;
    if (!is_null($vehicle_model) && $vehicle_modelname !== '') $appliedFilters[] = 'Vehicle Model: ' . $vehicle_modelname;
    if (!is_null($vehicle_make) && $vehicle_makename !== '') $appliedFilters[] = 'Vehicle Make: ' . $vehicle_makename;
    if (!is_null($date_filter) && $date_filter !== '') $appliedFilters[] = 'Date Range: ' . $date_filter;

    $filtersText = empty($appliedFilters) ? 'No filters applied' : implode('; ', $appliedFilters);
    $selectedIdsText = empty($selectedIds) ? 'ALL' : implode(', ', array_map('strval', $selectedIds));

    // --------------------------------
    // Audit Log
    // --------------------------------
    $fileName = 'return-request-list-' . date('d-m-Y') . '.xlsx';
    $user = Auth::user();
    $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

    $longDesc = "B2B Admin Return Request export triggered. File: {$fileName}. "
              . "Selected Fields: {$fieldsText}. Filters: {$filtersText}. Selected IDs: {$selectedIdsText}.";

    audit_log_after_commit([
        'module_id'         => 5,
        'short_description' => 'B2B Admin Return Request Export Initiated',
        'long_description'  => $longDesc,
        'role'              => $roleName,
        'user_id'           => Auth::id(),
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => 'b2b_admin_return_request.export',
        'ip_address'        => $request->ip(),
        'user_device'       => $request->userAgent()
    ]);

    return Excel::download(
        new B2BAdminReturnRequestExport(
            $from_date,
            $to_date,
            $selectedIds,
            $fields,
            $city,
            $zone,
            $status,
            $accountability_type,
            $customer_id,
            $vehicle_type,
            $vehicle_model,
            $vehicle_make,
            $date_filter
        ),
        'return-request-list-' . date('d-m-Y') . '.xlsx'
    );
}


    // public function export(Request $request)
    //     {

    //         $fields    = $request->input('fields', []);  
    //         $from_date = $request->input('from_date');
    //         $to_date   = $request->input('to_date');
    //         $zone = $request->input('zone_id')?? null;
    //         $city = $request->input('city_id')?? null;
    //         $status = $request->input('status')?? null;
    //         $accountability_type = $request->input('accountability_type')?? null;
    //         $customer_id = $request->input('customer_id')?? null;
    //         $selectedIds = $request->input('selected_ids', []);
             
    //       $vehicle_type = $request->input('vehicle_type')?? null;
    //       $vehicle_model = $request->input('vehicle_model')?? null;
    //       $date_filter = $request->input('date_filter')?? null;
    
        
    //         if (empty($fields)) {
    //             return back()->with('error', 'Please select at least one field to export.');
    //         }
            
    //     $formattedFields = [];
    //     foreach ((array) $fields as $item) {
    //         $name = null;
    
    //         if (is_string($item) && trim($item) !== '') {
    //             $name = $item;
    //         } elseif (is_array($item)) {
    //             if (!empty($item['name'])) $name = $item['name'];
    //             elseif (!empty($item['field'])) $name = $item['field'];
    //             else {
    //                 $fallback = reset($item);
    //                 if (is_string($fallback)) $name = $fallback;
    //             }
    //         }
    
    //         if (!$name) continue;
    
    //         $clean = ucwords(str_replace('_', ' ', strtolower($name)));
    
    //         // Manual mapping if needed
    //         $manual = [
    //             'Id' => 'ID',
    //             'Date Time' => 'Date & Time',
    //         ];
    //         if (isset($manual[$clean])) {
    //             $clean = $manual[$clean];
    //         }
    
    //         $formattedFields[] = $clean;
    //     }

    // $fieldsText = empty($formattedFields) ? 'ALL' : implode(', ', $formattedFields);

    // // --------------------------------
    // // Resolve names for filters
    // // --------------------------------
    // $zoneName  = $zone ? (optional(Zones::find($zone))->name ?? $zone) : null;
    // $cityName  = $city ? (optional(City::find($city))->city_name ?? $city) : null;

    // $accountabilityName = null;
    // if (!empty($accountability_type)) {
    //     $accountabilityName = optional(EvTblAccountabilityType::find($accountability_type))->name ?? $accountability_type;
    // }

    // $customerName = null;
    // if (!empty($customer_id)) {
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
    
    
    // // --------------------------------
    // // Build applied filters text
    // // --------------------------------
    // $appliedFilters = [];
    // if (!empty($from_date)) $appliedFilters[] = "From: {$from_date}";
    // if (!empty($to_date)) $appliedFilters[] = "To: {$to_date}";
    // if (!empty($status)) $appliedFilters[] = "Status: {$status}";
    // if (!empty($zoneName)) $appliedFilters[] = "Zone: {$zoneName}";
    // if (!empty($cityName)) $appliedFilters[] = "City: {$cityName}";
    // if (!empty($accountabilityName)) $appliedFilters[] = "Accountability Type: {$accountabilityName}";
    // if (!empty($customerName)) $appliedFilters[] = "Customer: {$customerName}";
    //     if (!is_null($vehicle_type) && $vehicletypename !== '') $appliedFilters[] = 'Vehicle Type : ' . $vehicletypename;
    // if (!is_null($vehicle_model) && $vehicle_modelname !== '') $appliedFilters[] = 'Vehicle Model: ' . $vehicle_modelname;
    // if (!is_null($date_filter) && $date_filter !== '') $appliedFilters[] = 'Date Range: ' . $date_filter;

    // $filtersText = empty($appliedFilters) ? 'No filters applied' : implode('; ', $appliedFilters);
    // $selectedIdsText = empty($selectedIds) ? 'ALL' : implode(', ', array_map('strval', $selectedIds));

    // // --------------------------------
    // // Audit Log
    // // --------------------------------
    // $fileName = 'return-request-list-' . date('d-m-Y') . '.xlsx';
    // $user = Auth::user();
    // $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

    // $longDesc = "B2B Admin Return Request export triggered. File: {$fileName}. "
    //           . "Selected Fields: {$fieldsText}. Filters: {$filtersText}. Selected IDs: {$selectedIdsText}.";

    // audit_log_after_commit([
    //     'module_id'         => 5, // As requested
    //     'short_description' => 'B2B Admin Return Request Export Initiated',
    //     'long_description'  => $longDesc,
    //     'role'              => $roleName,
    //     'user_id'           => Auth::id(),
    //     'user_type'         => 'gdc_admin_dashboard',
    //     'dashboard_type'    => 'web',
    //     'page_name'         => 'b2b_admin_return_request.export',
    //     'ip_address'        => $request->ip(),
    //     'user_device'       => $request->userAgent()
    // ]);
        
    //         return Excel::download(
    //             new B2BAdminReturnRequestExport($from_date, $to_date, $selectedIds, $fields,$city,$zone,$status,$accountability_type,$customer_id , $vehicle_type , $vehicle_model, $date_filter),
    //             'return-request-list-' . date('d-m-Y') . '.xlsx'
    //         );
    //     }
        

}
