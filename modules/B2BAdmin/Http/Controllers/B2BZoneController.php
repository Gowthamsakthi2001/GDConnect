<?php

namespace Modules\B2BAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\MasterManagement\Entities\CustomerLogin;
use Modules\MasterManagement\Entities\CustomerMaster;
use App\Exports\B2BAdminZonesExport;
use Maatwebsite\Excel\Facades\Excel;
use Modules\City\Entities\City;
use Modules\Zones\Entities\Zones;
use Illuminate\Support\Facades\Auth;
class B2BZoneController extends Controller
{
    public function zone_list(Request $request)
    {
        
        
        if ($request->ajax()) {
            try {
            $start  = $request->input('start', 0);
            $length = $request->input('length', 25);
            $search = $request->input('search.value');
            
            $city_id   = $request->input('city_id', []);
            $customer_id = $request->input('customer', []);
            $status   = $request->input('status', []);
         
           $query = CustomerMaster::with('cities')
            ->withCount([
                'customerlogins as zone_logins_count' => function ($q) {
                    $q->where('type', 'zone');
                }
            ]);
                    
    
            if (!empty($status) && !in_array('all', $status)) {
                $query->whereIn('status', $status);
            }


            if (!empty($city_id) && !in_array('all', $city_id)) {
                $query->whereIn('city_id', $city_id);
            }

            if (!empty($customer_id) && !in_array('all', $customer_id)) {
                $query->whereIn('id', $customer_id);
            }
            
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
            
        $q->orWhere('trade_name', 'like', "%{$search}%");

        // City
        $q->orWhereHas('cities', function($c) use ($search) {
            $c->where('city_name', 'like', "%{$search}%");
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
                           
                           

            $formattedData = $datas->map(function ($item ,$index) {
                $statusColumn = '';
                if ($item->status === 1) {
                    $statusColumn = '
                        <span style="background-color:#CAEDCE; color:#155724;" class="px-2 py-1 rounded-pill">
                            Active
                        </span>';
                } elseif ($item->status === 0) {
                    $statusColumn = '
                        <span style="background-color:#EECACB; color:#721c24;" class="px-2 py-1 rounded-pill">
                            <i class="bi bi-check-circle me-1"></i> Inactive
                        </span>';
                }
                $idEncode = encrypt($item->id);

                return [
                    $index + 1,
                    e($item->trade_name ?? '-'),
                    e($item->cities->city_name ?? '-'),
                    $item->zone_logins_count ?? 0,   
                    $statusColumn,
                    '<a href="'.route('b2b.admin.zone.zone_view', $idEncode).'"
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
                \Log::error('Zone List Error: '.$e->getMessage());
    
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
        $customers = CustomerMaster::select('id','trade_name')->where('status', 1) //updated by logesh
        ->orderBy('id', 'desc')
        ->get();
        return view('b2badmin::zones.list' , compact('cities','customers'));
    }
    
    public function zone_view(Request $request , $id)
    {
        $customer_id = decrypt($id);
        
         $data = CustomerMaster::with('cities', 'customerlogins')
        ->withCount([
            'customerlogins as zone_logins_count' => function ($q) {
                $q->where('type', 'zone');
            }
        ])
        ->find($customer_id);
                    
        
         if ($request->ajax()) {
            try {
            $start  = $request->input('start', 0);
            $length = $request->input('length', 25);
            $search = $request->input('search.value');
           
            $query = CustomerLogin::with('customer_relation','city','zone');
                    
    
            
            $query->where('customer_id' ,$customer_id);
            $query->where('type' ,'zone');
            
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    // City name + State name
                    $q->orWhereHas('city', function($c) use ($search) {
                        $c->where('city_name', 'like', "%{$search}%")
                          ->orWhereHas('state', function($s) use ($search) {
                              $s->where('state_name', 'like', "%{$search}%");
                          });
                    });
            
                    // Zone name
                    $q->orWhereHas('zone', function($z) use ($search) {
                        $z->where('name', 'like', "%{$search}%");
                    });
            
            
                    // Agent names without relation (direct users table)
                    $q->orWhereExists(function($sub) use ($search) {
                        $sub->select(\DB::raw(1))
                            ->from('users')
                            ->whereColumn('users.zone_id', 'ev_tbl_customer_logins.zone_id')
                            ->where('users.login_type', 2)
                            ->where('users.name', 'like', "%{$search}%");
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
                           
                           
            

            $formattedData = $datas->map(function ($item ,$index) {
                
                $zone_id = $item->zone_id ?? '';
                   // Get all agent names for this zone
                $agent_names = User::where('login_type', 2)
                    ->where('zone_id', $zone_id)
                    ->pluck('name')     // only fetch the name column
                    ->implode(', ');    // join names with comma
                
                
                $statusColumn = '';
                if ($item->status === 1) {
                    $statusColumn = '
                        <span style="background-color:#CAEDCE; color:#155724;" class="px-2 py-1 rounded-pill">
                            Active
                        </span>';
                } elseif ($item->status === 0) {
                    $statusColumn = '
                        <span style="background-color:#EECACB; color:#721c24;" class="px-2 py-1 rounded-pill">
                            <i class="bi bi-check-circle me-1"></i> Inactive
                        </span>';
                }

                return [
                    e($item->city->state->state_name ?? '-'),
                    e($item->city->city_name ?? '-'),
                    $item->zone->name ?? '-',   
                    $statusColumn,
                    $agent_names ?: '-' 
                ];
            });


            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $formattedData
            ]);
            } catch (\Exception $e) {
                \Log::error('Zone List Error: '.$e->getMessage());
    
                return response()->json([
                    'draw' => intval($request->input('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        
        
        return view('b2badmin::zones.view' ,compact('id' , 'data'));
    }
    
    
public function export(Request $request)
{
    $fields      = $request->input('fields', []);  
    $cityIds     = $request->input('city', []);     // array
    $customerIds = $request->input('customer', []); // array
    $selectedIds = $request->input('selected_ids', []);
    $status = $request->input('status', []);

    if (empty($fields)) {
        return back()->with('error', 'Please select at least one field to export.');
    }

    $formattedFields = [];
    foreach ((array) $fields as $item) {
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

        $clean = ucwords(str_replace('_', ' ', strtolower($name)));

        $manual = ['Id' => 'ID'];
        $formattedFields[] = $manual[$clean] ?? $clean;
    }

    $fieldsText = empty($formattedFields) ? 'ALL' : implode(', ', $formattedFields);

    $cityNames = [];

    if (!empty($cityIds)) {
        $cityRecords = City::whereIn('id', (array)$cityIds)->get();
        foreach ($cityRecords as $c) {
            $cityNames[] = $c->city_name;
        }

        $notFound = array_diff($cityIds, $cityRecords->pluck('id')->toArray());
        foreach ($notFound as $nf) {
            $cityNames[] = $nf;
        }
    }

    $cityFilterText = empty($cityNames) ? null : implode(', ', $cityNames);

    $customerNames = [];

    if (!empty($customerIds)) {
        $customerModelExists = class_exists(\App\Models\Customer::class);

        if ($customerModelExists) {
            $customers = \App\Models\Customer::whereIn('id', (array)$customerIds)->get();

            foreach ($customers as $cus) {
                $customerNames[] = $cus->name ?? $cus->id;
            }

            $notFoundCustomers = array_diff($customerIds, $customers->pluck('id')->toArray());
            foreach ($notFoundCustomers as $nf) {
                $customerNames[] = $nf;
            }

        } else {
            $customerNames = (array)$customerIds;
        }
    }

    $customerFilterText = empty($customerNames) ? null : implode(', ', $customerNames);

    $filters = [];

    if ($cityFilterText)     $filters[] = "City: {$cityFilterText}";
    if ($customerFilterText) $filters[] = "Customer: {$customerFilterText}";

    $filtersText = empty($filters) ? 'No filters applied' : implode('; ', $filters);

    $selectedIdsText = empty($selectedIds)
        ? 'ALL'
        : implode(', ', array_map('strval', (array)$selectedIds));

    $fileName = 'zones-list-' . date('d-m-Y') . '.xlsx';

    $user = Auth::user();
    $roleName = optional(\Modules\Role\Entities\Role::find(optional($user)->role))->name ?? 'Unknown';

    $longDesc = "User initiated B2B Admin Zone export. File: {$fileName}. "
              . "Selected Fields: {$fieldsText}. "
              . "Filters: {$filtersText}. "
              . "Selected IDs: {$selectedIdsText}.";

    audit_log_after_commit([
        'module_id'         => 5,
        'short_description' => 'B2B Admin Zone Export Initiated',
        'long_description'  => $longDesc,
        'role'              => $roleName,
        'user_id'           => Auth::id(),
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => 'b2b_admin_zone.export',
        'ip_address'        => $request->ip(),
        'user_device'       => $request->userAgent()
    ]);

    return Excel::download(
        new B2BAdminZonesExport($fields, $cityIds, $status , $customerIds),
        $fileName
    );
}



}
