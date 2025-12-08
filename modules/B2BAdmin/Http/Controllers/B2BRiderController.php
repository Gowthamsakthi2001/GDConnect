<?php

namespace Modules\B2BAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\B2B\Entities\B2BRider;
use Modules\City\Entities\City;
use Modules\B2B\Entities\B2BVehicleRequests; 
use Modules\Zones\Entities\Zones; //updated by logesh
use Modules\VehicleManagement\Entities\VehicleType; 
use App\Exports\B2BRiderExport;
use App\Exports\B2BAdminRiderExport;
use Modules\MasterManagement\Entities\CustomerMaster; //updated by logesh
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class B2BRiderController extends Controller
{

    
public function list(Request $request)
{
    if ($request->ajax()) {
        try {
            
            
            $start  = $request->input('start', 0);
            $length = $request->input('length', 10);
            $search = $request->input('search.value');
            $from   = $request->input('from_date'); 
            $to     = $request->input('to_date');   
            $zone   = $request->input('zone_id', []);
            $city   = $request->input('city_id', []);
            $customer_id = $request->input('customer_id', []);
            $date_filter   = $request->input('date_filter');
            // $customer_id   = $request->input('customer_id');
            // ✅ Start query
            $query = B2BRider::with('customerLogin.customer_relation','zone','city');

            // Apply search filter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('mobile_no', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
                // ---------------------------------------
            // QUICK DATE FILTER
            // ---------------------------------------
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
        
                    case 'custom':
                        if ($request->filled('from_date') && $request->filled('to_date')) {
                            $query->whereDate('created_at', '>=', $request->from_date)
                                  ->whereDate('created_at', '<=', $request->to_date);
                        }
                        break;
                }
            }


            
            if (!empty($city) && !in_array('all', $city)) {
                // Zone: filter by city + zone
            $query->whereIn('createdby_city', $city);
            }
            
            if (!empty($customer_id) && !in_array('all', $customer_id)) {
                $query->whereHas('customerLogin.customer_relation', function ($q) use ($customer_id) {
                    $q->whereIn('id', $customer_id);
                });
            }
                    
            if (!empty($zone) && !in_array('all', $zone)) {
                // Zone: filter by city + zone
                $query->whereIn('assign_zone_id', $zone);
            }
            $totalRecords = $query->count();

            if ($length == -1) {
                $length = $totalRecords;
            }

            $datas = $query->orderBy('id', 'desc')
                           ->skip($start)
                           ->take($length)
                           ->get();

            $counter = $start;

            $formattedData = $datas->map(function ($rider) use (&$counter) {
                $idEncode = encrypt($rider->id);

                $profileImage = $rider->profile_image 
                    ? asset('b2b/profile_images/'.$rider->profile_image) 
                    : asset('b2b/img/default_profile_img.png');

                $actionButtons = '
                    <div class="d-flex align-items-center gap-1">
                        <a href="'.route('b2b.admin.rider.rider_view',  ['id' => $idEncode]).'" title="View Rider Details"
                            class="d-flex align-items-center justify-content-center border-0"
                            style="background-color:#CAEDCE; color:#155724; border-radius:8px; width:35px; height:35px;">
                            <i class="bi bi-eye fs-5"></i>
                        </a>
                    </div>
                ';

                return [
                    // S.No
                    ++$counter,

                    // Rider Profile Image
                    '<img src="'.$profileImage.'" 
                          alt="Rider Profile" 
                          class="rounded-circle shadow-sm border border-2" 
                          style="width:48px; height:48px; object-fit:cover; border-color:#dee2e6; padding:2px; transition:transform 0.2s ease-in-out;" 
                          onmouseover="this.style.transform=\'scale(1.1)\'" 
                          onmouseout="this.style.transform=\'scale(1)\'">',

                    // Rider Name
                    e($rider->name ?? ''),

                    // Contact No
                    e($rider->mobile_no ?? ''),

                    // Client 
                    e($rider->customerLogin->customer_relation->trade_name ?? 'N/A'),
                    
                    e($rider->city->city_name ?? ''),
                    
                    e($rider->zone->name ?? ''),
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
    
    $vehicle_types = VehicleType::where('is_active', 1)->get();
    
    $customers = CustomerMaster::select('id','trade_name')->where('status', 1) //updated by logesh
    ->orderBy('id', 'desc')
    ->get();

    return view('b2badmin::rider.list' , compact('vehicle_types','cities' , 'customers'));
}

    
    public function rider_view(Request $request,$id)
    {
       
       $decrypt_id = decrypt($id);
        
       $rider = B2BRider::where('id', $decrypt_id)->first();
        
        return view('b2badmin::rider.view', compact('rider'));
    }
    
    //  public function rider_export(Request $request)
    // {
        
    //     $fields    = $request->input('fields', []);  
    //     $from_date = $request->input('from_date');
    //     $to_date   = $request->input('to_date');
    //     $zone = $request->input('zone_id')?? null;
    //     $city = $request->input('city_id')?? null;
    //      $selectedIds = $request->input('selected_ids', []);

    
    //     if (empty($fields)) {
    //         return back()->with('error', 'Please select at least one field to export.');
    //     }
    
    //     return Excel::download(
    //         new B2BAdminRiderExport($from_date, $to_date, $selectedIds, $fields,$city,$zone),
    //         'rider_list-' . date('d-m-Y') . '.xlsx'
    //     );
    // }
    
public function rider_export(Request $request)
{
    $fields = $request->input('fields', []);
    $from_date = $request->input('from_date');
    $to_date = $request->input('to_date');
    $zone = $request->input('zone_id') ?? [];
    $city = $request->input('city_id') ?? [];
    $datefilter = $request->input('datefilter') ?? null;
    $customer = $request->input('customer') ?? [];
    $selectedIds = $request->input('selected_ids', []);

    if (empty($fields)) {
        return back()->with('error', 'Please select at least one field to export.');
    }

    // ---------------------------
    // FIELD FORMATTER
    // ---------------------------
    $formattedFields = [];
    if (is_array($fields)) {
        foreach ($fields as $item) {
            $name = null;

            if (is_string($item) && trim($item) !== '') {
                $name = $item;
            } elseif (is_array($item)) {
                if (!empty($item['name']) && is_string($item['name'])) {
                    $name = $item['name'];
                } elseif (!empty($item['field']) && is_string($item['field'])) {
                    $name = $item['field'];
                } else {
                    $first = reset($item);
                    if (is_string($first) && trim($first) !== '') {
                        $name = $first;
                    }
                }
            }

            if (empty($name) || !is_string($name)) continue;

            $clean = ucwords(strtolower(str_replace('_', ' ', $name)));

            $manual = [
                'Date Time' => 'Date & Time',
                'Id' => 'ID'
            ];
            if (isset($manual[$clean])) {
                $clean = $manual[$clean];
            }

            $formattedFields[] = $clean;
        }
    }

    $fieldsText = empty($formattedFields) ? 'ALL' : implode(', ', $formattedFields);

    // -----------------------------------------
    // ARRAY-SAFE FILTER NAME RESOLUTION
    // -----------------------------------------

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

    // CUSTOMER
    if (is_array($customer)) {
        $customerName = CustomerMaster::whereIn('id', $customer)->pluck('name')->implode(', ');
    } elseif (!empty($customer)) {
        $customerName = optional(CustomerMaster::find($customer))->name ?? $customer;
    } else {
        $customerName = null;
    }

    // -----------------------------------------
    // BUILD FILTER TEXT
    // -----------------------------------------
    $appliedFilters = [];

    if (!empty($from_date)) $appliedFilters[] = 'From: ' . $from_date;
    if (!empty($to_date)) $appliedFilters[] = 'To: ' . $to_date;
    if (!empty($zoneName)) $appliedFilters[] = 'Zone: ' . $zoneName;
    if (!empty($cityName)) $appliedFilters[] = 'City: ' . $cityName;
    if (!empty($datefilter)) $appliedFilters[] = 'Date Range: ' . $datefilter;
    if (!empty($customerName)) $appliedFilters[] = 'Customer: ' . $customerName;

    $filtersText = empty($appliedFilters) ? 'No filters applied' : implode('; ', $appliedFilters);

    $selectedIdsText = empty($selectedIds) ? 'ALL' : implode(', ', array_map('strval', $selectedIds));

    // -----------------------------------------
    // AUDIT LOG
    // -----------------------------------------
    $fileName = 'rider_list-' . date('d-m-Y') . '.xlsx';
    $user = Auth::user();
    $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

    $longDesc = "User initiated Rider export. File: {$fileName}. "
              . "Selected Fields: {$fieldsText}. Filters: {$filtersText}. Selected IDs: {$selectedIdsText}.";

    audit_log_after_commit([
        'module_id'         => 4,
        'short_description' => 'B2B Admin Rider Export Initiated',
        'long_description'  => $longDesc,
        'role'              => $roleName,
        'user_id'           => Auth::id(),
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => 'b2b_admin_rider.export',
        'ip_address'        => $request->ip(),
        'user_device'       => $request->userAgent()
    ]);

    // -----------------------------------------
    // ORIGINAL EXPORT (unchanged)
    // -----------------------------------------
    return Excel::download(
        new B2BAdminRiderExport(
            $from_date,
            $to_date,
            $selectedIds,
            $fields,
            $city,
            $zone,
            $datefilter,
            $customer
        ),
        $fileName
    );
}



}
