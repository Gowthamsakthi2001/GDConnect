<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\Deliveryman\Entities\DeliveryManLogs;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\File;
use Modules\Deliveryman\Entities\OtpVerification;
use Illuminate\Support\Str;
use Modules\Leads\Entities\leads;
use App\Models\EvGlobalAadhaarNo;
use App\Models\EvDeliveryMan;
use App\Models\Holiday;
use App\Models\EvDeliveryManLogs;
use App\Models\EvGlobalAadhaarResponse;
use Illuminate\Support\Facades\Http;
use Modules\LeaveManagement\Entities\LeaveType;
use Modules\LeaveManagement\Entities\LeaveRequest;
use App\Helpers\CustomHandler;

class EvLeaveManagementContoller extends Controller
{
    public function leave_type_list(Request $request)
    {
            $leaves = LeaveType::where('status', 1)->get()->map(function ($leave) {
                return [
                    'id' => $leave->id,
                    'leave_name' => $leave->leave_name,
                    'short_name' => $leave->short_name,
                    'days' => $leave->days,
                    'created_at' => Carbon::parse($leave->created_at)->format('d-m-Y H:i:s'),
                    'updated_at' => Carbon::parse($leave->updated_at)->format('d-m-Y H:i:s'),
                ];
            });
        if($leaves->isNotEmpty()){
            return response()->json(['success' => true,'message' => 'types of leave fetched successfully.', 'data' => $leaves,], 200); 
        }
        return response()->json(['success' => false,'message' => 'Data Not Found', 'data' => null,], 400); 
    }
    
