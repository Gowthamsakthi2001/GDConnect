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
use App\Models\EVState;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Modules\City\Entities\City;
use Modules\MasterManagement\Entities\CustomerTypeMaster;
use App\Exports\CustomerTypeMasterExport;
use App\Helpers\CustomHandler;
use Illuminate\Support\Facades\Auth;

class CustomerTypeMasterController extends Controller
{
    
    public function index(Request $request)
    {
        $query = CustomerTypeMaster::query();
    
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
    
        $data = $query->orderBy('id', 'desc')->get();

        return view('mastermanagement::customer_type_master.index' , compact('data' ,'from_date' ,'to_date'));
    }
    
    
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_type' => 'required|string|max:255|unique:ev_tbl_customer_type_master,name',
            'status' => 'required', // adjust as per your allowed values
        ]);
    
    
        CustomerTypeMaster::create([
            'name' => $validated['customer_type'],
            'status' => $validated['status'],
        ]);
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    
        // Prepare log details
        $statusText = $validated['status'] == 1 ? 'Active' : 'Inactive';
        $longDescription = sprintf(
            "New Customer Type '%s' created with status: %s (ID: %s)",
            $validated['customer_type'],
            $statusText,
            $customerType->id
        );
    
        // Record the audit log after commit
        audit_log_after_commit([
            'module_id'         => 1, // Adjust module ID if needed
            'short_description' => 'Customer Type Created',
            'long_description'  => $longDescription,
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'customer_type_master.store',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Customer type created successfully.',
        ]);
        
        
    }

        public function update_status(Request $request)
    {
        $request->validate([
            'status' => 'required',
        ]);
    
        $record = CustomerTypeMaster::find($request->id);
        $oldStatus = $record->status;
        $record->status = $request->status;
        $record->save();
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $oldStatusText = $oldStatus == 1 ? 'Active' : 'Inactive';
        $newStatusText = $request->status == 1 ? 'Active' : 'Inactive';

        // Prepare log message
        $longDescription = sprintf(
            "Customer Type '%s' (ID: %d) status changed from %s to %s.",
            $record->name,
            $record->id,
            $oldStatusText,
            $newStatusText
        );

        // ✅ Record audit log
        audit_log_after_commit([
            'module_id'         => 1, // Keep consistent with Customer Type Master module
            'short_description' => 'Customer Type Status Updated',
            'long_description'  => $longDescription,
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'customer_type_master.update_status',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent(),
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
        ]);
    }
    
    
        public function update(Request $request)
    {
     
        $request->validate([
            'customer_type_name' => 'required|string|max:255|unique:ev_tbl_color_master,name,' . $request->id,
            'status' => 'required',
        ]);
    
        $model = CustomerTypeMaster::find($request->id);
         $old = $model->getAttributes();
        $model->update([
            'name' => $request->customer_type_name,
            'status' => $request->status,
            'updated_at'=> now() 
        ]);
        
        $new = $model->getAttributes();
        
        $changes = [];
        // Only list the fields you care about (name, status)
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
                'short_description' => 'Customer Type Updated',
                'long_description'  => "Customer Type '{$model->name}' (ID: {$model->id}) updated. Changes: {$changesText}",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'customer_type_master.update',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
        return response()->json(['success' => true, 'message' => 'Customer type updated successfully.']);
    }
    
        public function export_customer_type_master(Request $request)
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
            "Customer Type Master export initiated. Filters → Status: %s | From: %s | To: %s | Selected IDs: %s%s",
            $status ?? '-',
            $from_date ?? '-',
            $to_date ?? '-',
            $idsSample,
            $more
        );
        
        audit_log_after_commit([
                'module_id'         => 1,
                'short_description' => 'Customer Type Export Triggered',
                'long_description'  => $longDescription,
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'customer_type_master.export',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
        
          return Excel::download(new CustomerTypeMasterExport($status,$from_date,$to_date , $selectedIds), 'customer-type-master-' . date('d-m-Y') . '.xlsx');
       
    }
    
}