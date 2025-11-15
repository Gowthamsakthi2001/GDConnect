<?php

namespace Modules\MasterManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Modules\MasterManagement\Entities\RegistrationTypeMaster;
use App\Exports\RegistrationTypeMasterExport;
use Illuminate\Support\Facades\Auth;

class RegistrationTypeMasterController extends Controller
{
    
    public function index(Request $request)
    {
        $query = RegistrationTypeMaster::query();
      
        
    
        $status = $request->status ?? 'all';
        
        if($request->status != ""){
            $ch_status = $request->status;
        }else{
            $ch_status = 'all';
        }
        
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
    
        if (in_array($status, ['1', '0'])) {
            
            $query->where('status', $status);
        }
    
        
        if ($from_date) {
            $query->whereDate('created_at', '>=', $from_date);
        }
    
        if ($to_date) {
            $query->whereDate('created_at', '<=', $to_date);
        }
    
        $lists = $query->orderBy('id', 'desc')->get();
        

        
        

        return view('mastermanagement::registration_type_master.index' , compact('lists' ,'ch_status' , 'from_date' , 'to_date') );
    }
    
     public function store(Request $request)
    {
       
        
   
        if ($request->edit_id == "") {
            // Create new record
            $data = $request->validate([
                'registration_type' => 'required|unique:ev_tbl_registration_types,name|string|max:255',
                'status' => 'required|boolean',
            ]);
    
            $data['name'] = $request->registration_type;
            $data['status'] = $request->status;
    
            $model =RegistrationTypeMaster::create($data);
            
             $user = Auth::user();
                $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
                $statusText = $model->status == 1 ? 'Active' : 'Inactive';

                audit_log_after_commit([
                    'module_id'         => 1, // change if you use a different module id
                    'short_description' => 'Registration Type Created',
                    'long_description'  => "Registration Type '{$model->name}' created (ID: {$model->id}). Status: {$statusText}.",
                    'role'              => $roleName,
                    'user_id'           => $user->id ?? null,
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'registration_type_master.store',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                    
                ]);   
                
            return response()->json([
                'success' => true,
                'message' => 'New Registration Type Added Successfully!'
            ]);
        } else {
            
    
            $HYP_Master = RegistrationTypeMaster::where('id', $request->edit_id)->first();
    
            if (!$HYP_Master) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found!'
                ], 404);
            }
    
            $data = $request->validate([
                'registration_type' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('ev_tbl_registration_types', 'name')->ignore($request->edit_id),
                ],
                'status' => 'required|boolean',
            ]);
    
            $data['name'] = $request->registration_type;
            $data['status'] = $request->status;
    
            $old = $HYP_Master->getAttributes();

            $HYP_Master->update($data);

            $new = $HYP_Master->getAttributes();

            // Build changes text
            $changes = [];
            if (($old['name'] ?? null) != ($new['name'] ?? null)) {
                $changes[] = "Name: " . ($old['name'] ?? 'N/A') . " → " . ($new['name'] ?? 'N/A');
            }
            if ((string)($old['status'] ?? '') !== (string)($new['status'] ?? '')) {
                $oldStatus = isset($old['status']) ? (($old['status'] == 1) ? 'Active' : 'Inactive') : 'N/A';
                $newStatus = ($new['status'] == 1) ? 'Active' : 'Inactive';
                $changes[] = "Status: {$oldStatus} → {$newStatus}";
            }
            $changesText = empty($changes) ? 'No visible changes detected.' : implode('; ', $changes);
            
            $user = Auth::user();
                $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

                audit_log_after_commit([
                    'module_id'         => 1,
                    'short_description' => 'Registration Type Updated',
                    'long_description'  => "Registration Type '{$HYP_Master->name}' (ID: {$HYP_Master->id}) updated. Changes: {$changesText}",
                    'role'              => $roleName,
                    'user_id'           => $user->id ?? null,
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'registration_type_master.update',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Registration Type Updated Successfully!'
            ]);
        }
    }
    
    // // // public function destroy(Request $request)
    // // // {
    // // //     $delete = RegistrationTypeMaster::where('id', $request->id)->first();
    // // //     if ($delete) {
    // // //         $delete->delete();
    // // //         return response()->json([
    // // //             'success' => true,
    // // //             'message' => 'Hypothecation Name Deleted Successfully!'
    // // //         ]);
    // // //     } else {
    // // //         return response()->json([
    // // //             'success' => false,
    // // //             'message' => 'Hypothecation Name Deleted Failed!'
    // // //         ]);
    // // //     }
    // // // }
    
    public function status_update(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer',
                'status' => 'required|boolean', 
            ]);
    
    
            $updated = RegistrationTypeMaster::where('id', $request->id)->first();
             $oldStatus =  $updated->status;
             $updated->update(['status' => $request->status]);
    
            if ($updated) {
               
                $newStatus =  $request->status;
                
                $user = Auth::user();
                $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
                $oldText = $oldStatus == "1" ? 'Active' : 'Inactive';
                $newText = $newStatus == "1" ? 'Active' : 'Inactive';

                audit_log_after_commit([
                    'module_id'         => 1,
                    'short_description' => 'Registration Type Status Updated',
                    'long_description'  => "Registration Type '{$updated->name}' (ID: {$updated->id}) status changed: {$oldText} → {$newText}.",
                    'role'              => $roleName,
                    'user_id'           => $user->id ?? null,
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'registration_type_master.status_update',
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
    
    
    
    
    public function export_registration_type(Request $request)
    {
    
     
        
        $status = $request->status;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $selectedIds = json_decode($request->query('selected_ids', '[]'), true);
        
        $selectedCount = is_array($selectedIds) ? count($selectedIds) : 0;
        $idsSample = $selectedCount > 0 ? implode(',', array_slice($selectedIds, 0, 5)) : '-';
        $more = $selectedCount > 5 ? ' (+' . ($selectedCount - 5) . ' more)' : '';

        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

        $longDescription = sprintf(
            "Registration Type export initiated. Filters → Status: %s | From: %s | To: %s | Selected IDs: %s%s",
            $status ?? '-',
            $from_date ?? '-',
            $to_date ?? '-',
            $idsSample,
            $more
        );
        
        audit_log_after_commit([
                'module_id'         => 1,
                'short_description' => 'Registration Type Export Triggered',
                'long_description'  => $longDescription,
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'registration_type_master.export',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
            
         return Excel::download(new RegistrationTypeMasterExport($status,$from_date,$to_date , $selectedIds), 'Registration-types-' . date('d-m-Y') . '.xlsx');
       
    }
    
   
}