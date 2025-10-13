<?php

namespace Modules\HRStatus\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\City\Entities\City;
use Modules\City\Entities\Area;
use App\Models\BgvComment;
use App\Models\BgvDocument;
use Modules\RiderType\Entities\RiderType;
use App\Models\HrQuery;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RiderOnboardingExport;
use App\Exports\RiderOnboardLogExport;
use Modules\HRStatus\Entities\RiderOnboardingList;
use Modules\HRStatus\Entities\RiderOnboardingLog;
use Modules\MasterManagement\Entities\CustomerMaster;
use Modules\MasterManagement\Entities\CustomerOperationalHub;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\AssetMaster\Entities\LocationMaster; //updated by Mugesh.B
use Illuminate\Support\Arr;

class RiderOnboardController extends Controller
{
    
    public function index(Request $request){
        
        $query = RiderOnboardingList::query();
    
        $status = $request->status ?? 'all';
        $timeline   = $request->timeline ?? '';
        
        if($request->status != "" && $request->status != 'all'){
            $ch_status = $request->status;
        }else{
            $ch_status = 'all';
        }
        
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
    
        if (in_array($status, ['deliveryman', 'adhoc','helper']) && $request->status != 'all') {
            
            $query->where('role_type', $status);
        }

        if ($timeline) {
            switch ($timeline) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
    
                case 'this_week':
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(), now()->endOfWeek()
                    ]);
                    break;
    
                case 'this_month':
                    $query->whereBetween('created_at', [
                        now()->startOfMonth(), now()->endOfMonth()
                    ]);
                    break;
    
