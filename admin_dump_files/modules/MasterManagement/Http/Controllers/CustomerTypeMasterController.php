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
        $record->status = $request->status;
        $record->save();
    
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
        $model->update([
            'name' => $request->customer_type_name,
            'status' => $request->status,
            'updated_at'=> now() 
        ]);
    
        return response()->json(['success' => true, 'message' => 'Customer type updated successfully.']);
    }
    
        public function export_customer_type_master(Request $request)
    {
        
        
        $status = $request->status;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
         $selectedIds = json_decode($request->query('selected_ids', '[]'), true);
        
        
          return Excel::download(new CustomerTypeMasterExport($status,$from_date,$to_date , $selectedIds), 'customer-type-master-' . date('d-m-Y') . '.xlsx');
       
    }
    
}