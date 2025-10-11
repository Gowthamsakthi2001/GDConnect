<?php

namespace Modules\HRStatus\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\City\Entities\City;
use Modules\City\Entities\Area;
use App\Models\BgvComment;
use App\Models\BgvDocument;
use Modules\HRStatus\Entities\HRleveltwoDeliverymanAssignment;
use Modules\HRStatus\Entities\CandidateProgressLog;
use Modules\BgvVendor\Entities\BgvDeliverymanAssignment;
use Modules\RiderType\Entities\RiderType;
use App\Models\HrQuery;
use App\Models\User;
use App\Mail\CandidateStatusMail;
use Illuminate\Support\Facades\Mail;
use App\Models\CandidateKycUpdate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HRLevelOneController extends Controller
{
    public function hr_levelone_dashboard(Request $request)
    {
        $cities = City::where('status',1)->get();
        $login_user_role = auth()->user()->role ?? '';
        $city_id = $request->city_id ?? '';
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
        
        $total_application = Deliveryman::whereNotNull('register_date_time');
        if (!empty($from_date) && !empty($to_date)) {
            $total_application->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $total_application->where('current_city_id', $city_id);
        }
        $total_application_count = $total_application->count();

        
        $pending_application = Deliveryman::whereNotNull('register_date_time');
        if (!empty($from_date) && !empty($to_date)) {
            $pending_application->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $pending_application->where('current_city_id', $city_id);
        }
        $pending_application_count = $pending_application->where('kyc_verify', 0)->get()->count();
        
        $completed_application = Deliveryman::whereNotNull('register_date_time');
        if (!empty($from_date) && !empty($to_date)) {
            $completed_application->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $completed_application->where('current_city_id', $city_id);
        }
        $completed_application_count = $completed_application->where('kyc_verify', 1)->get()->count();
        
        $rejected_application = Deliveryman::whereNotNull('register_date_time');
        if (!empty($from_date) && !empty($to_date)) {
            $rejected_application->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $rejected_application->where('current_city_id', $city_id);
        }
        $rejected_application_count = $rejected_application->where('kyc_verify', 2)->get()->count();
        
        $hold_application = Deliveryman::whereNotNull('register_date_time');
        if (!empty($from_date) && !empty($to_date)) {
            $hold_application->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $hold_application->where('current_city_id', $city_id);
        }
        $hold_application_count = $hold_application->where('kyc_verify', 3)->get()->count();
        
        $total_hr_approve = Deliveryman::whereNotNull('register_date_time')->whereNotNull('approved_status')->where('rider_status', 3);
        if (!empty($from_date) && !empty($to_date)) {
            $total_hr_approve->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $total_hr_approve->where('current_city_id', $city_id);
        }
        $total_hr_approve_count = $total_hr_approve->get()->count();
        
        $total_hr_probation = Deliveryman::whereNotNull('register_date_time')->whereNotNull('approved_status')->where('rider_status', 3);
        if (!empty($from_date) && !empty($to_date)) {
            $total_hr_probation->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $total_hr_probation->where('current_city_id', $city_id);
        }
        $total_hr_probation_count = $total_hr_probation->get()->count();
        
        $total_hr_reject = Deliveryman::whereNotNull('register_date_time')->whereNotNull('approved_status')->where('rider_status', 2);
        if (!empty($from_date) && !empty($to_date)) {
            $total_hr_reject->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $total_hr_reject->where('current_city_id', $city_id);
        }
        $total_hr_reject_count = $total_hr_reject->get()->count();
        
        $total_hr_live = Deliveryman::whereNotNull('register_date_time')->whereNotNull('approved_status')->where('rider_status', 1);
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
            ->whereNotNull('register_date_time');
        
        if (!empty($city_id)) {
            $todays_application->where('current_city_id', $city_id);
        }
        
        $todays_applications = $todays_application->count();
        
        
    $bgv_pending_count = 0;
    $bgv_ageing_pending_count = 0;
    
    Deliveryman::whereNotNull('register_date_time')
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
        
        $total_employee_count = Deliveryman::whereNotNull('register_date_time')->where('work_type','in-house')->count();
        $total_rider_count = Deliveryman::whereNotNull('register_date_time')->where('work_type','deliveryman')->count();
        $total_adhoc_count = Deliveryman::whereNotNull('register_date_time')->where('work_type','adhoc')->count();
        $total_vehicle_count = DB::table('ev_modal_vehicles')->where('status',1)->get()->count();
        
         return view('hrstatus::level_one.hrdashboard_level_one',compact('login_user_role','cities','city_id','from_date','to_date','total_application_count','pending_application_count',
            'completed_application_count','rejected_application_count','hold_application_count','pending_percentage','completed_percentage','rejected_percentage','hold_percentage',
            'total_hr_approve_count','total_hr_probation_count','total_hr_probation_count','total_hr_live_count','hr_approve_percentage','hr_probation_percentage','hr_reject_percentage','hr_live_percentage','todays_applications','total_application_percentage'));
    }
    
    public function levelone_application_list(Request $request,$type)
    {
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
        $roll_type = $request->roll_type ?? '';
        $city_id = $request->city_id ?? '';
        $bgv_status = $request->bgv_status ?? '';
        $rider_status = $request->rider_status ?? '';
    
        $query = Deliveryman::whereNotNull('register_date_time')->whereNotNull('reg_application_id');
    
        if ($roll_type != "") {
            $query->where('work_type', $roll_type);
        }
        
        if (!empty($from_date) && !empty($to_date)) {
            $query->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        
        if (!empty($city_id)) {
            $query->where('current_city_id', $city_id);
        }
        if (!empty($bgv_status)) {
            $query->where('kyc_verify', $bgv_status);
        }
        
    
        $lists = $query->orderBy('id','desc')->get();
        $cities = City::where('status',1)->get();
    //   ,'lists'
      return view('hrstatus::level_one.application_list',compact('lists','type','cities','roll_type','from_date', 'to_date','city_id','bgv_status','rider_status'));
    }
    
    public function levelone_application_view(Request $request,$id){
        
        $application = Deliveryman::whereNotNull('register_date_time')->where('id',$id)->first();
        if (!$application) {
            return back()->with('error', 'Application not found');
        }
        $cities = City::all();
        $areas = Area::where('city_id',$application->current_city_id)->get();
        $rider_types = RiderType::where('status', 1)->get();


        $prev_url = url()->previous();
        return view('hrstatus::level_one.application_preview',compact('application','cities','areas','rider_types','prev_url'));
    }
    
    public function candidate_kyc_update(Request $request, $id)
    {
        $application = Deliveryman::whereNotNull('register_date_time')->where('id', $id)->first();
    
        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found']);
        }
        
    
        if ($request->hasFile('aadhaar_front_img')) {
            // $file = $request->file('aadhaar_front_img');
            // $fileName = 'aadhaar_front_' . time() . '.' . $file->getClientOriginalExtension();
            // $filePath = $file->storeAs('uploads/kyc', $fileName, 'public');
    
            // // Save to DB
            // $application->aadhaar_front = $filePath;
            // $application->aadhaar_front_updated_at = now();
            // $application->save();
    
            return response()->json(['success' => true, 'message' => 'Aadhaar front updated successfully']);
        }
    
        return response()->json(['success' => false, 'message' => 'No file uploaded']);
    }


   public function updateCandidateStatus(Request $request)
    {

        try {

            $validated = $request->validate([
                'application_id' => 'required|exists:ev_tbl_delivery_men,id',
                'status' => 'required|in:approve_sent_to_hr02,sent_to_bgv,on_hold,rejected',
                'remarks' => 'required_if:status,on_hold,rejected|nullable|string|max:1000'
            ]);
    
            DB::beginTransaction();
    
            $application = Deliveryman::whereNotNull('register_date_time')
                ->findOrFail($validated['application_id']);
    
           if($request->status == "approve_sent_to_hr02"){

                $assignedData = HRleveltwoDeliverymanAssignment::where('dm_id', $application->id)
                    ->where('assigned_dep', 'hr_level_one')
                    ->first();
        
                if ($assignedData) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This candidate has already been forwarded to HR Level Two.'
                    ]);
                }
           }
           
            if($request->status == "on_hold" || $request->status == "rejected"){

                $existData =Deliveryman::whereNotNull('register_date_time')->where('id',$validated['application_id'])->where('hr_level_one_status',$request->status)->first();
                $Text = $request->status == 'on_hold' ? 'On Hold' : 'Rejected';
                if ($existData) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This candidate has already been forwarded to '.$Text
                    ]);
                }
           }
           
           if($request->status == "sent_to_bgv"){

                $assignedData = BgvDeliverymanAssignment::where('dm_id', $application->id)
                    ->where('assigned_dep', 'hr_level_one')
                    ->first();
        
                if ($assignedData) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This candidate has already been forwarded to BGV.'
                    ]);
                }
           }
            
            $storeData = [
                'dm_id'        => $application->id,
                'assign_at'    => now(),
                'current_status' => 'pending',
                'assigned_dep' => 'hr_level_one',
                'assigned_by'  => auth()->user()->id
            ];
            $logData = [];
            $logData['dm_id'] = $application->id ?? null;
            $logData['application_status'] = $request->status ?? null;
            $logData['department'] = 'hr_level_one';
            $logData['created_by'] = auth()->user()->id ?? null;
            $logData['remarks'] = $request->remarks ?? null;
            
            if($request->status == "approve_sent_to_hr02"){
                HRleveltwoDeliverymanAssignment::create($storeData);
                $logData['remarks'] = 'Application forwarded to HR Level 02 for further processing.';
            }
            
            if($request->status == "sent_to_bgv"){
                BgvDeliverymanAssignment::create($storeData);
                $logData['remarks'] = 'Application forwarded to BGV department for verification.';
            }
    
            CandidateProgressLog::create($logData);
            
            $application->hr_level_one_status = $request->status;
            $application->save();
    
            // ✅ Notifications
            // $this->sendEmailNotification($validated, $application);
            // $this->notifyDeliveryman($application, $validated['status']);
            // $this->notifyHrTeam($validated['status'], $application);
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
    
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('HR Action Failed: ' . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'Operation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    
        protected function mapStatusToKycVerify($status)
    {
        
        return [
            'approve_sent_to_hr02' => 1,
            'sent_to_bgv' => 4,
            'on_hold' => 3,
            'rejected' => 2
        ][$status];
    }
    
        protected function sendEmailNotification($validated, $application)
    {
        Mail::to($this->getRecipients($validated['status'], $application->email))
            ->send(new CandidateStatusMail(
                $application,
                $validated['status'],
                $validated['remarks'] ?? null,
                auth()->user()->name
            ));
    }
    
    
       protected function notifyDeliveryman($application, $status)
    {
        
        
        if (!empty($application->fcm_token)) {
            try {
                $notificationService = app(\App\Services\FirebaseNotificationService::class);
                $notificationService->sendToDeliveryMan(
                    $application->id,
                    $application->fcm_token,
                    "Application Status Update",
                    $this->getDeliverymanStatusMessage($status),
                    [
                        'status' => $status,
                        'application_id' => $application->id,
                        'action' => 'status_update'
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Deliveryman push notification failed: ' . $e->getMessage());
            }
        }

    }

    protected function notifyHrTeam($status, $application)
    {
        try {
            $hrUsers = User::whereIn('role', ['HR', 'hr_manager']) // Adjust roles as needed
                ->whereNotNull('fcm_token')
                ->get();

            $notificationService = app(\App\Services\FirebaseNotificationService::class);

            foreach ($hrUsers as $hrUser) {
                $notificationService->sendToUser(
                    $hrUser->id,
                    $hrUser->fcm_token,
                    "New Application Update",
                    $this->getHrStatusMessage($status, $application),
                    [
                        'status' => $status,
                        'application_id' => $application->id,
                        'action' => 'hr_notification'
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('HR push notification failed: ' . $e->getMessage());
        }
    }

    protected function getDeliverymanStatusMessage($status)
    {
        return [
            'approve_sent_to_hr02' => 'Your application has been approved and moved to next stage',
            'sent_to_bgv' => 'Your application is under background verification',
            'on_hold' => 'Your application is on hold: ' . ($request->remarks ?? ''),
            'rejected' => 'Your application has been rejected: ' . ($request->remarks ?? '')
        ][$status];
    }

    protected function getHrStatusMessage($status, $application)
    {
        return sprintf(
            "Application %s %s by %s",
            $application->reg_application_id,
            [
                'approve_sent_to_hr02' => 'approved',
                'sent_to_bgv' => 'sent for BGV',
                'on_hold' => 'put on hold',
                'rejected' => 'rejected'
            ][$status],
            auth()->user()->name
        );
    }

    protected function getRecipients($status, $candidateEmail = null)
    {
        $recipients = [
            'approve_sent_to_hr02' => [''], //Hr Mail
            'sent_to_bgv' => [''], //Hr Mail
            'on_hold' => [''], //Hr Mail
            'rejected' => [''] //Hr Mail
        ];

        if (in_array($status, ['approve_sent_to_hr02', 'sent_to_bgv', 'on_hold', 'rejected'])) {
            $recipients[$status][] = $candidateEmail;
        }

        return array_filter($recipients[$status]);
    }


    protected function formatNotificationMessage($status, $application, $isHr = false)
    {
        $appId = $application->reg_application_id;
        $userName = auth()->user()->name;

        $messages = [
            'approve_sent_to_hr02' => $isHr
                ? "Application $appId approved by $userName (HR1)"
                : "Congratulations! Your application $appId has been approved",

            'sent_to_bgv' => $isHr
                ? "Application $appId sent to BGV by $userName"
                : "Your application $appId is under background verification",

            'on_hold' => $isHr
                ? "Application $appId put on hold by $userName"
                : "Your application $appId is on hold. We'll contact you soon",

            'rejected' => $isHr
                ? "Application $appId rejected by $userName"
                : "Update: Your application $appId couldn't be processed"
        ];

        return $messages[$status] ?? "Status updated for application $appId";
    }

  
}