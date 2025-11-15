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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\VehicleManagement\Entities\VehicleType;
use Modules\AssetMaster\Entities\LocationMaster;
use Modules\AssetMaster\Entities\QualityCheckMaster;
use App\Exports\QualityCheckListExport;

class QualityCheckListController extends Controller
{
    public function index(Request $request)
    {
        $query = QualityCheckMaster::query();
    
        $status = $request->status ?? 'all';
        
        if($request->status != ""){
            $ch_status = $request->status;
        }else{
            $ch_status = 'all';
        }
        
        $fill_vehicle_type = $request->vehicle_type ?? '';
        
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
    
        if (in_array($status, ['1', '0'])) {
            
            $query->where('status', $status);
        }
    
        if ($fill_vehicle_type) {
            $query->where('vehicle_type_id', '=', $fill_vehicle_type);
        }
        
        if ($from_date) {
            $query->whereDate('created_at', '>=', $from_date);
        }
    
        if ($to_date) {
            $query->whereDate('created_at', '<=', $to_date);
        }
    
        $lists = $query->orderBy('id', 'desc')->get();
        
        $vehicle_types = VehicleType::where('is_active',1)->get();

        return view('assetmaster::qc_check_list.index', compact('lists', 'vehicle_types','status', 'from_date', 'to_date','ch_status','fill_vehicle_type'));
    }
    
    public function create(Request $request)
    {
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find(optional($user)->role))->name ?? 'Unknown';


