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
use Modules\MasterManagement\Entities\InventoryLocationMaster;
use App\Exports\InventoryLocationMasterExport;


class InventoryLocationMasterController extends Controller
{
    
    public function index(Request $request)
    {
        
     
        
        $query = InventoryLocationMaster::query();
    
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

        return view('mastermanagement::inventory_location_master.index', compact('lists' ,'status', 'from_date', 'to_date','ch_status'));
    }
    
     public function store(Request $request)
    {
       
        
        if ($request->edit_id == "") {
            // Create new record
            $data = $request->validate([
                'location' => 'required|unique:ev_tbl_inventory_location_master,name|string|max:255',
                'status' => 'required|boolean',
            ]);
    
            $data['name'] = $request->location;
            $data['status'] = $request->status;
    
            InventoryLocationMaster::create($data);
    
            return response()->json([
                'success' => true,
                'message' => 'New Inventory Location Added Successfully!'
            ]);
        } else {
            
            
    
            $INV_Master = InventoryLocationMaster::where('id', $request->edit_id)->first();
    
            if (!$INV_Master) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found!'
                ], 404);
            }
    
            $data = $request->validate([
                'location' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('ev_tbl_inventory_location_master', 'name')->ignore($request->edit_id),
                ],
                'status' => 'required|boolean',
            ]);
    
            $data['name'] = $request->location;
            $data['status'] = $request->status;
    
            $INV_Master->update($data);
    
            return response()->json([
                'success' => true,
                'message' => 'Inventory Location Updated Successfully!'
            ]);
        }
    }
    
    // // public function destroy(Request $request)
    // // {
    // //     $delete = InventoryLocationMaster::where('id', $request->id)->first();
    // //     if ($delete) {
    // //         $delete->delete();
    // //         return response()->json([
    // //             'success' => true,
    // //             'message' => 'Hypothecation Name Deleted Successfully!'
    // //         ]);
    // //     } else {
    // //         return response()->json([
    // //             'success' => false,
    // //             'message' => 'Hypothecation Name Deleted Failed!'
    // //         ]);
    // //     }
    // // }
    
    public function update_status(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer',
                'status' => 'required|boolean', 
            ]);
    
    
            $updated = InventoryLocationMaster::where('id', $request->id)
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
    
    public function export_inventory_location(Request $request)
    {
        
        $status = $request->status;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $selectedIds = json_decode($request->query('selected_ids', '[]'), true);
        
         return Excel::download(new InventoryLocationMasterExport($status,$from_date,$to_date , $selectedIds), 'inventory-locations-' . date('d-m-Y') . '.xlsx');
       
    }
    
   
}