                case 'this_year':
                    $query->whereBetween('created_at', [
                        now()->startOfYear(), now()->endOfYear()
                    ]);
                    break;
            }
        } else {
            // Manual date filtering
            if (!empty($from_date)) {
                $query->whereDate('created_at', '>=', $from_date);
            }
    
            if (!empty($to_date)) {
                $query->whereDate('created_at', '<=', $to_date);
            }
        }
    
        $lists = $query->orderBy('id', 'desc')->get();
        return view('hrstatus::rider_onboard.index', compact('lists','status', 'from_date', 'to_date','ch_status','timeline'));
    }
    
    public function onboard_log(Request $request){
        
        $query = RiderOnboardingLog::query();
    
        $status = $request->status ?? 'all';
        $dm_id = $request->dm_id ?? '';
        $c_id = $request->c_id ?? '';

        $timeline   = $request->timeline ?? '';
        
        if($request->status != "" && $request->status != 'all'){
            $ch_status = $request->status;
        }else{
            $ch_status = 'all';
        }
        
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
    
        if (in_array($status, ['deliveryman', 'adhoc','helper']) && $request->status != 'all') {
            
            $query->where('role_type', $status);
        }

        if ($timeline) {
            switch ($timeline) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
    
                case 'this_week':
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(), now()->endOfWeek()
                    ]);
                    break;
    
                case 'this_month':
                    $query->whereBetween('created_at', [
                        now()->startOfMonth(), now()->endOfMonth()
                    ]);
                    break;
    
                case 'this_year':
                    $query->whereBetween('created_at', [
                        now()->startOfYear(), now()->endOfYear()
                    ]);
                    break;
            }
        } else {

            if (!empty($from_date)) {
                $query->whereDate('created_at', '>=', $from_date);
            }
    
            if (!empty($to_date)) {
                $query->whereDate('created_at', '<=', $to_date);
            }
        }
        
        if(!empty($dm_id)){
            $query->where('dm_id',$dm_id);
        }
        if(!empty($c_id)){
            $query->where('customer_master_id',$c_id);
        }
    
        $lists = $query->orderBy('id', 'desc')->get();
        
        $customers = CustomerMaster::where('status', 1)
            ->select('id', 'name')
            ->get();
    
        $deliveryman_data = Deliveryman::where('delete_status', 0)
            ->whereNotNull('emp_id')
            ->where('rider_status', 1)
            ->whereNotNull('register_date_time')
            ->select('id', 'first_name', 'last_name','emp_id')
            ->get();
    
    
        return view('hrstatus::rider_onboard.rider_onboard_log', compact('lists','status','dm_id','c_id', 'from_date', 'to_date','ch_status','timeline','deliveryman_data','customers'));
    }
    
   public function create()
    {
        $customers = CustomerMaster::where('status', 1)
            ->get();
    
        $deliveryman_data = Deliveryman::where('delete_status', 0)
            ->whereNotNull('emp_id')
            ->where('rider_status', 1)
            ->whereNotNull('register_date_time')
            ->select('id', 'first_name', 'last_name','emp_id')
            ->get();
            
        
        $cities = LocationMaster::where('status' , 1)->get();
            
        
    
        return view('hrstatus::rider_onboard.rider_onboard_create', [
            'customers' => $customers,
            'deliveryman_data' => $deliveryman_data,
            'cities' =>$cities
        ]);
    }

    
    public function store(Request $request)
    {

        
        $validator = Validator::make($request->all(), [
            'role_type' => 'required|in:deliveryman,adhoc,helper',
            'id' => 'required|exists:ev_tbl_delivery_men,id',
            'name' => 'required', 
            'client_id' => 'required|exists:ev_tbl_customer_master,id',
            'client_name' => 'required', 
            'onboard_date' => 'required|date',
            'city' =>'required', 
            'hub' =>'required'
        ]);
    
        // Custom validation to check if DM already exists for the role_type
        $validator->after(function ($validator) use ($request) {
            $exists = RiderOnboardingList::where('dm_id', $request->id)
                        ->where('role_type', $request->role_type)
                        ->first();
    
            if ($exists) {
                $message = ($request->role_type === 'deliveryman') 
                            ? 'Rider already assigned.' 
                            : ucfirst($request->role_type) . ' already assigned.';
        
                $validator->errors()->add('id', $message);
            }
        });
    
    
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
        try {
            DB::beginTransaction();
    
            RiderOnboardingList::create([
                'role_type' => $request->role_type,
                'dm_id' => $request->id,
                'customer_master_id' => $request->client_id,
                'onboard_date' => $request->onboard_date,
                'city_id' => $request->city,
                'hub_id' => $request->hub,
                'created_by' => auth()->user()->id ?? null,
                'status' => 1,
            ]);
    
            RiderOnboardingLog::create([
                'role_type' => $request->role_type,
                'dm_id' => $request->id,
                'customer_master_id' => $request->client_id,
                'onboard_date' => $request->onboard_date,
                'city_id' => $request->city,
                'hub_id' => $request->hub,
                'remarks' => 'Onboarded successfully by ' . (auth()->user()->name ?? ''),
                'created_by' => auth()->user()->id ?? null,
                'status' => 1,
            ]);
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Rider onboarded successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    
    public function edit(Request $request){
  
        $edit_data = RiderOnboardingList::where('id',$request->edit_id)->first();
        if(!$edit_data){
            return back()->with('error','Data Not Found');
        }
        $customers = CustomerMaster::where('status', 1)
            ->get();
            
        $cities = LocationMaster::where('status' , 1)->get();
    
        $deliveryman_data = Deliveryman::where('delete_status', 0)
            ->whereNotNull('emp_id')
            // ->where('rider_status', 1)
            ->whereNotNull('register_date_time')
            ->where('work_type',$edit_data->role_type)
            ->select('id', 'first_name', 'last_name','emp_id','work_type')
            ->get();

        // dd($edit_data->role_type,$deliveryman_data);
        return view('hrstatus::rider_onboard.rider_onboard_edit',compact('edit_data','customers','deliveryman_data' ,'cities'));
    }
    
    // public function update(Request $request, $id)
    // {
    //     $update_data = RiderOnboardingList::find($id);
    
    //     if (!$update_data) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Data Not Found',
    //         ]);
    //     }
    
    //     $validator = Validator::make($request->all(), [
    //         'role_type' => 'required|in:deliveryman,adhoc,helper',
    //         'id' => 'required|exists:ev_tbl_delivery_men,id',
    //         'name' => 'required',
    //         'client_id' => 'required|exists:ev_tbl_customer_master,id',
    //         'client_name' => 'required',
    //         'onboard_date' => 'required|date',
    //     ]);
    
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }
    
    //     try {
    //         DB::beginTransaction();
    
    //         $update_data->update([
    //             'role_type' => $request->role_type,
    //             'dm_id' => $request->id,
    //             'customer_master_id' => $request->client_id,
    //             'onboard_date' => $request->onboard_date,
    //             'updated_by' => auth()->user()->id ?? null,
    //             'status' => 1,
    //         ]);
    
    //         RiderOnboardingLog::create([
    //             'role_type' => $request->role_type,
    //             'dm_id' => $request->id,
    //             'customer_master_id' => $request->client_id,
    //             'onboard_date' => $request->onboard_date,
    //             'remarks' => 'Onboarding details updated by ' . (auth()->user()->name ?? ''),
    //             'created_by' => auth()->user()->id ?? null,
    //             'status' => 1,
    //         ]);
    
    //         DB::commit();
    
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Rider onboarding details updated successfully!',
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Something went wrong!',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
    

    public function update(Request $request, $id)
    {
        $update_data = RiderOnboardingList::find($id);
    
        if (!$update_data) {
            return response()->json([
                'success' => false,
                'message' => 'Data Not Found',
            ]);
        }
    
        $validator = Validator::make($request->all(), [
            'role_type' => 'required|in:deliveryman,adhoc,helper',
            'id' => 'required|exists:ev_tbl_delivery_men,id',
            'name' => 'required',
            'client_id' => 'required|exists:ev_tbl_customer_master,id',
            'client_name' => 'required',
            'onboard_date' => 'required|date',
            'city' => 'required',
            'hub' =>'required'
        ]);
        
        // Custom validation for duplicate rider per role_type (excluding current record)
        $validator->after(function ($validator) use ($request, $id) {
            $exists = RiderOnboardingList::where('dm_id', $request->id)
                        ->where('role_type', $request->role_type)
                        ->where('id', '!=', $id)  // exclude current record
                        ->first();
        

        if ($exists) {
                $message = ($request->role_type === 'deliveryman') 
                            ? 'Rider already assigned.' 
                            : ucfirst($request->role_type) . ' already assigned.';
        
                $validator->errors()->add('id', $message);
            }
        });
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
        try {
            DB::beginTransaction();
    
            // Store original values for comparison
            $original = $update_data->only([
                'role_type',
                'dm_id',
                'customer_master_id',
                'onboard_date',
                'city_id',
               'hub_id'
            ]);
    
            // New incoming values
            $newData = [
                'role_type' => $request->role_type,
                'dm_id' => $request->id,
                'customer_master_id' => $request->client_id,
                'onboard_date' => $request->onboard_date,
                'city_id'  => $request->city ,
                'hub_id'   => $request->hub
                
            ];
    
            $remarks = 'Updated by ' . (auth()->user()->name ?? '') . '. ';
            foreach ($newData as $field => $newValue) {
                $oldValue = $original[$field];
                if ($oldValue != $newValue) {
                    if ($field == 'dm_id') {
                        $oldDm = Deliveryman::find($oldValue);
                        $newDm = Deliveryman::find($newValue);
            
                        $oldEmpId = $oldDm->emp_id ?? $oldValue;
                        $newEmpId = $newDm->emp_id ?? $newValue;
            
                        $remarks .= "DM ID: '{$oldEmpId}' → '{$newEmpId}', ";
                    } else {
                        $remarks .= ucfirst(str_replace('_', ' ', $field)) . ": '{$oldValue}' → '{$newValue}', ";
                    }
                }
            }

            // Remove trailing comma
            $remarks = rtrim($remarks, ', ');
    
            $update_data->update(array_merge($newData, [
                'updated_by' => auth()->user()->id ?? null,
                'status' => 1,
            ]));
    
            RiderOnboardingLog::create([
                'role_type' => $request->role_type,
                'dm_id' => $request->id,
                'customer_master_id' => $request->client_id,
                'onboard_date' => $request->onboard_date,
                'city_id'  => $request->city ,
                'hub_id'   => $request->hub,
                'remarks' => $remarks,
                'created_by' => auth()->user()->id ?? null,
                'status' => 1,
            ]);
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Rider onboarding details updated successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    
    
        
    public function rider_onboard_view(Request $request){
        
        $edit_data = RiderOnboardingList::where('id',$request->view_id)->first();
        if(!$edit_data){
            return back()->with('error','Data Not Found');
        }
        $customers = CustomerMaster::where('status', 1)
            ->get();
    
        $deliveryman_data = Deliveryman::where('delete_status', 0)
            ->whereNotNull('emp_id')
            ->where('rider_status', 1)
            ->whereNotNull('register_date_time')
            ->select('id', 'first_name', 'last_name','emp_id')
            ->get();
            
         $cities = LocationMaster::where('status' , 1)->get();
         
          $hubs = CustomerOperationalHub::where('status' , 1)->get();
          
    
        return view('hrstatus::rider_onboard.rider_onboard_view',compact('edit_data','customers','deliveryman_data' ,'cities' ,'hubs'));
    }
    
        public function rider_onboard_view_log(Request $request){
        
        $edit_data = RiderOnboardingLog::where('id',$request->view_id)->first();
        if(!$edit_data){
            return back()->with('error','Data Not Found');
        }
        $customers = CustomerMaster::where('status', 1)
            ->get();
    
        $deliveryman_data = Deliveryman::where('delete_status', 0)
            ->whereNotNull('emp_id')
            ->where('rider_status', 1)
            ->whereNotNull('register_date_time')
            ->select('id', 'first_name', 'last_name','emp_id')
            ->get();
            
                 $cities = LocationMaster::where('status' , 1)->get();
         
          $hubs = CustomerOperationalHub::where('status' , 1)->get();
    
        return view('hrstatus::rider_onboard.rider_onboard_view_log',compact('edit_data','customers','deliveryman_data' ,'cities' ,'hubs'));
    }
    
    public function destroy(Request $request,$id)
    {
        $record = RiderOnboardingList::find($id);
    
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Rider onboarding data not found.',
            ], 404);
        }
    
        try {
            DB::beginTransaction();
    
            RiderOnboardingLog::create([
                'role_type' => $record->role_type,
                'dm_id' => $record->dm_id,
                'customer_master_id' => $record->customer_master_id,
                'onboard_date' => $record->onboard_date,
                'remarks' => 'Deleted by ' . (auth()->user()->name ?? 'system'),
                'created_by' => auth()->user()->id ?? null,
                'status' => 1, 
            ]);
    
            $record->delete();
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Rider onboarding data deleted successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete rider onboarding data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
        public function fetch_hubsdetail(Request $request)
    {

        if (!isset($request->id) || $request->id == "") {
            return response()->json([
                'success' => false,
                'message' => 'Client Name field is required.',
            ], 404);
        }
    
        $data = CustomerMaster::with('operationalHubs')->where('id',$request->id)->get();

            return response()->json([
                'success' => true,
                'message' => 'Hubs data fetched successfully',
                'data'=>$data,
                'count'=>count($data)
            ]);
       
    }
    

    public function fetch_riderdetail(Request $request)
    {
        // dd($request->all());

        if (!isset($request->type) || $request->type == "") {
            return response()->json([
                'success' => false,
                'message' => 'Rider Type field is required.',
            ], 404);
        }
    
        $data = Deliveryman::where('delete_status', 0)
            ->whereNotNull('emp_id')
            // ->where('rider_status', 1)
            ->whereNotNull('register_date_time')
            ->select('id', 'first_name', 'last_name','emp_id')
            ->where('work_type',$request->type)
            ->get();

            return response()->json([
                'success' => true,
                'message' => 'Rider Type data fetched successfully',
                'data'=>$data,
                'count'=>count($data)
            ]);
       
    }
    
    public function export_rider_onboarding(Request $request){
        
        $selectedIds = json_decode($request->query('selected_ids', '[]'), true);
        $status = $request->status;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $timeline = $request->timeline;
        $selectedFields = json_decode($request->query('fields', '[]'), true);
        // dd($status, $from_date, $to_date, $timeline, $selectedIds, $selectedFields);
        return Excel::download(
            new RiderOnboardingExport($status, $from_date, $to_date, $timeline, $selectedIds, $selectedFields),
            'Rider-Onboarding-Lists.xlsx'
        );
        
    }
    
     public function export_rider_onboard_log(Request $request){
        
        $selectedIds = json_decode($request->query('selected_ids', '[]'), true);
        $status = $request->status;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $timeline = $request->timeline;
        $dm_id = $request->dm_id;
        $c_id = $request->c_id;
        $selectedFields = json_decode($request->query('fields', '[]'), true);
        // dd($status, $from_date, $to_date, $timeline, $selectedIds, $selectedFields);
        return Excel::download(
            new RiderOnboardLogExport($status, $from_date, $to_date, $timeline, $selectedIds, $selectedFields,$dm_id,$c_id),
            'Rider-Onboarding-Lists.xlsx'
        );
        
    }

}
