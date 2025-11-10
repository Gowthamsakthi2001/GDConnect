<?php

namespace Modules\MasterManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Modules\MasterManagement\Entities\RecoveryUpdatesMaster;
use App\Exports\RecoveryUpdatesMasterExport; // create similar to ColorMasterExport

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
        $record->status = (int) $request->status;
        $record->save();

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
        $model->update([
            'label_name'      => $request->label_name,
            'status'    => (int) $request->status,
            'updated_at'=> now(),
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
        return Excel::download(
            new RecoveryUpdatesMasterExport($status, $from_date, $to_date, $selectedIds),
            'recovery-updates-master-' . date('d-m-Y') . '.xlsx'
        );
    }
}
