<?php

namespace Modules\B2BAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\City\Entities\City;
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
                $city   = $request->input('city_id');
                $zone   = $request->input('zone_id');
               
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

                if ($from) $query->whereDate('created_at', '>=', $from);
                if ($to)   $query->whereDate('created_at', '<=', $to);
                if ($city) $query->where('city_id', $city);
                if ($zone) $query->where('zone_id', $zone);

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
            new B2BAgentExport($from_date, $to_date, $selectedIds, $fields,$city,$zone),
            'agent_list-' . date('d-m-Y') . '.xlsx'
        );
    }
    
    public function updateStatus(Request $request)
    {
        $agent = B2BAgent::findOrFail($request->id);
        $agent->status = $request->status;
        $agent->save();
    
        return response()->json(['message' => 'Agent status updated successfully.']);
    }
}