        $data = $request->validate([
            'vehicle_type' => 'required|integer|exists:vehicle_types,id',
            'label_names' => 'required|array|min:1',
            'label_names.*' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);
        
        if ($validator->fails()) {
        // ❗ Audit: validation failed
        $errorsText = implode(', ', array_slice($validator->errors()->all(), 0, 10));
            audit_log([
                'module_id'         => 4,
                'short_description' => 'QC Checklist Labels Create Failed (Validation)',
                'long_description'  => 'Validation errors: ' . $errorsText,
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'qc_checklist_master.create',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors()
            ], 422);
        }
    
        foreach ($request->label_names as $labelName) {
            QualityCheckMaster::create([
                'label_name' => $labelName,
                'vehicle_type_id' => $request->vehicle_type,
                'status' => $request->status,
            ]);
        }
        
        $count  = count($request->label_names);
        $sample = implode(', ', array_slice($request->label_names, 0, 5)) . ($count > 5 ? ' …' : '');
        $vtName = optional(\Modules\VehicleManagement\Entities\VehicleType::find($request->vehicle_type))->name
                  ?? (string)$request->vehicle_type;
    
        audit_log([
            'module_id'         => 4,
            'short_description' => 'QC Checklist Labels Created',
            'long_description'  => "Created {$count} QC checklist label(s) for Vehicle Type \"{$vtName}\". Sample: {$sample}",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'qc_checklist_master.create',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
    
            return response()->json([
                'success' => true,
                'message' => 'New Label Names Added Successfully!'
            ]);
        

    }
    
    
    
      public function store(Request $request)
    {
         $user     = Auth::user();
         $roleName = optional(\Modules\Role\Entities\Role::find(optional($user)->role))->name ?? 'Unknown';
         
        if ($request->edit_qcl_id == "") {
            $data = $request->validate([
                'vehicle_type' => 'required',
                'name' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);
                if ($validator->fails()) {
                // Log: Validation failed
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'QC Checklist Master Save Failed (Validation)',
                    'long_description'  => 'Validation errors: ' . implode(', ', $validator->errors()->all()),
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'qc_checklist_master.store',
                    'ip_address'        => $request->ip(),
                    'user_device'       => $request->userAgent()
                ]);
        
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors()
                ], 422);
            }
            $data['label_name'] = $request->name;
            $data['vehicle_type_id'] = $request->vehicle_type;
            $data['status'] = $request->status;
            QualityCheckMaster::create($data);
            
            $vehicleTypeName = optional(VehicleType::find($request->vehicle_type))->name ?? 'Unknown';
             audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'QC Checklist Master Created',
                'long_description'  => 'Created QC checklist label "'.$data['label_name'].'" for Vehicle Type "'.$vehicleTypeName.'".',
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'qc_checklist_master.store.create',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
                
            return response()->json([
                'success' => true,
                'message' => 'New Label Name Added Successfully!'
            ]);
        } else {
            $QCL_Master = QualityCheckMaster::findOrFail($request->edit_qcl_id);
    
            $data = $request->validate([
                'vehicle_type' => 'required',
                'name' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);
            $data['label_name'] = $request->name;
            $data['vehicle_type_id'] = $request->vehicle_type;
            $data['status'] = $request->status;
            $QCL_Master->update($data);
            
            $vehicleTypeName = optional(VehicleType::find($request->vehicle_type))->name ?? 'Unknown';
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'QC Checklist Master Updated',
                'long_description'  => 'Updated QC checklist label (ID: '.$QCL_Master->id.') to "'.$data['label_name'].'" for Vehicle Type "'.$vehicleTypeName.'".',
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'qc_checklist_master.store.update',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Label Name Updated Successfully!'
            ]);
        }
    }
    
    public function destroy(Request $request)
    {
        $delete = QualityCheckMaster::where('id', $request->id)->first();
        if ($delete) {
            $labelName = $delete->label_name;
            $delete->delete();
            $roleName = optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown';

            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'QC Checklist Label Deleted',
                'long_description'  => 'The QC Checklist Label "'.$labelName.'" has been deleted.',
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'qc_checklist_master.destroy',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
            return response()->json([
                'success' => true,
                'message' => ' Label Name Deleted Successfully!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Label Name Deleted Failed!'
            ]);
        }
    }
    
    // public function update_status(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'id' => 'required|integer',
    //             'status' => 'required|boolean', 
    //         ]);
    
    
    //         $updated = QualityCheckMaster::where('id', $request->id)
    //             ->update(['status' => $request->status]);
    
    //         if ($updated) {
    //                 $statusText = $request->status ? 'Active' : 'Inactive';

    //                 // ✅ Audit Log
    //                 audit_log_after_commit([
    //                     'module_id'         => 4,
    //                     'short_description' => "QC Checklist Label {$statusText}",
    //                     'long_description'  => "QC Checklist Label '{$label->label_name}' has been {$statusText}.",
    //                     'role'              => optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown',
    //                     'user_id'           => Auth::id(),
    //                     'user_type'         => 'gdc_admin_dashboard',
    //                     'dashboard_type'    => 'web',
    //                     'page_name'         => 'qc_checklist_master.update_status',
    //                     'ip_address'        => request()->ip(),
    //                     'user_device'       => request()->userAgent()
    //                 ]);
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Status updated successfully.'
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Failed to update status or no changes detected.'
    //             ]);
    //         }
    
    //     } catch (\Exception $e) {
    //         audit_log_after_commit([
    //         'module_id'         => 4,
    //         'short_description' => 'QC Checklist Status Update Failed (Exception)',
    //         'long_description'  => "Error occurred while updating QC Checklist Status. Error: " . $e->getMessage(),
    //         'role'              => optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown',
    //         'user_id'           => Auth::id(),
    //         'user_type'         => 'gdc_admin_dashboard',
    //         'dashboard_type'    => 'web',
    //         'page_name'         => 'qc_checklist_master.update_status',
    //         'ip_address'        => request()->ip(),
    //         'user_device'       => request()->userAgent()
    //     ]);
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An error occurred: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
    
        public function update_status(Request $request)
    {
        try {
    
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'status' => 'required|boolean',
            ]);
    
            // ✅ If Validation Fails → Log & Return
            if ($validator->fails()) {
    
                $errorMessages = implode(', ', $validator->errors()->all());
    
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'QC Checklist Status Update Failed (Validation)',
                    'long_description'  => "Validation Error while updating QC Checklist status. Errors: {$errorMessages}",
                    'role'              => optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown',
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'qc_checklist_master.update_status',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                ]);
    
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $label = QualityCheckMaster::find($request->id);
    
            // ✅ Record Not Found Case Log
            if (!$label) {
    
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'QC Checklist Status Update Failed (Not Found)',
                    'long_description'  => "Attempted to update QC Checklist but no record found for ID: {$request->id}",
                    'role'              => optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown',
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'qc_checklist_master.update_status',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                ]);
    
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found.'
                ]);
            }
    
            $oldStatus = $label->status;
            $label->status = $request->status;
            $label->save();
    
            $statusText = $request->status ? 'Active' : 'Inactive';
    
            // ✅ Success Log
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => "QC Checklist Label Status Updated : {$statusText}",
                'long_description'  => "QC Checklist Label '{$label->label_name}' has been changed to {$statusText}.",
                'role'              => optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown',
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'qc_checklist_master.update_status',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
    
            return response()->json([
                'success' => true,
                'message' => "Status {$statusText} Successfully!"
            ]);
    
        } catch (\Exception $e) {
    
            // ✅ Exception Log
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'QC Checklist Status Update Failed (Exception)',
                'long_description'  => "Error occurred while updating QC Checklist Status. Error: " . $e->getMessage(),
                'role'              => optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown',
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'qc_checklist_master.update_status',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }


    public function export_qc_check_lists(Request $request)
    {
        $status = $request->status ?? 'all';
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
        $fill_vehicle_type = $request->fill_vehicle_type ?? '';
        
            audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'QC Checklist Exported',
            'long_description'  => 'Quality Check label list export has been done.',
            'long_description'  => sprintf(
                    'Quality Check label list export triggered. Filters -> Status: %s,Fill Vehicle Type: %s, From: %s, To: %s, Selected IDs: %d',
                    $status ?: 'all',
                    $fill_vehicle_type ?: '-',
                    $from_date ?: '-',
                    $to_date ?: '-',
                    is_array($selectedIds) ? count($selectedIds) : 0
                ),
            'role'              => optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown',
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'qc_checklist_master.export_qc_check_lists',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
    
       return Excel::download(
            new QualityCheckListExport($status, $from_date, $to_date, $fill_vehicle_type),
            'Quality Check Label list ' . date('d-m-Y') . '.xlsx'
        );

    }

    
    
    
}