<?php

namespace Modules\B2BAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\City\Entities\City;
use Modules\Zones\Entities\Zones;
use Modules\B2B\Entities\B2BAgent;
use Modules\B2B\Entities\B2BVehicleRequests; 
use Modules\VehicleManagement\Entities\VehicleType; 

use App\Exports\B2BAgentExport;
use Maatwebsite\Excel\Facades\Excel;

class B2BAgentController extends Controller
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
                $date_filter   = $request->input('date_filter');
               
                $query = B2BAgent::where('role',17)->withCount([
                            'deploymentRequests as deployment_request_count' => function ($q) {
                                $q->where('status', 'completed');
                            },
                            'returnRequests as return_request_count' => function ($q) {
                                $q->where('status', 'closed');
                            }
                        ]);

                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                          ->orWhere('phone', 'like', "%$search%")
                          ->orWhere('email', 'like', "%$search%");
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

                    case 'custom':
                        // handled below by from/to date
                        break;
                }
            }
                if ($from) $query->whereDate('created_at', '>=', $from);
                if ($to)   $query->whereDate('created_at', '<=', $to);
                
                
                
                if ($city) $query->whereIn('city_id', $city);
                if ($zone) $query->whereIn('zone_id', $zone);

                $totalRecords = $query->count();
                if ($length == -1) $length = $totalRecords;

                $agents = $query->orderBy('id', 'desc')
                                ->skip($start)
                                ->take($length)
                                ->get();

                $data = $agents->map(function($agent, $index) use ($start) {
                    $idEncode = encrypt($agent->id);
                    $profileImage = $agent->profile_photo_path 
                        ? asset('uploads/users/' . $agent->profile_photo_path) 
                        : asset('b2b/img/default_profile_img.png');

                    $action = '<div class="d-flex align-items-center gap-1">
                        <a href="'.route('b2b.admin.agent.agent_view', ['id'=>$idEncode]).'" title="View Agent Details"
                           class="d-flex align-items-center justify-content-center border-0"
                           style="background-color:#CAEDCE; color:#155724; border-radius:8px; width:35px; height:35px;">
                           <i class="bi bi-eye fs-5"></i>
                        </a>
                    </div>';

                    return [
                        '<input class="form-check-input sr_checkbox" type="checkbox" style="width:25px;height:25px;" value="'.$agent->id.'">',
                        $agent->emp_id ?? '-',
                        '<img src="'.$profileImage.'" class="rounded-circle" style="width:40px; height:40px; object-fit:cover;">',
                        $agent->name ?? '-',
                        $agent->phone ?? '-',
                        $agent->city->city_name ?? '-',
                        $agent->zone->name ?? '-',
                        $agent->created_at->format('d-m-Y') ?? '-',
                        $agent->updated_at->format('d-m-Y') ?? '-',
                        // $agent->last_login ?? '-',
                        $agent->deployment_request_count ?? 0,
                        $agent->return_request_count ?? 0,
                        '<div class="form-check form-switch">
                            <input class="form-check-input custom-switch" type="checkbox" data-id="'.$agent->id.'" '.(($agent->status=='Active') ? 'checked' : '').'>
                        </div>',
                        $action
                    ];
                });

                return response()->json([
                    'draw' => intval($request->input('draw')),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $totalRecords,
                    'data' => $data
                ]);
            } catch (\Exception $e) {
                \Log::error('Agent List Error: '.$e->getMessage());
                return response()->json([
                    'draw' => intval($request->input('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        $cities = City::where('status', 1)->get();

        return view('b2badmin::agent.list', compact('cities'));
    }

        public function agent_view(Request $request, $id)
        {
            $id = decrypt($id);
        
            $agent = B2BAgent::where('id', $id)
                ->withCount([
                    'deploymentRequests as deployment_request_count' => function ($q) {
                        $q->where('status', 'completed');
                    },
                    'returnRequests as return_request_count' => function ($q) {
                        $q->where('status', 'closed');
                    }
                ])
                ->firstOrFail();
        
            return view('b2badmin::agent.view', compact('agent'));
        }

    public function agent_export(Request $request)
    {
        $fields      = $request->input('fields', []);  
        $from_date   = $request->input('from_date');
        $to_date     = $request->input('to_date');
    
        // ****** UPDATED FOR MULTIPLE ********
        $zones       = (array) $request->input('zone_id', []);  // array
        $cities      = (array) $request->input('city_id', []);  // array
    
        // Remove "all" if present
        $zones   = array_filter($zones, fn($v) => $v !== "all");
        $cities  = array_filter($cities, fn($v) => $v !== "all");
    
        $datefilter  = $request->input('datefilter') ?? null;
        $selectedIds = $request->input('selected_ids', []);
    
        if (empty($fields)) {
            return back()->with('error', 'Please select at least one field to export.');
        }
    
        // ******** FIELD FORMATTER ********
        $formattedFields = [];
        if (is_array($fields)) {
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
    
                if (empty($name)) continue;
    
                $clean = ucwords(strtolower(str_replace('_', ' ', $name)));
    
                $manual = [
                    'Date Time'   => 'Date & Time',
                    'Qc Checklist'=> 'QC Checklist',
                    'Id'          => 'ID'
                ];
    
                $formattedFields[] = $manual[$clean] ?? $clean;
            }
        }
    
        $fieldsText = empty($formattedFields) ? 'ALL' : implode(', ', $formattedFields);
    
        // ******** MULTIPLE ZONE/CITY NAMES ********
        $zoneNames = [];
        $cityNames = [];
    
        if (!empty($zones)) {
            $zoneNames = Zones::whereIn('id', $zones)->pluck('name')->toArray();
        }
    
        if (!empty($cities)) {
            $cityNames = City::whereIn('id', $cities)->pluck('city_name')->toArray();
        }
    
        // ******** LOG PREPARATION ********
        $fileName = 'agent_list-' . date('d-m-Y') . '.csv';
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    
        $appliedFilters = [];
    
        if (!empty($from_date))  $appliedFilters[] = "From: $from_date";
        if (!empty($to_date))    $appliedFilters[] = "To: $to_date";
    
        if (!empty($zoneNames))  $appliedFilters[] = 'Zone: ' . implode(', ', $zoneNames);
        if (!empty($cityNames))  $appliedFilters[] = 'City: ' . implode(', ', $cityNames);
    
        if (!empty($datefilter)) $appliedFilters[] = "Date Range: $datefilter";
    
        $filtersText      = empty($appliedFilters) ? 'No filters applied' : implode('; ', $appliedFilters);
        $selectedIdsText  = empty($selectedIds) ? 'ALL' : implode(', ', $selectedIds);
    
        $longDesc = "User initiated B2B Agent export. File: {$fileName} | Selected Fields: {$fieldsText} | Filters: {$filtersText} | Selected IDs: {$selectedIdsText}.";
    
        audit_log_after_commit([
            'module_id'         => 5,
            'short_description' => 'B2B Admin Agent Export Initiated',
            'long_description'  => $longDesc,
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'agent.export',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
    
        return Excel::download(
            new B2BAgentExport(
                $from_date,
                $to_date,
                $selectedIds,
                $fields,
                $cities,    // pass array
                $zones,     // pass array
                $datefilter
            ),
            $fileName
        );
    }

    
    public function updateStatus(Request $request)
    {
        $agent = B2BAgent::findOrFail($request->id);
        $oldStatus = $agent->status;
        $newStatus = $request->status;
        $agent->status = $request->status;
        $agent->save();
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

        if ((string)$oldStatus !== (string)$newStatus) {
            
            $oldLabel =  (string)$oldStatus;
            $newLabel =  (string)$newStatus;

            $agentName = $agent->name ?? ($agent->first_name ?? 'Agent');

            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Agent Status Updated',
                'long_description'  => "Agent ({$agent->id} - {$agentName}) status changed: {$oldLabel} → {$newLabel}",
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'agent.update_status',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
        }
        return response()->json(['message' => 'Agent status updated successfully.']);
    }
}
