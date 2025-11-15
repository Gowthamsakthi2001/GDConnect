<?php

namespace Modules\MasterManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SidebarModule;
use Modules\Role\Entities\Role;
use App\Helpers\CustomHandler;
use Modules\MasterManagement\Entities\RecoveryReasonMaster;
use App\Exports\RecoveryReasonMasterExport;

class RecoveryReasonMasterController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = RecoveryReasonMaster::query();

            $type  = $request->input('type');
            $status  = $request->input('status');
            $search  = $request->input('search.value');
            $start   = $request->input('start', 0);
            $length  = $request->input('length', 15);

            if ($type !== "" && $status !== null) {

                $query->where('type', $type);
            }

            if ($status !== null && $status !== 'all') {
                $query->where('status', $status);
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('label_name', 'like', "%$search%");
                });
            }

            $totalRecords = $query->count();

            if ($length == -1) {
                $length = $totalRecords;
            }

            // dd($query->toSql(),$query->getBindings());
            $data = $query->orderBy('id', 'desc')
                ->skip($start)
                ->take($length)
                ->get();

            $sno = $start + 1;

            $formattedData = $data->map(function ($item) use (&$sno) {
                $statusHtml = $item->status == 1
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';

            $label_name = $item->label_name ?? 'N/A';
            $type_name = strtoupper($item->type) ?? 'N/A';
            $created_at = $item->created_at ? date('d-m-Y h:i:s A',strtotime($item->created_at)) : 'N/A';
            $updated_at = $item->updated_at ? date('d-m-Y h:i:s A',strtotime($item->updated_at)) : 'N/A';
           $statusHtml = '
                    <div class="form-check form-switch d-flex justify-content-center align-items-center m-0 p-0">
                        <input class="form-check-input toggle-status"
                               data-id="'.$item->id.'"
                               type="checkbox"
                               role="switch"
                               id="toggleSwitch'.$item->id.'"
                               '.($item->status == 1 ? 'checked' : '').'>
                    </div>';

             $actionsHtml = '
                <a href="javascript:void(0);" data-labelname="'.$label_name.'" data-status="'.$item->status.'" onclick="AddorEditRRModal('.$item->id.',this)" class="dropdown-item d-flex align-items-center justify-content-center">
                    <i class="bi bi-pencil-square me-2"></i>
                </a>
            ';

                return [
                    'checkbox' => $sno++,
                    'label_name' => $label_name,
                    'type_name' => $type_name,
                    'status' => $statusHtml,
                    'created_at'=>$created_at,
                    'updated_at'=>$updated_at,
                    'actions' => $actionsHtml,
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $formattedData,
            ]);
        }
        return view('mastermanagement::recovery_master.recovery_reasons');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'reason_name'     => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $normalized = strtolower(preg_replace('/\s+/', '', $value)); 
        
                    $exists = DB::table('ev_tbl_recovery_reason_master')
                        ->whereRaw("REPLACE(LOWER(label_name), ' ', '') = ?", [$normalized])
                        ->exists();
        
                    if ($exists) {
                        $fail('The label name has already been taken.');
                    }
                },
            ],
            'status'      => 'required|in:0,1'
        ]);
        
        $createModel = new RecoveryReasonMaster();
        $createModel->label_name = $request->reason_name;
        $createModel->type = 'gdm';
        $createModel->status = $request->status;
        $createModel->save();
        
        audit_log_after_commit([
            'module_id'         => 1, 
            'short_description' => 'Recovery Reason Created',
            'long_description'  => "A new recovery reason '{$createModel->label_name}' was created (DB ID: {$createModel->id}). Status: {$createModel->status}.",
            'role'              => optional(Auth::user())->role ?? 'admin',
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'recovery_reason_master.create',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent(),
        ]);

        return response()->json(['success' => true, 'message' => 'Reason created successfully']);
    }
    
    public function update(Request $request, $id)
    {

         $request->validate([
            'reason_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($id) {
                    $normalized = strtolower(preg_replace('/\s+/', '', $value)); 
            
                    $exists = DB::table('ev_tbl_recovery_reason_master')
                        ->where('id', '!=', $id)
                        ->whereRaw("REPLACE(LOWER(label_name), ' ', '') = ?", [$normalized])
                        ->exists();
            
                    if ($exists) {
                        $fail('The label name has already been taken.');
                    }
                },
            ],
            'status'      => 'required|in:0,1'
        ]);
        
        $updateModel = RecoveryReasonMaster::where('id',$id)->first();
        if(!$updateModel){
            return response()->json(['success' => false, 'message' => 'Reason Not Found!']);
        }
        $old = [
            'label_name' => $updateModel->label_name,
            'type'       => $updateModel->type,
            'status'     => (string)$updateModel->status,
        ];
        $updateModel->label_name = $request->reason_name;
        $updateModel->type = 'gdm';
        $updateModel->status = $request->status;
        $updateModel->save();
        
        $changes = [];
        if ($old['label_name'] !== $updateModel->label_name) {
            $changes[] = "label_name: '{$old['label_name']}' -> '{$updateModel->label_name}'";
        }
        if ($old['type'] !== $updateModel->type) {
            $changes[] = "type: '{$old['type']}' -> '{$updateModel->type}'";
        }
        if ((string)$old['status'] !== (string)$updateModel->status) {
            $changes[] = "status: '{$old['status']}' -> '{$updateModel->status}'";
        }
        $changesText = empty($changes) ? 'No visible changes (values remained the same).' : implode('; ', $changes);
        
        audit_log_after_commit([
            'module_id'         => 1, 
            'short_description' => 'Recovery Reason Updated',
            'long_description'  => "Recovery reason (DB ID: {$updateModel->id}) updated. Changes: {$changesText}",
            'role'              => optional(Auth::user())->role ?? 'admin',
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'recovery_reason_master.update',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent(),
        ]);
            return response()->json(['success' => true, 'message' => 'Reason updated successfully']);
        }
    
    public function status_update(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer',
                'status' => 'required|boolean', 
            ]);
    
            
            $model = RecoveryReasonMaster::where('id', $request->id)->first();
                $model->update(['status' => $request->status]);
            
                $changesText = "status:'{$request->status}'";

            if ($model) {
                audit_log_after_commit([
                    'module_id'         => 1, // adjust if you use a different module id
                    'short_description' => 'Recovery Reason Status Updated',
                    'long_description'  => "Recovery reason (DB ID: {$model->id}, label: '{$model->label_name}') status updated. Changes: {$changesText}",
                    'role'              => optional(Auth::user())->role ?? 'admin',
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'recovery_reason_master.status_update',
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
    
     public function export(Request $request)
    {
        
        $status = $request->status;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $selectedIds = json_decode($request->query('selected_ids', '[]'), true);
        
        $selectedCount = is_array($selectedIds) ? count($selectedIds) : 0;
        $idsSample = $selectedCount > 0 ? implode(',', array_slice($selectedIds, 0, 5)) : '-';
        $more = $selectedCount > 5 ? ' (+' . ($selectedCount - 5) . ' more)' : '';
        audit_log_after_commit([
            'module_id'         => 1, // same as your Recovery Reason Master module id
            'short_description' => 'Recovery Reason Export Triggered',
            'long_description'  => sprintf(
                "Recovery Reason export initiated. Filters → Status: %s, From: %s, To: %s, Selected IDs: %s%s",
                $status ?? '-',
                $from_date ?? '-',
                $to_date ?? '-',
                $idsSample,
                $more
            ),
            'role'              => optional(Auth::user())->role ?? 'admin',
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'recovery_reason_master.export',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent(),
        ]);
         return Excel::download(new RecoveryReasonMasterExport($status,$from_date,$to_date , $selectedIds), 'Recovery Reasons-' . date('d-m-Y') . '.xlsx');
       
    }
    
}