<?php

namespace Modules\Zones\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\EVState;
use Modules\Zones\Entities\Zones;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\City\Entities\City;

class ZonesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $states = EVState::where('status',1)->get();
        $cities = City::where('status',1)->get();
        
        return view('zones::index',compact('states','cities'));
    }
    
    public function list()
    {
        $zones = Zones::All();
        return view('zones::list',compact('zones'));
    }
    
    public function render_zone_list(Request $request)
    {
        if ($request->ajax()) {
            $query = Zones::with(['state','city'])
                ->where('delete_status', 0);
    
            $status    = $request->input('status');
            $state_id  = $request->input('state_id');
            $city_id   = $request->input('city_id');
            $timeline  = $request->input('timeline');
            $from_date = $request->input('from_date');
            $to_date   = $request->input('to_date');
            $search    = $request->input('search.value');
            $start     = $request->input('start', 0);
            $length    = $request->input('length', 15);
    
            if (!empty($status)) {
                $query->where('status', $status);
            }
    
            if (!empty($state_id)) {
                $query->where('state_id', $state_id);
            }
    
            if (!empty($city_id)) {
                $query->where('city_id', $city_id);
            }
    
            if (!empty($timeline)) {
                $now = now();
                switch ($timeline) {
                    case 'today':
                        $query->whereDate('created_at', $now->toDateString());
                        break;
                    case 'this_week':
                        $query->whereBetween('created_at', [
                            $now->startOfWeek(), $now->endOfWeek()
                        ]);
                        break;
                    case 'this_month':
                        $query->whereBetween('created_at', [
                            $now->startOfMonth(), $now->endOfMonth()
                        ]);
                        break;
                    case 'this_year':
                        $query->whereBetween('created_at', [
                            $now->startOfYear(), $now->endOfYear()
                        ]);
                        break;
                }
            } elseif (!empty($from_date) || !empty($to_date)) {
                if (!empty($from_date)) {
                    $query->where('created_at', '>=', Carbon::parse($from_date)->startOfDay());
                }
                if (!empty($to_date)) {
                    $query->where('created_at', '<=', Carbon::parse($to_date)->endOfDay());
                }
            }
    
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhereHas('state', function($q) use ($search) {
                          $q->where('state_name', 'like', "%$search%");
                      })
                      ->orWhereHas('city', function($q) use ($search) {
                          $q->where('city_name', 'like', "%$search%");
                      });
                });
            }
    
            $totalRecords = $query->count();
    
            if ($length == -1) {
                $length = $totalRecords;
            }
    
            $data = $query->orderBy('id', 'desc')
                ->skip($start)
                ->take($length)
                ->get();
    
            $formattedData = $data->map(function($item) {

                
                if($item->status == 1){
                    $colorClass = 'text-success';
                }else if($item->status == 0){
                    $colorClass = 'text-danger';
                }else{
                    $colorClass = 'text-warning';
                }

                if($item->status == 1){
                    $displayStatus = 'Active';
                }else if($item->status == 0){
                    $displayStatus = 'Inactive';
                }else{
                    $displayStatus = 'Pending';
                }
    
                return [
                    'checkbox' => '<div class="form-check"><input class="form-check-input sr_checkbox" style="width:25px; height:25px;" type="checkbox" value="'.$item->id.'"></div>',
                    'zoneName' => $item->name ?? 'N/A',
                    'stateName'=> $item->state->state_name ?? 'N/A',
                    'cityName' => $item->city->city_name ?? 'N/A',
                    'status'   => '<div class="d-flex align-items-center gap-2"><i class="bi bi-circle-fill '.$colorClass.'"></i><span>'.$displayStatus.'</span></div>',
                    'statusToggleAction' => '<div class="form-check form-switch d-flex justify-content-center align-items-center m-0 p-0">
                        <input 
                            class="form-check-input toggle-status" 
                            data-id="'. $item->id .'" 
                            data-url="'. route('admin.Green-Drive-Ev.zone.toggle-status', ['id' => $item->id]) .'" 
                            type="checkbox" 
                            role="switch" 
                            id="toggleSwitch'.$item->id.'" 
                            '.($item->status == 1 ? 'checked' : '').'>
                    </div>',

                    'ActionBtns' => '<div class="dropdown">
                                        <button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end text-center p-1">
                                            <li>
                                                <a href="'.route('admin.Green-Drive-Ev.zone.edit',['id'=>$item->id]).'" class="dropdown-item d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-eye me-2 fs-5"></i> View
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center justify-content-center" onclick="DeleteRecord(\''.$item->id.'\')">
                                                    <i class="bi bi-trash me-2"></i> Delete
                                                </a>
                                            </li>
                                        </ul>
                                    </div>',
                ];
            });
    
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $formattedData
            ]);
        }
    
        return response()->json([
            'draw' => 0,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => []
        ]);
    }

    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $states = EVState::where('status',1)->get();
        $cities = City::where('status',1)->get();
        return view('zones::create',compact('states','cities'));
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
       $request->validate([
            'state'         => 'required|exists:ev_tbl_states,id',
            'city'          => 'required|exists:ev_tbl_city,id',
            'zone_name'     => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $normalized = strtolower(preg_replace('/\s+/', '', $value)); 
        
                    $exists = DB::table('zones')
                        ->whereRaw("REPLACE(LOWER(name), ' ', '') = ?", [$normalized])
                        ->exists();
        
                    if ($exists) {
                        $fail('The zone name has already been taken.');
                    }
                },
            ],
            'search_address'=> 'required|string|max:255',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'zone'          => 'nullable|json',
        ]);

        // Decode the JSON string to get the coordinates
        $zoneCoordinates = json_decode($request->input('zone'), true);

        if($zoneCoordinates){
            if (!is_array($zoneCoordinates) || empty($zoneCoordinates)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid zone coordinates.'
                ]);
            }
        
            // Ensure the polygon is closed (first point == last point)
            if ($zoneCoordinates[0] !== end($zoneCoordinates)) {
                $zoneCoordinates[] = $zoneCoordinates[0];
            }
        
            // Convert coordinates to WKT POLYGON format
            $wkt = 'POLYGON((' . implode(',', array_map(function ($coord) {
                // ✅ Ensure correct keys exist
                if (!isset($coord['lng'], $coord['lat'])) {
                    throw new \Exception("Invalid coordinate format.");
                }
                return "{$coord['lng']} {$coord['lat']}";
            }, $zoneCoordinates)) . '))';
        }

    
        try {
            $record = DB::table('zones')->insert([
                'state_id'   => $request->state,
                'city_id'    => $request->city,
                'address'    => $request->search_address,
                'name'       => $request->zone_name,
                'lat'        => $request->latitude,
                'long'       => $request->longitude,
                'status'     => 1,
                'coordinates'=> $request->zone ? DB::raw("ST_GeomFromText('$wkt')") : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $data = Zones::with('state','city')->where('id',$record->id)->first();
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
            $statusText = $record->status == 1 ? 'Active' : 'Inactive';
            $stateName = $data->state->state_name ?? $request->state;
            $cityName = $data->city->city_name ?? $request->city;
            audit_log_after_commit([
                'module_id'         => 5,
                'short_description' => 'Zone Created',
                'long_description'  => "Zone '{$record->name}' created (ID: {$data->id}). City: {$cityName}. State: {$stateName}. Status: {$statusText}.",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'zone.store',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Zone saved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save zone: ' . $e->getMessage()
            ]);
        }
    }

    public function check_exist_zone(Request $request)
    {
        $normalized = strtolower(preg_replace('/\s+/', '', $request->zone_name));
    
        $query = DB::table('zones')
            ->whereRaw("REPLACE(LOWER(name), ' ', '') = ?", [$normalized]);
    
        // Only ignore current zone if zone_id is provided
        if ($request->zone_id) {
            $query->where('id', '!=', $request->zone_id);
        }
    
        $exists = $query->exists();
    
        return response()->json([
            'exists' => $exists,
            'message' => $exists
                ? 'The zone name has already been taken.'
                : 'Zone name is available.You can Proceed further'
        ]);
    }




    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('zones::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        
        $zone = DB::table('zones')->where('id',$id)->first();
        
        if(!$zone){
            return redirect()->back()->with('error', 'Zone not found');
        }
        
        
        $states = EVState::where('status',1)->get();
        $cities = City::where('state_id',$zone->state_id)->where('status',1)->get();
        
        
        // // Fetch the zone details by ID
        // $zones = DB::table('zones')
        //     ->select('id', 'name', DB::raw('ST_AsText(coordinates) as coordinates'))
        //     ->where('id', $id)
        //     ->first();
    
        // // Convert WKT string to array of coordinates
        // $formattedCoordinates = [];
        // if ($zones && $zones->coordinates) {
        //     // Example of WKT string: "POLYGON((lat1 lng1, lat2 lng2, ...))"
        //     $coordinatesString = str_replace(['POLYGON((', '))', 'MULTIPOINT(', ')'], '', $zones->coordinates);
        //     $points = explode(',', $coordinatesString);
    
        //     foreach ($points as $point) {
        //         $latLng = explode(' ', trim($point));
        //         if (count($latLng) === 2) {
        //             $formattedCoordinates[] = [
        //                 "lat" => (float)$latLng[1], // Latitude
        //                 "lng" => (float)$latLng[0]  // Longitude
        //             ];
        //         }
        //     }
        // }
    
        // // Convert formatted coordinates to JSON
        // $json = json_encode($formattedCoordinates, JSON_PRETTY_PRINT);
    
        // Pass the zone data to the edit view
        return view('zones::edit', compact('states', 'cities','zone'));
    }

    
    // Add a private function to parse the WKT
    private function parseWKT($wkt)
    {
        // If the WKT is for a polygon, you can use this approach
        preg_match('/\((.*?)\)/', $wkt, $matches);
        $coords = $matches[1] ?? '';  // Grab the coordinates from inside the parentheses
    
        $points = explode(',', $coords);  // Split the coordinates by comma
    
        // Clean up the coordinates and convert to an array of lat/lng
        $coordinates = [];
        foreach ($points as $point) {
            $point = trim($point);
            list($lat, $lng) = explode(' ', $point);
            $coordinates[] = ['lat' => (float) $lat, 'lng' => (float) $lng];
        }
    
        return $coordinates;
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
     
        $request->validate([
            'state'         => 'required|exists:ev_tbl_states,id',
            'city'          => 'required|exists:ev_tbl_city,id',
            'zone_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($id) {
                    $normalized = strtolower(preg_replace('/\s+/', '', $value)); 
            
                    $exists = DB::table('zones')
                        ->where('id', '!=', $id)
                        ->whereRaw("REPLACE(LOWER(name), ' ', '') = ?", [$normalized])
                        ->exists();
            
                    if ($exists) {
                        $fail('The zone name has already been taken.');
                    }
                },
            ],
            'search_address'=> 'required|string|max:255',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'zone'          => 'nullable|json',
        ]);
          
        // dd($request->all());

        // Decode the JSON string to get the coordinates
        $zoneCoordinates = json_decode($request->input('zone'), true);

        if($zoneCoordinates){
            if (!is_array($zoneCoordinates) || empty($zoneCoordinates)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid zone coordinates.'
                ]);
            }
        
            // Ensure the polygon is closed (first point == last point)
            if ($zoneCoordinates[0] !== end($zoneCoordinates)) {
                $zoneCoordinates[] = $zoneCoordinates[0];
            }
        
            // Convert coordinates to WKT POLYGON format
            $wkt = 'POLYGON((' . implode(',', array_map(function ($coord) {
                // ✅ Ensure correct keys exist
                if (!isset($coord['lng'], $coord['lat'])) {
                    throw new \Exception("Invalid coordinate format.");
                }
                return "{$coord['lng']} {$coord['lat']}";
            }, $zoneCoordinates)) . '))';
        }

    
        try {
            $old = Zones::with('state','city')->where('id', $id)->first();
            $data = [
                'state_id'   => $request->state,
                'city_id'    => $request->city,
                'address'    => $request->search_address,
                'name'       => $request->zone_name,
                'lat'        => $request->latitude,
                'long'       => $request->longitude,
                'status'     => 1,
                'coordinates'=> $request->zone ? DB::raw("ST_GeomFromText('$wkt')") : null,
                'updated_at' => now(),
            ];
            
            Zones::where('id',$id)->update($data);
            
            $record = Zones::with('state','city')->where('id', $id)->first();
            
            $oldName = $old->name ?? '-';
            $oldCity = $old->city->city_name ?? $old->city_id;
            $oldState = $old->state->state_name ?? $old->state_id;
            $oldStatus = (int) ($old->status ?? 0);
            $newStatus = (int) ($record->status ?? 0);
            $newCity = $record->city->city_name ?? $record->city_id;
            $newState = $record->state->state_name ?? $record->state_id;
            $oldStatusText = $oldStatus == 1 ? 'Active' : 'Inactive';
            $newStatusText = $newStatus == 1 ? 'Active' : 'Inactive';
    
            // Audit log
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    
            audit_log_after_commit([
                'module_id'         => 5,
                'short_description' => 'Zone Updated',
                'long_description'  => "Zone updated (ID: {$record->id}). Name: '{$oldName}' → '{$record->name}'; City ID: '{$oldCity}' → '{$record->city_id}'; Status: {$oldStatusText} → {$newStatusText}.",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'zone.update',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
        
            return response()->json([
                'success' => true,
                'message' => 'Zone Updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save zone: ' . $e->getMessage()
            ]);
        }
        
    }


    /**
     * Remove the specified resource from storage.
     */
     public function toggleStatus(Request $request, $id)
    {
        try {
            $zone = Zones::findOrFail($id);
            $oldStatus = (int) $zone->status;
            $zone->status = !$zone->status;
            $zone->save();
    
            $newStatus = (int) $zone->status;
            $oldText = $oldStatus === 1 ? 'Active' : 'Inactive';
            $newText = $newStatus === 1 ? 'Active' : 'Inactive';
    
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

            audit_log_after_commit([
                'module_id'         => 5,
                'short_description' => 'Zone Status Updated',
                'long_description'  => "Zone '{$zone->name}' (ID: {$zone->id}) status changed: {$oldText} → {$newText}.",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'zone.update_status',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
    
            return response()->json([
                'success' => true,
                'message' => "Zone status updated successfully ({$oldText} → {$newText}).",
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    
    public function destroy($id)
    {
        $zone = Zones::findOrFail($id);
        $oldName = $zone->name;
        Zones::destroy($id);
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

        audit_log_after_commit([
            'module_id'         => 5,
            'short_description' => 'Zone Deleted',
            'long_description'  => "Zone '{$oldName}' (ID: {$id}) was deleted.",
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'zone.delete',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
        return redirect()->back()->with('success', 'Zone deleted.');
    }
}
