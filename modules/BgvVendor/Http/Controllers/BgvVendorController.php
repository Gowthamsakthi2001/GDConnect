<?php

namespace Modules\BgvVendor\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\BgvVendor\Entities\BgvDeliverymanAssignment;
use App\Models\BgvComment;
use App\Models\BgvDocument;
use App\Models\HrQuery;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Modules\City\Entities\City;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class BgvVendorController extends Controller
{

   public function show_bgv_dashboard(Request $request)
    {
        $cities = City::where('status',1)->get();
        $login_user_role = auth()->user()->role ?? '';
        
        $city_id = $request->city_id ?? '';
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
        
        $total_application = Deliveryman::where('delete_status', 0);
        if (!empty($from_date) && !empty($to_date)) {
            $total_application->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $total_application->where('current_city_id', $city_id);
        }
        $total_application_count = $total_application->get()->count();
        
        $pending_application = Deliveryman::where('delete_status', 0);
        if (!empty($from_date) && !empty($to_date)) {
            $pending_application->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $pending_application->where('current_city_id', $city_id);
        }
        $pending_application_count = $pending_application->where('kyc_verify', 0)->get()->count();
        
        $completed_application = Deliveryman::where('delete_status', 0);
        if (!empty($from_date) && !empty($to_date)) {
            $completed_application->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $completed_application->where('current_city_id', $city_id);
        }
        $completed_application_count = $completed_application->where('kyc_verify', 1)->get()->count();
        
        $rejected_application = Deliveryman::where('delete_status', 0);
        if (!empty($from_date) && !empty($to_date)) {
            $rejected_application->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $rejected_application->where('current_city_id', $city_id);
        }
        $rejected_application_count = $rejected_application->where('kyc_verify', 2)->get()->count();
        
        $hold_application = Deliveryman::where('delete_status', 0);
        if (!empty($from_date) && !empty($to_date)) {
            $hold_application->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $hold_application->where('current_city_id', $city_id);
        }
        $hold_application_count = $hold_application->where('kyc_verify', 3)->get()->count();
        
        $total_hr_approve = Deliveryman::where('delete_status', 0)->whereNotNull('approved_status')->where('rider_status', 3);
        if (!empty($from_date) && !empty($to_date)) {
            $total_hr_approve->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $total_hr_approve->where('current_city_id', $city_id);
        }
        $total_hr_approve_count = $total_hr_approve->get()->count();
        
        $total_hr_probation = Deliveryman::where('delete_status', 0)->whereNotNull('approved_status')->where('rider_status', 3);
        if (!empty($from_date) && !empty($to_date)) {
            $total_hr_probation->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $total_hr_probation->where('current_city_id', $city_id);
        }
        $total_hr_probation_count = $total_hr_probation->get()->count();
        
        $total_hr_reject = Deliveryman::where('delete_status', 0)->whereNotNull('approved_status')->where('rider_status', 2);
        if (!empty($from_date) && !empty($to_date)) {
            $total_hr_reject->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $total_hr_reject->where('current_city_id', $city_id);
        }
        $total_hr_reject_count = $total_hr_reject->get()->count();
        
        $total_hr_live = Deliveryman::where('delete_status', 0)->whereNotNull('approved_status')->where('rider_status', 1);
        if (!empty($from_date) && !empty($to_date)) {
            $total_hr_live->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $total_hr_live->where('current_city_id', $city_id);
        }
        $total_hr_live_count = $total_hr_live->get()->count();
        
        // Percentage calculations (rounded to 2 decimal places)
        $pending_percentage   = $total_application_count > 0 ? round(($pending_application_count / $total_application_count) * 100, 2) : 0;
        $completed_percentage = $total_application_count > 0 ? round(($completed_application_count / $total_application_count) * 100, 2) : 0;
        $rejected_percentage  = $total_application_count > 0 ? round(($rejected_application_count / $total_application_count) * 100, 2) : 0;
        $hold_percentage      = $total_application_count > 0 ? round(($hold_application_count / $total_application_count) * 100, 2) : 0;
        $total_application_percentage = $pending_percentage + $completed_percentage + $rejected_percentage + $hold_percentage;

        
        $hr_approve_percentage      = $total_application_count > 0 ? round(($total_hr_approve_count / $total_application_count) * 100, 2) : 0;
        $hr_probation_percentage      = $total_application_count > 0 ? round(($total_hr_probation_count / $total_application_count) * 100, 2) : 0;
        $hr_reject_percentage      = $total_application_count > 0 ? round(($total_hr_reject_count / $total_application_count) * 100, 2) : 0;  
        $hr_live_percentage      = $total_application_count > 0 ? round(($total_hr_live_count / $total_application_count) * 100, 2) : 0;  
        

        $todays_application = Deliveryman::whereDate('register_date_time', Carbon::today())
            ->where('delete_status', 0);
        
        if (!empty($city_id)) {
            $todays_application->where('current_city_id', $city_id);
        }
        
        $todays_applications = $todays_application->count();
        
        
    $bgv_pending_count = 0;
    $bgv_ageing_pending_count = 0;
    
    Deliveryman::where('delete_status', 0)
        ->where('kyc_verify', 0)
        // ->when(!empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {
        //     $query->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        // })
        // ->when(!empty($city_id), function ($query) use ($city_id) {
        //     $query->where('current_city_id', $city_id);
        // })
        ->chunk(1000, function ($bgv_pending_applications) use (&$bgv_pending_count, &$bgv_ageing_pending_count) {
            foreach ($bgv_pending_applications as $val) {
                $created_date = \Carbon\Carbon::parse($val->register_date_time);
                $current_date = \Carbon\Carbon::now();
                $ageing_days = $current_date->diffInDays($created_date);
    
                if ($ageing_days > 7) {
                    $bgv_ageing_pending_count += 1;
                } else {
                    $bgv_pending_count += 1;
                }
            }
        });
        
        $total_employee_count = Deliveryman::where('delete_status', 0)->where('work_type','in-house')->count();
        $total_rider_count = Deliveryman::where('delete_status', 0)->where('work_type','deliveryman')->count();
        $total_adhoc_count = Deliveryman::where('delete_status', 0)->where('work_type','adhoc')->count();
        $total_vehicle_count = DB::table('ev_modal_vehicles')->where('status',1)->get()->count();
        
          return view('bgvvendor::show_bgv_dashboard',compact('login_user_role','cities','city_id','from_date','to_date','total_application_count','pending_application_count',
            'completed_application_count','rejected_application_count','hold_application_count','pending_percentage','completed_percentage','rejected_percentage','hold_percentage',
            'todays_applications','total_application_percentage','bgv_pending_count','bgv_ageing_pending_count'));
    }
    
    
    
    
    public function summary(Request $request)
    {

        
        $from_date = $request->from_date ?? '';
        $to_date   = $request->to_date ?? '';
        $timeline  = $request->timeline ?? '';
        
        // Base query with common filters
        $baseQuery = Deliveryman::where('delete_status', 0);
        
        // Timeline filter
        if (!empty($timeline)) {
            switch ($timeline) {
                case 'today':
                    $baseQuery->whereDate('created_at', Carbon::today());
                    break;
        
                case 'this_day': // This Week
                    $baseQuery->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek(),
                    ]);
                    break;
        
                case 'this_month':
                    $baseQuery->whereMonth('created_at', Carbon::now()->month)
                              ->whereYear('created_at', Carbon::now()->year);
                    break;
        
                case 'this_year':
                    $baseQuery->whereYear('created_at', Carbon::now()->year);
                    break;
            }
        }
        
        // Custom date range filter
        if (!empty($from_date) && !empty($to_date)) {
            $baseQuery->whereBetween('created_at', [
                Carbon::parse($from_date)->startOfDay(),
                Carbon::parse($to_date)->endOfDay(),
            ]);
        }
        
        // Clone and apply status-wise counts
        $pending_count  = (clone $baseQuery)->where('kyc_verify', 0)->count();
        $complete_count = (clone $baseQuery)->where('kyc_verify', 1)->count();
        $reject_count   = (clone $baseQuery)->where('kyc_verify', 2)->count();
        $hold_count     = (clone $baseQuery)->where('kyc_verify', 3)->count();
        
        // Optional: total after all filters
        $total_count = (clone $baseQuery)->count();
        

        return view('bgvvendor::summary_bgv' , compact('total_count' , 'pending_count' ,'complete_count' ,'reject_count' ,'hold_count'));
    }

   
  public function bgv_verification_list(Request $request , $type)
    {
        
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
        $roll_type = $request->roll_type ?? '';
        $city_id = $request->city_id ?? '';
        $bgv_status = $request->bgv_status ?? '';
        $query = Deliveryman::where('delete_status', 0);
    
        if (!empty($roll_type)) {
            $query->where('work_type', $roll_type);
        }
    
        if (!empty($from_date) && !empty($to_date)) {
            $query->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        
        if (!empty($city_id)) {
            $query->where('current_city_id', $city_id);
        }
        // if (!empty($bgv_status)) {
        //     $query->where('kyc_verify', $bgv_status);
        // }
        
         // ✅ type to kyc_verify mapping
        $statusMap = [
            'pending_application' => 0,
            'complete_application' => 1,
            'reject_application' => 2,
            'hold_application' => 3,
        ];

        if (array_key_exists($type, $statusMap)) {
            $query->where('kyc_verify', $statusMap[$type]);
        }


        $cities = City::where('status',1)->get();
        $lists = $query->orderBy('id', 'desc')->get();
    // dd($query->toSql(), $query->getBindings(),$from_date,$to_date);
        return view('bgvvendor::recruiters_bgv_lists', compact('lists', 'cities','roll_type', 'from_date', 'to_date','city_id','bgv_status' ,'type'));
    }

    
    // public function bgv_verification_list(Request $request , $type)
    // {
    //     $from_date = $request->from_date ?? '';
    //     $to_date = $request->to_date ?? '';
    //     $roll_type = $request->roll_type ?? '';
    //     $city_id = $request->city_id ?? '';
    //     $bgv_status = $request->bgv_status ?? '';
    
    //     $query = BgvDeliverymanAssignment::with('delivery_man')
    //             ->whereHas('delivery_man', function ($q) {
    //         $q->where('delete_status', 0);
    //     });
        
    //     // ✅ Filter by work_type inside delivery_man relation
    //     if (!empty($roll_type)) {
    //         $query->whereHas('delivery_man', function ($q) use ($roll_type) {
    //             $q->where('work_type', $roll_type);
    //         });
    //     }
    
    //     // ✅ Filter by register date inside delivery_man
    //     if (!empty($from_date) && !empty($to_date)) {
    //         $query->whereHas('delivery_man', function ($q) use ($from_date, $to_date) {
    //             $q->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
    //         });
    //     }
    
    //     // ✅ Filter by KYC verify status based on $type
    //     $statusMap = [
    //         'pending_application' => 0,
    //         'complete_application' => 1,
    //         'reject_application' => 2,
    //         'hold_application' => 3,
    //     ];
    
    //     // if (array_key_exists($type, $statusMap)) {
    //     //     $query->whereHas('delivery_man', function ($q) use ($statusMap, $type) {
    //     //         $q->where('kyc_verify', $statusMap[$type]);
    //     //     });
    //     // }
    
    //         if (!empty($city_id)) {
    //         $query->whereHas('delivery_man', function ($q) use ($city_id) {
    //             $q->where('current_city_id', $city_id);
    //         });
    //     }
    
    //     $cities = City::where('status',1)->get();
    //     $lists = $query->orderBy('id', 'desc')->get();
    
    //     return view('bgvvendor::recruiters_bgv_lists', compact(
    //         'lists',
    //         'cities',
    //         'roll_type',
    //         'from_date',
    //         'to_date',
    //         'city_id',
    //         'bgv_status',
    //         'type'
    //     ));
    // }

    
    public function bgv_document_verify(Request $request,$id)
    {

        $dm = Deliveryman::where('delete_status', 0)->where('id',$id)->first();
        if (!$dm) {
             return back()->with('error', 'data not found');
        }

        return view('bgvvendor::bgv_document_verify', compact('dm'));
    }
    
    public function bgv_comment_store(Request $request)
    {
        try {
            $dm = Deliveryman::find($request->dm_id);
            if (!$dm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rider not found.'
                ]);
            }
            
            if ($request->remarks == '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Remarks field is required. Please enter a comment.'
                ]);
            }
            $comment = new BgvComment();
            $comment->dm_id = $request->dm_id;
            $comment->bgv_status = $request->bgv_status;
            $comment->remarks = $request->remarks;
            $comment->comment_type = 'bgv_vendor';
            $comment->bgv_id = auth()->id();
            $comment->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Comments Added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
    
    
    
     public function bgv_document_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doc_dm_id' => 'required|exists:ev_tbl_delivery_men,id',
            'documents' => 'required|array',
            // 'documents.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', 
            'documents.*' => 'file|max:5120', 
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            $dm = Deliveryman::find($request->doc_dm_id);
    
            $fileNames = [];
            foreach ($request->file('documents') as $file) {
                $name = uniqid('bgv_', true) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('EV/bgv_upload_docs'), $name);
                $fileNames[] = $name;
            }
    
            $doc = new BgvDocument();
            $doc->dm_id = $dm->id;
            $doc->documents = implode(',', $fileNames);
            $doc->doc_type = 'bgv_vendor';
            $doc->bgv_id = auth()->id();
            $doc->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Documents uploaded successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }
    }

    public function dashboard_filter_data(Request $request,$type)
    {
        $type = $request->type ?? '';
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
        
        $roll_type = $request->roll_type ?? '';
        $city_id = $request->city_id ?? '';
        $bgv_status = $request->bgv_status ?? '';
        
        $query = Deliveryman::where('delete_status', 0);
    
        if (!empty($from_date) && !empty($to_date)) {
            $query->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        
        if(!empty($type) && $type == "complete_application"){
            $query->where('kyc_verify',1);
        }
        
        if(!empty($type) && $type == "rejected_application"){
            $query->where('kyc_verify',2);
        }
        
        if(!empty($type) && $type == "pending_application"){
            $query->where('kyc_verify',0);
        }
        
        if(!empty($type) && $type == "hold_application"){
            $query->where('kyc_verify',3);
        }
        
        if (!empty($city_id)) {
            $query->where('current_city_id', $city_id);
        }
        if (!empty($bgv_status)) {
            $query->where('kyc_verify', $bgv_status);
        }
       
    
        $lists = $query->orderBy('id', 'desc')->get();
        // dd($query->toSql(), $query->getBindings(),$from_date,$to_date);

        $cities = City::where('status',1)->get();
        
        return view('bgvvendor::dashboard_filter_data', compact('lists','cities','type', 'from_date', 'to_date','roll_type','city_id','bgv_status'));
    }
    
     public function recruiter_query_list(Request $request,$id)
    {

        $dm = Deliveryman::where('delete_status', 0)->where('id',$id)->first();
        if (!$dm) {
            return back()->with('error', 'Rider Not found');
        }
        $quries = HrQuery::where('dm_id',$dm->id)->where('query_type','hr_query')->get();

        return view('bgvvendor::hr_query_view', compact('dm','quries'));
    }
    
}
