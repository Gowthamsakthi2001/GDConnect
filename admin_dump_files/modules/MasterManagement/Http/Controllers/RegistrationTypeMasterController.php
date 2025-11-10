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
    
            RegistrationTypeMaster::create($data);
    
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
    
            $HYP_Master->update($data);
    
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
    
    
            $updated = RegistrationTypeMaster::where('id', $request->id)
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
    
    
    
    
    public function export_registration_type(Request $request)
    {
    
     
        
        $status = $request->status;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $selectedIds = json_decode($request->query('selected_ids', '[]'), true);
        
         return Excel::download(new RegistrationTypeMasterExport($status,$from_date,$to_date , $selectedIds), 'Registration-types-' . date('d-m-Y') . '.xlsx');
       
    }
    
   
}