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
        $data = $request->validate([
            'vehicle_type' => 'required|integer|exists:vehicle_types,id',
            'label_names' => 'required|array|min:1',
            'label_names.*' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);
    
        foreach ($request->label_names as $labelName) {
            QualityCheckMaster::create([
                'label_name' => $labelName,
                'vehicle_type_id' => $request->vehicle_type,
                'status' => $request->status,
            ]);
        }

            return response()->json([
                'success' => true,
                'message' => 'New Label Names Added Successfully!'
            ]);
        

    }
    
    
    
      public function store(Request $request)
    {
        if ($request->edit_qcl_id == "") {
            $data = $request->validate([
                'vehicle_type' => 'required',
                'name' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);
            $data['label_name'] = $request->name;
            $data['vehicle_type_id'] = $request->vehicle_type;
            $data['status'] = $request->status;
            QualityCheckMaster::create($data);
    
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
            $delete->delete();
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
    
    public function update_status(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer',
                'status' => 'required|boolean', 
            ]);
    
    
            $updated = QualityCheckMaster::where('id', $request->id)
                ->update(['status' => $request->status]);
    
            if ($updated) {
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
    
    public function export_qc_check_lists(Request $request)
    {
        $status = $request->status ?? 'all';
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
        $fill_vehicle_type = $request->fill_vehicle_type ?? '';
        
       return Excel::download(
            new QualityCheckListExport($status, $from_date, $to_date, $fill_vehicle_type),
            'Quality Check Label list ' . date('d-m-Y') . '.xlsx'
        );

    }

    
    
    
}