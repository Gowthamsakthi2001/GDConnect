<?php

namespace Modules\B2BAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\B2B\Entities\B2BRider;
use Modules\City\Entities\City;
use Modules\B2B\Entities\B2BVehicleRequests; 
use Modules\VehicleManagement\Entities\VehicleType; 
use App\Exports\B2BAdminRiderExport;
use Maatwebsite\Excel\Facades\Excel;

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
            $zone   = $request->input('zone_id');
            $city   = $request->input('city_id');
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

            if (!empty($from)) {
                $query->whereDate('created_at', '>=', $from);
            }
            if (!empty($to)) {
                $query->whereDate('created_at', '<=', $to);
            }
            
            if ($city) {
                // Zone: filter by city + zone
            $query->where('createdby_city', $city);
            }
                    
            if ($zone) {
                // Zone: filter by city + zone
                $query->where('assign_zone_id', $zone);
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

    return view('b2badmin::rider.list' , compact('vehicle_types','cities'));
}

    
    public function rider_view(Request $request,$id)
    {
       
       $decrypt_id = decrypt($id);
        
       $rider = B2BRider::where('id', $decrypt_id)->first();
        
        return view('b2badmin::rider.view', compact('rider'));
    }
    
     public function rider_export(Request $request)
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
            new B2BAdminRiderExport($from_date, $to_date, $selectedIds, $fields,$city,$zone),
            'rider_list-' . date('d-m-Y') . '.xlsx'
        );
    }

}
