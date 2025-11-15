<?php

namespace Modules\AssetMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

use Illuminate\Support\DB;  
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

use Modules\AssetMaster\Entities\AmsLocationMaster; 
use Modules\AssetMaster\Entities\AssetInsuranceDetails;
use Modules\AssetMaster\Entities\AssetMasterBattery;
use Modules\AssetMaster\Entities\AssetMasterCharger;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\AssetMaster\Entities\ManufacturerMaster;
use Modules\AssetMaster\Entities\ModalMasterVechile;
use Modules\AssetMaster\Entities\ModelMasterBattery;
use Modules\AssetMaster\Entities\ModelMasterCharger;
use Modules\AssetMaster\Entities\AssetStatus;//updated by Gowtham.s
use Modules\AssetMaster\Entities\PoTable;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\City\Entities\City;//updated by Mugesh.B
use App\Exports\LocationMasterExport;//updated by Mugesh.B
use App\Models\EVState;//updated by Mugesh.B

//dataTable
use Modules\AssetMaster\DataTables\ModalMasterVechileDataTable;
use Modules\AssetMaster\DataTables\ModalMasterBatteryDataTable;
use Modules\AssetMaster\DataTables\ModalMasterChargerDataTable;
use Modules\AssetMaster\DataTables\ManufactureMasterDataTable;
use Modules\AssetMaster\DataTables\PotableDataTable;
use Modules\AssetMaster\DataTables\AmsLocationMasterDataTable;
use Modules\AssetMaster\DataTables\AssetInsuranceDataTable;
use Modules\AssetMaster\DataTables\AssetMasterBatteryDataTable;
use Modules\AssetMaster\DataTables\AssetMasterChargerDataTable;
use Modules\AssetMaster\DataTables\AssetMasterVechileDataTables;
use Modules\AssetMaster\DataTables\AssetStatusDataTable;

use Modules\AssetMaster\Entities\LocationMaster; 
use Modules\AssetMaster\Entities\LocationMasterHub; 

class LocationMasterController extends Controller
{


    public function location_mater_list(Request $request)
    {
       $query = LocationMaster::with('state_relation');


    
        $status = $request->status ?? 'all';
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
    
        // Only apply filter if valid
        if (in_array($status, ['1', '0'])) {
            $query->where('status', $status);
        }
    
        if ($from_date) {
            $query->whereDate('created_at', '>=', $from_date);
        }
    
        if ($to_date) {
            $query->whereDate('created_at', '<=', $to_date);
        }
    
        $list = $query->orderBy('id', 'desc')->get();
        
        
    
        return view('assetmaster::location_master.location_master_list', compact('list', 'status', 'from_date', 'to_date'));
    }


    
     public function create_location_master(Request $request){
         
         $city = City::where('status' , 1)->get();
         $states = EVState::where('status',1)->get();
         
       
        return view('assetmaster::location_master.create_location_master' , compact('city' ,'states'));
    }


     public function update_location_master(Request $request){
       $location = LocationMaster::where('id',$request->id)->first();
       $city = City::where('status' , 1)->get();
       $states = EVState::where('status',1)->get();
       
       if(!$location){
           return back()->with('error','Location Master Not Found');
       }
       
        return view('assetmaster::location_master.update_location_master',compact('location' , 'city' ,'states'));
    }
    
    
      public function view_location_master(Request $request){
        $location = LocationMaster::where('id',$request->id)->first();
        $states = EVState::where('status',1)->get();
        $city = City::where('status' , 1)->get();
           if(!$location){
               return back()->with('error','Location Master Not Found');
           }
            
        return view('assetmaster::location_master.view_location_master',compact('location' ,'states' , 'city'));
    }
    
    
    
       
   public function export_location_master(Request $request)
    {
        
        $status = $request->status;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        
        $roleName = optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown';

        // ✅ Log Export Activity
        audit_log([
            'module_id'         => 4,
            'short_description' => 'Location Master Exported',
            'long_description'  => 'Location Master Data Export triggered with filters: Status = ' . ($status ?: 'All') .
                                    ', From Date = ' . ($from_date ?: '-') .
                                    ', To Date = ' . ($to_date ?: '-'),
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'location_master.export_location_master',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
          return Excel::download(new LocationMasterExport($status,$from_date,$to_date), 'location-master-' . date('d-m-Y') . '.xlsx');
       
    }
    
    public function store_location_master(Request $request){
        
        $roleName = optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown';
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:ev_tbl_location_master,name',
            'city' => 'required',
            'state'=>'required',
            'city_code'=>'required' ,
            'hub_name'=>'required|array'
        ]);
            if ($validator->fails()) {
        
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Location Master Create Failed (Validation)',
                'long_description'  => 'Validation errors: ' . implode(', ', $validator->errors()->all()),
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'location_master.store',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
        
        
        return response()->json([
            'success' => false,
            'errors'  => $validator->errors(),
        ], 422);
    }
        
        $location = LocationMaster::create($request->only('name', 'city', 'state','city_code'));
        
        
        $hubs = $request->hub_name;
        
        foreach ($hubs as $inx => $hub) {
            LocationMasterHub::create([
                'location_id' => $location->id,
                'hub_name' => $hub,
            ]);
        }
        
        $hubCount  = count($hubs);
    $hubSample = $hubCount ? implode(', ', array_slice($hubs, 0, 5)) . ($hubCount > 5 ? ' …' : '') : '-';

    audit_log_after_commit([
        'module_id'         => 4,
        'short_description' => 'Location Master Created',
        'long_description'  => sprintf(
            'Location "%s" created (City: %s, State: %s, City Code: %s). Hubs added: %d. Sample: %s',
            $location->name,
            $location->city,
            $location->state,
            $location->city_code,
            $hubCount,
            $hubSample
        ),
        'role'              => $roleName,
        'user_id'           => Auth::id(),
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => 'location_master.store',
        'ip_address'        => request()->ip(),
        'user_device'       => request()->userAgent()
    ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Location Master added successfully.'
        ]);

    }
    
