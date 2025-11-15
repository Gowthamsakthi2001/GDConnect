<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\VehicleManagement\DataTables\VehicleTypeDataTable;
use Modules\VehicleManagement\Entities\VehicleType;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VehicleTypeExport;
use Illuminate\Support\Facades\Auth;

class VehicleTypeController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:vehicle_type_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Vehicle Type Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Vehicle Type Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle.type',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        // $lists = VehicleType::orderBy('id','desc')->get();
        
        $query = VehicleType::query();
    
        $status = $request->status ?? 'all';
        
        if($request->status != ""){
            $ch_status = $request->status;
        }else{
            $ch_status = 'all';
        }

        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
    
        if (in_array($status, ['1', '0'])) {
            
            $query->where('is_active', $status);
        }
    
        if ($from_date) {
            $query->whereDate('created_at', '>=', $from_date);
        }
    
        if ($to_date) {
            $query->whereDate('created_at', '<=', $to_date);
        }
    
        $lists = $query->orderBy('id', 'desc')->get();
        return view('vehiclemanagement::type.index',compact('lists','status', 'from_date', 'to_date','ch_status'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vehiclemanagement::type.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
     
   public function store(Request $request)
    {
        if ($request->type_id == "") {
            // Add new Vehicle Type
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:vehicle_types,name',
                'description' => 'nullable|string',
                'status' => 'required|boolean',
            ]);
            $data['is_active'] = $request->status;
            $vehicleType = VehicleType::create($data);
            
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
            $statusText = $vehicleType->is_active == 1 ? 'Active' : 'Inactive';

            audit_log_after_commit([
                'module_id'         => 7,
                'short_description' => 'Vehicle Type Created',
                'long_description'  => "Vehicle Type '{$vehicleType->name}' created (ID: {$vehicleType->id}). Status: {$statusText}.",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'vehicle_type.store',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'New Vehicle Type Added Successfully!'
            ]);
        } else {
            // Update existing Vehicle Type
            $vehicleType = VehicleType::findOrFail($request->type_id);
    
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:vehicle_types,name,' . $vehicleType->id,
                'description' => 'nullable|string',
                'status' => 'required|boolean',
            ]);
            $data['is_active'] = $request->status;
            $oldName = $vehicleType->name;
            $oldDescription = $vehicleType->description;
            $oldStatus = (int) $vehicleType->is_active;
            $oldStatusText = $oldStatus == 1 ? 'Active' : 'Inactive';
        
            $vehicleType->update($data);
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
            $newStatus = (int) $vehicleType->is_active;
            $newStatusText = $newStatus == 1 ? 'Active' : 'Inactive';

            audit_log_after_commit([
                'module_id'         => 7,
                'short_description' => 'Vehicle Type Updated',
                'long_description'  => "Vehicle Type updated (ID: {$vehicleType->id}). Name: '{$oldName}' → '{$vehicleType->name}'; Description changed: " . ($oldDescription === $vehicleType->description ? 'No' : 'Yes') . "; Status: {$oldStatusText} → {$newStatusText}.",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'vehicle_type.update',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Vehicle Type Updated Successfully!'
            ]);
        }
    }
    
    
    public function update_status(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer',
                'status' => 'required|boolean', 
            ]);
    
    
            $updated = VehicleType::where('id', $request->id)->first();
            $oldStatus = (int) $updated->is_active;
            $newStatus = (int) $request->status;
            $updated->update(['is_active' => $request->status]);
    
            if ($updated) {
                $user = Auth::user();
                $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
                $oldText = $oldStatus == 1 ? 'Active' : 'Inactive';
                $newText = $newStatus == 1 ? 'Active' : 'Inactive';
                
                audit_log_after_commit([
                    'module_id'         => 7,
                    'short_description' => 'Vehicle Type Status Updated',
                    'long_description'  => "Vehicle Type '{$updated->name}' (ID: {$updated->id}) status changed: {$oldText} → {$newText}.",
                    'role'              => $roleName,
                    'user_id'           => $user->id ?? null,
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'vehicle_type.update_status',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                ]);
                
                    return response()->json([
                    'success' => true,
                    'message' => 'Status updated successfully.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update status or no changes detected.'
                ]);
            }
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }


    


    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(VehicleType $type)
    {
        return view('vehiclemanagement::type.create_edit', ['item' => $type])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleType $type, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_types,name,' . $type->id . ',id',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $type->update($data);

        return response()->success($type, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $delete = VehicleType::where('id', $request->id)->first();
        if ($delete) {
            $delete->delete();
            return response()->json([
                'success' => true,
                'message' => ' Vehicle Type Deleted Successfully!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle Type Deleted Failed!'
            ]);
        }
    }
    
    public function export_vehicle_type_lists(Request $request)
    {
        return Excel::download(new VehicleTypeExport(), 'Vehicle Type list ' . date('d-m-Y') . '.xlsx');
    }
    
}
