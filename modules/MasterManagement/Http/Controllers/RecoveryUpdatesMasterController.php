<?php

namespace Modules\MasterManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Modules\MasterManagement\Entities\RecoveryUpdatesMaster;
use App\Exports\RecoveryUpdatesMasterExport; // create similar to ColorMasterExport
use Illuminate\Support\Facades\Auth;

class RecoveryUpdatesMasterController extends Controller
{
    public function index(Request $request)
    {
        $query = RecoveryUpdatesMaster::query();

        $status    = $request->status ?? 'all';
        $from_date = $request->from_date ?? '';
        $to_date   = $request->to_date ?? '';

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

        // blade created earlier: resources/views/admin/recovery_updates/index.blade.php
        return view('mastermanagement::recovery_master.recovery_updates_master', compact('data', 'from_date', 'to_date'));
    }

    public function store(Request $request)
    {
        $table = (new RecoveryUpdatesMaster)->getTable();

        $validated = $request->validate([
            'label_name'  => ['required', 'string', 'max:255', Rule::unique($table, 'label_name')],
            'status' => ['required', Rule::in(['0','1',0,1])],
        ]);

        RecoveryUpdatesMaster::create([
            'label_name'   => $validated['label_name'],
            'status' => (int) $validated['status'],
        ]);
        
         $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
            $statusText = $model->status == 1 ? 'Active' : 'Inactive';

            audit_log_after_commit([
                'module_id'         => 1,
                'short_description' => 'Recovery Update Created',
                'long_description'  => "Recovery update '{$model->label_name}' created (ID: {$model->id}). Status: {$statusText}.",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'recovery_updates_master.store',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
            
        return response()->json([
            'success' => true,
            'message' => 'New recovery update created successfully.',
        ]);
    }

    public function update_status(Request $request)
    {
        $request->validate([
            'id'     => ['required','integer','exists:' . (new RecoveryUpdatesMaster)->getTable() . ',id'],
            'status' => ['required', Rule::in(['0','1',0,1])],
        ]);

        $record = RecoveryUpdatesMaster::find($request->id);
        $oldStatus = (int) $record->status;
        $newStatus = (int) $request->status;
        $record->status = (int) $request->status;
        $record->save();
        
                $user = Auth::user();
                $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
                $oldText = $oldStatus == 1 ? 'Active' : 'Inactive';
                $newText = $newStatus == 1 ? 'Active' : 'Inactive';
                
            audit_log_after_commit([
                    'module_id'         => 1,
                    'short_description' => 'Recovery Update Status Changed',
                    'long_description'  => "Recovery update '{$record->label_name}' (ID: {$record->id}) status changed: {$oldText} → {$newText}.",
                    'role'              => $roleName,
                    'user_id'           => $user->id ?? null,
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'recovery_updates_master.update_status',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                
                ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
        ]);
    }

    public function update(Request $request)
    {
        $table = (new RecoveryUpdatesMaster)->getTable();

        $request->validate([
            'id'     => ['required','integer','exists:' . $table . ',id'],
            'label_name'   => ['required','string','max:255', Rule::unique($table, 'label_name')->ignore($request->id)],
            'status' => ['required', Rule::in(['0','1',0,1])],
        ]);

        $model = RecoveryUpdatesMaster::find($request->id);
        $old = $model->getAttributes();
        $model->update([
            'label_name'      => $request->label_name,
            'status'    => (int) $request->status,
            'updated_at'=> now(),
        ]);
        
        $new = $model->getAttributes();
         $changes = [];
        if (($old['label_name'] ?? null) != ($new['label_name'] ?? null)) {
            $changes[] = "Label Name: " . ($old['label_name'] ?? 'N/A') . " → " . ($new['label_name'] ?? 'N/A');
        }
        if ((string)($old['status'] ?? '') !== (string)($new['status'] ?? '')) {
            $oldStatusText = isset($old['status']) ? (($old['status'] == 1) ? 'Active' : 'Inactive') : 'N/A';
            $newStatusText = ($new['status'] == 1) ? 'Active' : 'Inactive';
            $changes[] = "Status: {$oldStatusText} → {$newStatusText}";
        }
        $changesText = empty($changes) ? 'No visible changes detected.' : implode('; ', $changes);
        
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

            audit_log_after_commit([
                'module_id'         => 1,
                'short_description' => 'Recovery Update Edited',
                'long_description'  => "Recovery update '{$model->label_name}' (ID: {$model->id}) updated. Changes: {$changesText}",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'recovery_updates_master.update',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Recovery Updates Master updated successfully.',
        ]);
    }

    public function export(Request $request)
    {
        $status      = $request->status;
        $from_date   = $request->from_date;
        $to_date     = $request->to_date;
        $selectedIds = json_decode($request->query('selected_ids', '[]'), true);

        // Create an export class similar to ColorMasterExport
        // e.g., app/Exports/RecoveryUpdatesMasterExport.php
         $selectedCount = is_array($selectedIds) ? count($selectedIds) : 0;
        $idsSample = $selectedCount > 0 ? implode(',', array_slice($selectedIds, 0, 5)) : '-';
        $more = $selectedCount > 5 ? ' (+' . ($selectedCount - 5) . ' more)' : '';
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

        $longDescription = sprintf(
            "Recovery Updates Master export initiated. Filters → Status: %s | From: %s | To: %s | Selected IDs: %s%s",
            $status ?? '-',
            $from_date ?? '-',
            $to_date ?? '-',
            $idsSample,
            $more
        );
        
            audit_log_after_commit([
                'module_id'         => 7,
                'short_description' => 'Recovery Updates Export Triggered',
                'long_description'  => $longDescription,
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'recovery_updates_master.export',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
        return Excel::download(
            new RecoveryUpdatesMasterExport($status, $from_date, $to_date, $selectedIds),
            'recovery-updates-master-' . date('d-m-Y') . '.xlsx'
        );
    }
}