    // public function edit_location_master(Request $request){ 
        
    //   dd($request->all());
       
    // }
    
    public function edit_location_master(Request $request)
    {
        $roleName = optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown';
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:ev_tbl_location_master,name,' . $request->location_id,
            'city' => 'required',
            'state' => 'required',
            'city_code'=>'required' ,
            'hub_name' => 'required|array',
        ]);
    
        if ($validator->fails()) {
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'Location Master Save Failed (Validation)',
                    'long_description'  => 'Validation errors: ' . implode(', ', $validator->errors()->all()),
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'location_master.edit',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                ]);
    
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }
    
        if ($request->location_id) {
            $location = LocationMaster::find($request->location_id);
            if (!$location) {
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'Location Master Save Failed (Not Found)',
                    'long_description'  => 'Location not found for ID: ' . $request->location_id,
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'location_master.edit',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Location not found.'
                ]);
            }
    
            $location->update($request->only('name', 'city', 'state' ,'city_code'));
        } else {
            $location = LocationMaster::create($request->only('name', 'city', 'state' ,'city_code'));
        }
    
        // Get input hubs
        $hubNames = $request->hub_name;
        $hubIds = $request->hub_id ?? [];
    
        $submittedHubIds = [];
    
        foreach ($hubNames as $index => $hubName) {
            $hubId = $hubIds[$index] ?? null;
    
            if ($hubId) {
                $existingHub = LocationMasterHub::find($hubId);
                if ($existingHub) {
                    $existingHub->update([
                        'hub_name' => $hubName,
                    ]);
                    $submittedHubIds[] = $hubId;
                }
            } else {
                $newHub = LocationMasterHub::create([
                    'location_id' => $location->id,
                    'hub_name' => $hubName,
                ]);
                $submittedHubIds[] = $newHub->id;
            }
        }
    
        // Delete removed hubs
        LocationMasterHub::where('location_id', $location->id)
            ->whereNotIn('id', $submittedHubIds)
            ->delete();
        
         $hubCount  = count($hubNames);
    $hubSample = $hubCount ? implode(', ', array_slice($hubNames, 0, 5)) . ($hubCount > 5 ? ' …' : '') : '-';
    $action    = $request->location_id ? 'Updated' : 'Created';

    audit_log_after_commit([
        'module_id'         => 4,
        'short_description' => "Location Master {$action}",
        'long_description'  => sprintf(
            'Location "%s" (City: %s, State: %s, City Code: %s) %s. Hubs: %d. Sample: %s',
            $location->name,
            $location->city,
            $location->state,
            $location->city_code,
            strtolower($action),
            $hubCount,
            $hubSample
        ),
        'role'              => $roleName,
        'user_id'           => Auth::id(),
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => 'location_master.edit',
        'ip_address'        => request()->ip(),
        'user_device'       => request()->userAgent()
    ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Location Master saved successfully.'
        ]);
    }
    
    
     
        public function update_status(Request $request)
        {
            $request->validate([
                'id' => 'required|integer',
                'status' => 'required'
            ]);
            
            $roleName = optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown';
              
            $updated = LocationMaster::where('id', $request->id)->first();
            $locationName = $updated->name;
            $oldStatus = $updated->status?'Active':'Inactive';
            $newStatus = $request->status ?'Active':'Inactive';
            $updated->update(['status' => $request->status]);
        
            if ($updated) {
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'Location Status Updated',
                    'long_description'  => 'Status of Location "' . $locationName . '" has been changed from ' . $oldStatus . ' to ' . $newStatus . '.',
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'location_master.update_status',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Status updated successfully.'
                ]);
            } else {
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'Location Status Update Failed (Not Found)',
                    'long_description'  => 'Location Master record not found for ID: ' . $request->id,
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'location_master.update_status',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                ]);
        
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update status.'
                ]);
            }
        }

    
}