    public function new_leave_request(Request $request){
        $validator = Validator::make($request->all(), [
            'dm_id' => 'required|exists:ev_tbl_delivery_men,id',
            'leave_id' => 'required|exists:ev_leave_types,id',
            'start_date' => 'required|date_format:Y-m-d H:i:s',
            'end_date' => 'required|date_format:Y-m-d H:i:s|after_or_equal:start_date',
            'remarks' => 'required'
        ], [
            'dm_id.required' => 'The deliveryman field is required',
            'dm_id.exists' => 'The selected deliveryman does not exist',
            'leave_id.required' => 'The Leave field is required',
            'leave_id.exists' => 'The selected leave type does not exist',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $leave = LeaveType::where('id',$request->leave_id)->where('status', 1)->first();
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        $get_day_count = $start_date->diffInDays($end_date) + 1;
        
        $leave_request = LeaveRequest::where('leave_id',$request->leave_id)->where('dm_id',$request->dm_id)->whereNull('approve_status')->whereNull('reject_status')->first();
        if($leave_request){
            return response()->json(['success' => true,'message' => 'Thank you for contact us. We will contact you as soon as possible'], 200);
        }
        
        $leave_request = LeaveRequest::where('leave_id',$request->leave_id)->where('dm_id',$request->dm_id)->where('approve_status', 1)->first();

       if ($leave_request) {
            $remain_day = abs($leave_request->apply_days - $leave->days);
            
            if ($get_day_count >= $leave_request->apply_days) {
                return response()->json([
                    'success' => false, 
                    'message' => "You have already taken {$leave_request->apply_days} days for {$leave->leave_name}. You can only apply for {$remain_day} days."
                ], 200);
            }
        }
        
        if ($get_day_count > $leave->days) {
            return response()->json([
                'success' => false, 
                'message' => "{$leave->leave_name} Maximum leave allowed for {$leave->days} days."
            ], 200);
        }
        
        $leave_req = new LeaveRequest();
        $leave_req->dm_id = $request->dm_id;
        $leave_req->leave_id = $request->leave_id;
        $leave_req->req_status = 1;
        $leave_req->apply_days = $get_day_count;
        $leave_req->start_date = $request->start_date;
        $leave_req->end_date = $request->end_date;
        $leave_req->remarks = $request->remarks;
        $leave_req->save();

        if (!$leave_req->save()) {
            return response()->json(['success' => false, 'message' => 'The network connection has failed. Please try again later.'], 200);
        }
        $this->leave_apply_whatsapp_message($request,$get_day_count,'admin');
        $this->leave_apply_whatsapp_message($request,$get_day_count,'employee');
        return response()->json([
            'success' => true,
            'message' => 'Thank you for submitting. We will contact you as soon as possible.'
        ], 200);
    }
    
    public function permission_new_request(Request $request){
        $validator = Validator::make($request->all(), [
            'dm_id' => 'required|exists:ev_tbl_delivery_men,id',
            'leave_id' => 'required|exists:ev_leave_types,id',
            'permission_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'remarks' => 'required'
        ], [
            'dm_id.required' => 'The deliveryman field is required',
            'dm_id.exists' => 'The selected deliveryman does not exist',
            'leave_id.required' => 'The Leave field is required',
            'leave_id.exists' => 'The selected leave type does not exist',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $time = $request->start_time;
        $formatted_time = Carbon::parse($time)->format('H:i:s');
        $time_end = $request->end_time;
        $formatted_time_end = Carbon::parse($time_end)->format('H:i:s');
        $start_time = Carbon::parse($formatted_time);
        $end_time = Carbon::parse($formatted_time_end);
        $get_time_count = $start_time->diffInMinutes($end_time) / 60;
        $leave = LeaveType::where('id',$request->leave_id)->where('status', 1)->first();
  
        if ($leave->leave_type == 'day') {
            return response()->json([
                'success' => false, 
                'message' => 'Sorry, this type of leave permission is not accepted. Please contact your administrator.'
            ], 400);
        }
        
        if($get_time_count > $leave->days){
             return response()->json([
                'success' => false, 
                'message' => 'Sorry, this permission is not accepted. Please contact your administrator.'
            ], 400);
        }
        
        $exist_permission = LeaveRequest::where('leave_id',$request->leave_id)->where('dm_id',$request->dm_id)->where('permission_date',$request->permission_date)->first();
        if($exist_permission){
             return response()->json([
                'success' => false, 
                'message' => 'Sorry, you have already applied. We will contact you as soon as possible'
            ], 400);
        }
        
        $leave_request = LeaveRequest::where('leave_id',$request->leave_id)->where('dm_id',$request->dm_id)->where('req_status', 1)->first();
        if($leave_request){
            return response()->json(['success' => true,'message' => 'Thank you for contact us. We will contact you as soon as possible'], 200);
        }
        $leave_req = new LeaveRequest();
        $leave_req->dm_id = $request->dm_id;
        $leave_req->leave_id = $request->leave_id;
        $leave_req->req_status = 1;
        $leave_req->permission_date = $request->permission_date;
        $leave_req->start_time = $request->start_time;
        $leave_req->end_time = $request->end_time;
        $leave_req->permission_hr = $get_time_count;
        $leave_req->remarks = $request->remarks;
        $leave_req->save();

        if (!$leave_req->save()) {
            return response()->json(['success' => false, 'message' => 'The network connection has failed. Please try again later.'], 200);
        }
        $this->permission_apply_whatsapp_message($request,$get_time_count,'admin');
        $this->permission_apply_whatsapp_message($request,$get_time_count,'employee');
        return response()->json([
            'success' => true,
            'message' => 'Thank you for submitting. We will contact you as soon as possible.'
        ], 200);
    }
    
    public function leave_count_summary(Request $request,$id){
    
        $dm = Deliveryman::where('id',$id)->first();
        if (!$dm) {
            return response()->json(['success' => false,'message' => 'Deliveryman Not Found'], 404); 
        }
        $year = date('Y');
        $totalDays = LeaveType::whereYear('created_at',$year)->sum('days');
        $taken_leaves = LeaveRequest::whereYear('created_at',$year)->with('leave')->where('dm_id',$id)->where('approve_status',1)->whereNull('req_status')->get();
        
        $taken_leave_count = 0;
        if($taken_leaves){
            foreach($taken_leaves as $leave){
                $taken_leave_count += $leave->leave->days;
            }
        }
        $balan_leave = $totalDays - $taken_leave_count;
        $cancelled_leave = LeaveRequest::whereYear('created_at',$year)->where('dm_id',$id)->where('reject_status',1)->whereNull('req_status')->get()->count();
        $count = [];
        $count['total_leave'] = $totalDays;
        $count['taken_leave'] = $taken_leave_count;
        $count['balance_leave'] = $balan_leave;
        $count['cancelled_leave'] = $cancelled_leave;
        
        $leaves_days_only = LeaveType::where('status', 1)->get()->map(function ($leave) {
                return [
                    'id' => $leave->id,
                    'leave_name' => $leave->leave_name,
                    'short_name' => $leave->short_name,
                    'days' => $leave->days,
                    'type'=>$leave->leave_type,
                    'created_at' => Carbon::parse($leave->created_at)->format('d-m-Y H:i:s'),
                    'updated_at' => Carbon::parse($leave->updated_at)->format('d-m-Y H:i:s'),
                ];
        });
        
        // $leaves_hour_only = LeaveType::where('status', 1)->where('leave_type','hour')->get()->map(function ($leave) {
        //         return [
        //             'id' => $leave->id,
        //             'leave_name' => $leave->leave_name,
        //             'short_name' => $leave->short_name,
        //             'type'=>$leave->leave_type,
        //             'created_at' => Carbon::parse($leave->created_at)->format('d-m-Y H:i:s'),
        //             'updated_at' => Carbon::parse($leave->updated_at)->format('d-m-Y H:i:s'),
        //         ];
        // });
            
        return response()->json(['success' => true,'message' => 'leave summary fetched successfully.', 
        'leave_summary' => $count,
        'types_of_leave'=>$leaves_days_only], 200); 
    }
    
     public function deliveryman_approve_reject_list(Request $request, $id)
    {
        $dm = Deliveryman::where('id', $id)->first();
        if (!$dm) {
            return response()->json(['success' => false, 'message' => 'Deliveryman Not Found'], 404);
        }
    
        $approved_leaves = LeaveRequest::with('leave')
            ->where('dm_id', $id)
            // ->where('approve_status', 1)
            ->whereNull('req_status')
            ->get()
            ->map(function ($data) {
                return [
                    'id' => $data->id,
                    'leave_name' => $data->leave->leave_name ?? null,
                    'short_name' => $data->leave->short_name ?? null,
                    'day_or_hour' => $data->apply_days . '' . ($data->leave->leave_type ?? ''),
                    'start_date' => Carbon::parse($data->start_date)->format('d-m-Y H:i:s'),
                    'end_date' => Carbon::parse($data->end_date)->format('d-m-Y H:i:s'),
                    'status'=>$data->approve_status== 1 ? 'approved' : 'rejected',
                    'created_at' => Carbon::parse($data->created_at)->format('d-m-Y H:i:s'),
                    'updated_at' => Carbon::parse($data->updated_at)->format('d-m-Y H:i:s'),
                ];
            });
    
        return response()->json(['success' => true, 'message' => 'leave list fetched successfully.', 'data' => $approved_leaves], 200);
    }
    
    public function deliveryman_leave_pending_list(Request $request, $id)
    {
        $dm = Deliveryman::where('id', $id)->first();
        if (!$dm) {
            return response()->json(['success' => false, 'message' => 'Deliveryman Not Found'], 404);
        }
    
        $pending_leaves = LeaveRequest::with('leave')
            ->where('dm_id', $id)
            ->whereNotNull('req_status')
            ->get()
            ->map(function ($data) {
                return [
                    'id' => $data->id,
                    'leave_name' => $data->leave->leave_name ?? null,
                    'short_name' => $data->leave->short_name ?? null,
                    'day_or_hour' => $data->apply_days . '' . ($data->leave->leave_type ?? ''),
                    'start_date' => Carbon::parse($data->start_date)->format('d-m-Y H:i:s'),
                    'end_date' => Carbon::parse($data->end_date)->format('d-m-Y H:i:s'),
                    'status'=> 'pending',
                    'created_at' => Carbon::parse($data->created_at)->format('d-m-Y H:i:s'),
                    'updated_at' => Carbon::parse($data->updated_at)->format('d-m-Y H:i:s'),
                ];
            });
    
        return response()->json(['success' => true, 'message' => 'pending list fetched successfully.', 'data' => $pending_leaves], 200);
    }
   
// public function filter_leave_present(Request $request, $id)
// {
//     $dm = Deliveryman::where('id', $id)->first();
//     if (!$dm) {
//         return response()->json(['success' => false, 'message' => 'Deliveryman Not Found'], 404);
//     }

//     $year = date('Y');
//     $this_month = date('m');
//     if ($request->month && $request->year) {
//         $year = $request->year;
//         $this_month = $request->month;  
//     }

//     // Get approved leaves
//     $taken_leaves = LeaveRequest::whereYear('created_at', $year)
//         ->whereMonth('created_at', $this_month)
//         ->with('leave')
//         ->where('dm_id', $id)
//         ->where('approve_status', 1)
//         ->whereNull('req_status')
//         ->get();

//     $leave_dates = [];
//     $leave_dates_only = []; // for in_array check

//     foreach ($taken_leaves as $leave) {
//         $start = new \DateTime($leave->start_date);
//         $end = new \DateTime($leave->end_date);

//         while ($start <= $end) {
//             $date = $start->format('Y-m-d');
//             $leave_dates[] = [
//                 'date' => $date,
//                 'type' => 'leave'
//             ];
//             $leave_dates_only[] = $date;
//             $start->modify('+1 day');
//         }
//     }

//     // Get holidays
//     $holidays = Holiday::select('date')
//                 ->whereYear('date', $year)
//                 ->whereMonth('date', $this_month)
//                 ->where('is_active', 1)
//                 ->get();

//     $holiday_dates = [];
//     $holiday_dates_only = [];

//     foreach ($holidays as $holiday) {
//         $date = $holiday->date->format('Y-m-d');
//         $holiday_dates[] = [
//             'date' => $date,
//             'type' => 'holiday'
//         ];
//         $holiday_dates_only[] = $date;
//     }

//     // Get punched days
//     $total_punched_days_raw = EvDeliveryManLogs::select('punched_in')
//                         ->where('user_id', $id)
//                         ->whereYear('punched_in', $year)
//                         ->whereMonth('punched_in', $this_month)
//                         ->get();

//     $total_punched_days = [];
//     $punched_dates_only = [];

//     foreach ($total_punched_days_raw as $log) {
//         $date = \Carbon\Carbon::parse($log->punched_in)->format('Y-m-d');
//         $total_punched_days[] = [
//             'punched_in' => $date,
           
//         ];
//         $punched_dates_only[] = $date;
//     }

//     // Build full month period
//         $today = \Carbon\Carbon::today();
//         $currentYear = $today->year;
//         $currentMonth = $today->month;
        
//         $startOfMonth = \Carbon\Carbon::create($year, $this_month, 1);
        
//         // Handle future months
//         if ($year > $currentYear || ($year == $currentYear && $this_month > $currentMonth)) {
//             $absent_dates = []; // future month → no absents
//         } else {
//             // If current month/year → only till today, else full month
//             if ($year == $currentYear && $this_month == $currentMonth) {
//                 $endOfMonth = $today;
//             } else {
//                 $endOfMonth = $startOfMonth->copy()->endOfMonth();
//             }
        
//             $period = \Carbon\CarbonPeriod::create($startOfMonth, $endOfMonth);
        
//             $absent_dates = [];
//             foreach ($period as $date) {
//                 $d = $date->format('Y-m-d');
//                 if (
//                     !in_array($d, $leave_dates_only) &&
//                     !in_array($d, $holiday_dates_only) &&
//                     !in_array($d, $punched_dates_only)
//                 ) {
//                     $absent_dates[] = [
//                         'date' => $d,
//                         'type' => 'not_punched_out'
//                     ];
//                 }
//             }
//         }

//     return response()->json([
//         'success' => true,
//         'message' => 'This month leave list fetched successfully.',
//         'data' => [
//             "approved_leave_days" => $leave_dates,
//             "not_punched_days" => $absent_dates,
//             "total_punched_days" => $total_punched_days,
//             "holiday" => $holiday_dates,
//             "approval_date_time" => $dm->as_approve_datetime ?? null,
//         ]
//     ], 200);
// }
    
    public function filter_leave_present(Request $request, $id)
    {
        $dm = Deliveryman::where('id', $id)->first();
        if (!$dm) {
            return response()->json(['success' => false, 'message' => 'Deliveryman Not Found'], 404);
        }
        
        $year = date('Y');
        $this_month = date('m');
        if($request->month && $request->year){
            $year = $request->year;
            $this_month = $request->month;  
        }
        
        $taken_leaves = LeaveRequest::whereYear('created_at', $year)
            ->whereMonth('created_at', $this_month)
            ->with('leave')
            ->where('dm_id', $id)
            ->where('approve_status', 1)
            ->whereNull('req_status')
            ->get();
        
        $leave_dates = [];
        $holiday_dates = [];
        $not_punched_dates = [];
        $holidays = Holiday::select('date')
                    ->whereYear('date',$year)
                    ->whereMonth('date', $this_month)
                    ->where('is_active',1)
                    ->get();
        
        $days_not_punched = EvDeliveryManLogs::select('punched_in')
                            ->where('user_id', $id)
                            ->whereYear('punched_in', $year)
                            ->whereMonth('punched_in', $this_month)
                            ->whereNull('punched_out')
                            ->get(); 
      
        $total_punched_days = EvDeliveryManLogs::select('punched_in')
                            ->where('user_id', $id)
                            ->whereYear('punched_in', $year)
                            ->whereMonth('punched_in', $this_month)
                            ->whereNotNull('punched_out')
                            ->get(); 
                            
        foreach ($holidays as $holiday) {
                $holiday_dates[] = [
                    'date' => $holiday->date->format('Y-m-d'),
                    'type' => 'holiday'
                ];
        }
        
        foreach ($days_not_punched as $not_punched) {
                $not_punched_dates[] = [
                    'date' =>  \Carbon\Carbon::parse($not_punched->punched_in)->format('Y-m-d'),
                    'type' => 'not_punched_out'
                ];
            
        }
        
        foreach ($taken_leaves as $leave) {
            $start = new \DateTime($leave->start_date);
            $end = new \DateTime($leave->end_date);
    
            while ($start <= $end) {
                $leave_dates[] = [
                    'date' => $start->format('Y-m-d'),
                    'type' => 'leave'
                ];
                $start->modify('+1 day');
            }
        }
    
        return response()->json([
            'success' => true,
            'message' => 'This month leave list fetched successfully.',
            'data' =>[
                "approved_leave_days"=>$leave_dates,
                "not_punched_days"=>$not_punched_dates,
                "total_punched_days"=>$total_punched_days,
                "holiday"=>$holiday_dates,
                "approval_date_time"=>$dm->as_approve_datetime??null,
                ]
            
        ], 200);
    }
    
    public function leave_apply_whatsapp_message($request, $apply_days, $type)
    {
        $dm = Deliveryman::where('id', $request->dm_id)->first();
        $leave = LeaveType::where('id', $request->leave_id)->where('status', 1)->first();
    
        if (!$dm || !$leave) {
            return response()->json(['error' => 'Invalid data provided'], 400);
        }

        $workType = '';
        if ($dm->work_type == 'deliveryman') {
         $workType = "Rider";
        } else if ($dm->work_type == 'in-house') {
            $workType = "Employee";
        }else if ($dm->work_type == 'adhoc') {
            $workType = "Adhoc";
        } 
    
         if ($type === 'admin') {
            $message = "Dear Admin,\n\n" .
                "A new leave request has been submitted:\n\n" .
                "Leave Type: " . $leave->leave_name . " (" . $leave->short_name . ")\n" .
                "Employee Name : " . $dm->first_name . " " . $dm->last_name . " (" . $workType . ")\n" .
                "Contact : " . $dm->mobile_number . "\n" .
                "Leave Duration : " . date('d-m-Y', strtotime($request->start_date)) . " to " . date('d-m-Y', strtotime($request->end_date)) . "\n" .
                "Total Days Applied : " . $apply_days . "\n" .
                "Remarks : " . $request->remarks . "\n\n" .
                "Please review and process the request accordingly.\n" .
                "**GreenDriveConnect Team**";
    
            $response = CustomHandler::admin_whatsapp_message($message);
        } elseif ($type === 'employee') {
            $message = "Hello " . $dm->first_name . " " . $dm->last_name . ",\n\n" .
                "Your leave application has been received successfully.\n\n" .
                "Leave Type : " . $leave->leave_name . " (" . $leave->short_name . ")\n" .
                "Leave Duration : " . date('d-m-Y', strtotime($request->start_date)) . " to " . date('d-m-Y', strtotime($request->end_date)) . "\n" .
                "Total Days Applied : " . $apply_days . "\n" .
                "Remarks : " . $request->remarks . "\n\n" .
                "We will review your request and update you soon.\n" .
                "**GreenDriveConnect Team**";
    
            $response = CustomHandler::user_whatsapp_message($dm->mobile_number,$message);
        } else {
            return response()->json(['error' => 'Invalid type'], 400);
        }
        
        return response()->json(['error' => 'Invalid type'], 400);
    }

    public function permission_apply_whatsapp_message($request, $get_time_count, $type)
    {
        $dm = Deliveryman::where('id', $request->dm_id)->first();
        $leave = LeaveType::where('id', $request->leave_id)->where('status', 1)->first();
    
        if (!$dm || !$leave) {
            return response()->json(['error' => 'Invalid data provided'], 400);
        }

        $workType = '';
        if ($dm->work_type == 'deliveryman') {
         $workType = "Rider";
        } else if ($dm->work_type == 'in-house') {
            $workType = "Employee";
        }else if ($dm->work_type == 'adhoc') {
            $workType = "Adhoc";
        } 

    
        if ($type === 'admin') {
            $message = "Dear Admin,\n\n" .
                "A new Permission request has been submitted:\n\n" .
                "Leave Type: " . $leave->leave_name . " (" . $leave->short_name . ")\n" .
                "Employee Name : " . $dm->first_name . " " . $dm->last_name . " (" . $workType . ")\n" .
                "Contact : " . $dm->mobile_number . "\n" .
                "Date : " . date('d-m-Y', strtotime($request->permission_date)) . "\n" .
                "Time : " . date('h:i A', strtotime($request->start_time)) . " to " . date('h:i A', strtotime($request->end_time)) . "\n" .
                "Total Hour : " . $get_time_count . "\n" .
                "Remarks : " . $request->remarks . "\n\n" .
                "Please review and process the request accordingly.\n" .
                "**GreenDriveConnect Team**";
    
            CustomHandler::admin_whatsapp_message($message);
        } elseif ($type === 'employee') {
            $message = "Hello " . $dm->first_name . " " . $dm->last_name . ",\n\n" .
                "Your leave application has been received successfully.\n\n" .
                "Leave Type : " . $leave->leave_name . " (" . $leave->short_name . ")\n" .
                "Date : " . date('d-m-Y', strtotime($request->permission_date)) . "\n" .
                "Time : " . date('h:i A', strtotime($request->start_time)) . " to " . date('h:i A', strtotime($request->end_time)) . "\n" .
                "Total Hour : " . $get_time_count . "\n" .
                "Remarks : " . $request->remarks . "\n\n" .
                "We will review your request and update you soon.\n" .
                "**GreenDriveConnect Team**";
    
            CustomHandler::user_whatsapp_message($dm->mobile_number, $message);
        } else {
            return response()->json(['error' => 'Invalid type'], 400);
        }
        
        return response()->json(['error' => 'Invalid type'], 400);
    }


}