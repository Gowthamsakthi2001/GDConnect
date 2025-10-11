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
use Modules\MasterManagement\Entities\BusinessConstitutionType;
use App\Exports\BusinessConstitutionTypeExport;


class BusinessConstitutionTypeController extends Controller
{
    
    public function index(Request $request)
    {
        $query = BusinessConstitutionType::query();
    
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

        return view('mastermanagement::business_constitution_type_master.index', compact('lists','status', 'from_date', 'to_date','ch_status'));
    }
    
     public function store(Request $request)
    {
        if ($request->edit_id == "") {
            // Create new record
            $data = $request->validate([
                'hypothecation_name' => 'required|unique:business_constitution_types,name|string|max:255',
                'status' => 'required|boolean',
            ]);
    
            $data['name'] = $request->hypothecation_name;
            $data['status'] = $request->status;
    
            BusinessConstitutionType::create($data);
    
            return response()->json([
                'success' => true,
                'message' => 'New Hypothecation Name Added Successfully!'
            ]);
        } else {
    
            $HYP_Master = BusinessConstitutionType::where('id', $request->edit_id)->first();
    
            if (!$HYP_Master) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found!'
                ], 404);
            }
    
            $data = $request->validate([
                'hypothecation_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('business_constitution_types', 'name')->ignore($request->edit_id),
                ],
                'status' => 'required|boolean',
            ]);
    
            $data['name'] = $request->hypothecation_name;
            $data['status'] = $request->status;
    
            $HYP_Master->update($data);
    
            return response()->json([
                'success' => true,
                'message' => 'Hypothecation Name Updated Successfully!'
            ]);
        }
    }
    
    // public function destroy(Request $request)
    // {
    //     $delete = BusinessConstitutionType::where('id', $request->id)->first();
    //     if ($delete) {
    //         $delete->delete();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Hypothecation Name Deleted Successfully!'
    //         ]);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Hypothecation Name Deleted Failed!'
    //         ]);
    //     }
    // }
    
    public function status_update(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer',
                'status' => 'required|boolean', 
            ]);
    
    
            $updated = BusinessConstitutionType::where('id', $request->id)
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
    
    public function export_hypthecation(Request $request)
    {
        
        $status = $request->status;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $selectedIds = json_decode($request->query('selected_ids', '[]'), true);
        
         return Excel::download(new BusinessConstitutionTypeExport($status,$from_date,$to_date , $selectedIds), 'Hypothecations-' . date('d-m-Y') . '.xlsx');
       
    }
    
   
}