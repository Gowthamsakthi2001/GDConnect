<?php

namespace Modules\City\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\City\DataTables\CityDataTable;
use Modules\City\DataTables\AreaDataTable;
use Modules\City\Entities\City;
use Modules\City\Entities\Area;
use Illuminate\Support\Facades\Auth; 
class CityController extends Controller
{
   // Display form to create a new city
    public function index()
    {
        return view('city::create');
    }

    // Store a new city
    public function create(Request $request)
    {
        $request->validate([
            'city_name' => 'required|string|max:255',
            'short_code' => ['required', 'size:3', 'regex:/^[A-Z]{3}$/'],
            // 'pincode' => ['required', 'digits:6', 'regex:/^[1-9][0-9]{5}$/'],
            'status' => 'required|boolean',
        ]);

        // Check if the city already exists
        $existingCity = City::where('city_name', $request->city_name)->first();
    
        if ($existingCity) {
            // Return error message if the city already exists
            return redirect()->back()->withErrors(['city_name' => 'This city already exists.']);
        }
        
        $existingCitycode = City::where('short_code', $request->short_code)->first();
    
        if ($existingCitycode) {
            // Return error message if the city already exists
            return redirect()->back()->withErrors(['short_code' => 'This city short code already exists.']);
        }
    
        // If the city doesn't exist, create a new one
        $city = City::create($request->only('city_name', 'short_code','status'));
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $statusText = $city->status == 1 ? 'Active' : 'Inactive';

        audit_log_after_commit([
            'module_id'         => 3,
            'short_description' => 'City Created',
            'long_description'  => "City '{$city->city_name}' created (ID: {$city->id}). Code: {$city->short_code}. Status: {$statusText}.",
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'city_master.store',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
        
        return redirect()->route('admin.Green-Drive-Ev.City.list')->with('success', 'City created successfully.');
    }

    // List all cities
    public function list(CityDataTable $dataTable)
    {
        return $dataTable->render('city::list');
    }
    

    // Delete a specific city
    public function delete_city($id) // don't delete the city because this one based on work recruiters and etc.. added by Gowtham.s
    {
        $city = City::findOrFail($id);
        // $city->delete();
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    
        audit_log_after_commit([
            'module_id'         => 3,
            'short_description' => 'City Delete Attempted',
            'long_description'  => "Delete attempted for City '{$city->city_name}' (ID: {$city->id}) but deletion is skipped as this city is referenced elsewhere.",
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'city_master.delete',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
        return redirect()->route('admin.Green-Drive-Ev.City.list')->with('success', 'City deleted successfully.');
    }

    // Show form to edit a specific city
    public function edit_city($id)
    {
        $city = City::findOrFail($id);
        return view('city::edit', compact('city'));
    }

    // Update a specific city
    public function update(Request $request, $id)
    {
        $request->validate([
            'city_name' => 'required|string|max:255',
            'short_code' => ['required', 'size:3', 'regex:/^[A-Z]{3}$/'],
            // 'pincode' => ['required', 'digits:6', 'regex:/^[1-9][0-9]{5}$/'],
            'status' => 'required|boolean',
        ]);

        $city = City::findOrFail($id);
        $oldName = $city->city_name;
        $oldCode = $city->short_code;
        $oldStatus = (int) $city->status;
        $existingCitycode = City::where('short_code', $request->short_code)->where('id','!=',$city->id)->first();
    
        if ($existingCitycode) {
            // Return error message if the city already exists
            return redirect()->back()->withErrors(['short_code' => 'This city short code already exists.']);
        }
        
        $city->update($request->only('city_name', 'short_code','status'));
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $newStatus = (int) $city->status;
        $oldStatusText = $oldStatus == 1 ? 'Active' : 'Inactive';
        $newStatusText = $newStatus == 1 ? 'Active' : 'Inactive';
    
        audit_log_after_commit([
            'module_id'         => 3,
            'short_description' => 'City Updated',
            'long_description'  => "City updated (ID: {$city->id}). Name: '{$oldName}' → '{$city->city_name}'; Code: '{$oldCode}' → '{$city->short_code}'; Status: {$oldStatusText} → {$newStatusText}.",
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'city_master.update',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
        return redirect()->route('admin.Green-Drive-Ev.City.list')->with('success', 'City updated successfully.');
    }

    // Change status of a city
    public function change_status($id, $status)
    {
        $city = City::findOrFail($id);
        $oldStatus = (int) $city->status;
        $newStatus = (int) $status;

        $city->status = $status;
        $city->save();
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $oldText = $oldStatus == 1 ? 'Active' : 'Inactive';
        $newText = $newStatus == 1 ? 'Active' : 'Inactive';
    
        audit_log_after_commit([
            'module_id'         => 3,
            'short_description' => 'City Status Updated',
            'long_description'  => "City '{$city->city_name}' (ID: {$city->id}) status changed: {$oldText} → {$newText}.",
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'city_master.update_status',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
        
        return redirect()->route('admin.Green-Drive-Ev.City.list')->with('success', 'City status updated successfully.');
    }
    
    public function area_index()
    {
        $City = City::where('status',1)->get();
        return view('city::area.create',compact('City'));
    }
    
    public function area_list(AreaDataTable $dataTable)
    {
        return $dataTable->render('city::area.list');
    }
    
    public function area_change_status($id, $status)
    {
        $area = Area::findOrFail($id);
        $oldStatus = (int) $area->status;
        $newStatus = (int) $status;
        $area->status = $status;
        $area->save();
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $oldText = $oldStatus == 1 ? 'Active' : 'Inactive';
        $newText = $newStatus == 1 ? 'Active' : 'Inactive';
    
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Area Status Updated',
            'long_description'  => "Area '{$area->Area_name}' (ID: {$area->id}) status changed: {$oldText} → {$newText}.",
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'area_master.update_status',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
    
        return redirect()->route('admin.Green-Drive-Ev.Area.list')->with('success', 'Area status updated successfully.');
    }
    
    // Delete a specific city
    public function area_delete($id)
    {
        $area = Area::findOrFail($id);
        $oldName = $area->Area_name;
        $area->delete();
         $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Area Deleted',
            'long_description'  => "Area '{$oldName}' (ID: {$id}) was deleted.",
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'area_master.delete',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
        return redirect()->route('admin.Green-Drive-Ev.Area.list')->with('success', 'Area deleted successfully.');
    }
    
    // Show form to edit a specific city
    public function area_edit($id)
    {
        $area = Area::findOrFail($id);
         $City = City::where('status',1)->get();
        return view('city::area.edit', compact('area','City'));
    }
    
    public function area_create(Request $request)
    {
        $request->validate([
            'city_id' => 'required',  // Ensure the city exists
            'status' => 'required',  // Ensure status is boolean
            'area_name' => 'required|string|max:155' . $request->city_id  // Check uniqueness per city
        ]);
    
        // If validation passes, create the area
        $area = Area::create([
            'Area_name' => $request->area_name,
            'city_id' => $request->city_id,
            'status' => $request->status,
        ]);
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $statusText = $area->status == 1 ? 'Active' : 'Inactive';
    
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Area Created',
            'long_description'  => "Area '{$area->Area_name}' created (ID: {$area->id}). City ID: {$area->city_id}. Status: {$statusText}.",
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'area_master.create',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
        return redirect()->route('admin.Green-Drive-Ev.Area.list')->with('success', 'Area created successfully.');
    }

    public function area_update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'city_id' => 'required',  // Ensure the city exists
            'status' => 'required',  // Ensure status is boolean
            'area_name' => 'required|string|max:155'  // Check uniqueness per city, excluding the current area
        ]);
    
        // Find the area by its ID
        $area = Area::findOrFail($id);
        $oldName = $area->Area_name;
        $oldCity = $area->city_id;
        $oldStatus = (int) $area->status;

        // Update the area
        $area->update([
            'Area_name' => $request->area_name,
            'city_id' => $request->city_id,
            'status' => $request->status,
        ]);
        
         $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $newStatus = (int) $area->status;
        $oldStatusText = $oldStatus == 1 ? 'Active' : 'Inactive';
        $newStatusText = $newStatus == 1 ? 'Active' : 'Inactive';
    
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Area Updated',
            'long_description'  => "Area updated (ID: {$area->id}). Name: '{$oldName}' → '{$area->Area_name}'; City ID: '{$oldCity}' → '{$area->city_id}'; Status: {$oldStatusText} → {$newStatusText}.",
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'area_master.update',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
    
        // Redirect with success message
        return redirect()->route('admin.Green-Drive-Ev.Area.list')->with('success', 'Area updated successfully.');
    }

}
