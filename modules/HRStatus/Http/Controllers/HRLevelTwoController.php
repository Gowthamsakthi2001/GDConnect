<?php

namespace Modules\HRStatus\Http\Controllers;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\HRStatus\Entities\HRleveltwoDeliverymanAssignment;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\City\Entities\City;
use Modules\City\Entities\Area;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\RiderType\Entities\RiderType;
use App\Models\CandidateKycUpdate; //updated by Mugesh.B
use Modules\HRStatus\Entities\CandidateProgressLog;
use Modules\HRStatus\Entities\HRLevelTwoQueries;

use Illuminate\Support\Facades\Mail;
use App\Mail\ApproveEmployeeMail;
use App\Mail\HRlevelTwoMail;
use App\Mail\NotifyHRMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Exports\HRLevelTwoExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class HRLevelTwoController extends Controller
{


    public function hr_leveltwo_dashboard(Request $request)
    
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
        
        $totalcount = HRleveltwoDeliverymanAssignment::count();
        $pending = HRleveltwoDeliverymanAssignment::where('current_status', 'pending')->count();
        $sentToBGV = HRleveltwoDeliverymanAssignment::where('current_status', 'sent_to_bgv')->count();
        $sentToHR1 = HRleveltwoDeliverymanAssignment::where('current_status', 'sent_to_hr1')->count();
        
        $approvedEmp = HRleveltwoDeliverymanAssignment::with('delivery_man')
            ->where('current_status', 'approved')
            ->whereHas('delivery_man', function ($query) {
                $query->where('work_type', 'in-house');
            })
            ->count();
        
        $approvedRider = HRleveltwoDeliverymanAssignment::with('delivery_man')
            ->where('current_status', 'approved')
            ->whereHas('delivery_man', function ($query) {
                $query->where('work_type', 'deliveryman');
            })
            ->count();

        $rejected = HRleveltwoDeliverymanAssignment::where('current_status', 'rejected')->count();

        
        
        
        
         return view('hrstatus::level_two.hrdashboard_level_two',compact('login_user_role','cities','city_id','from_date','to_date','total_application_count','pending_application_count',
            'completed_application_count','rejected_application_count','hold_application_count','pending_percentage','completed_percentage','rejected_percentage','hold_percentage',
            'total_hr_approve_count','total_hr_probation_count','total_hr_probation_count','total_hr_live_count','hr_approve_percentage','hr_probation_percentage','hr_reject_percentage','hr_live_percentage','todays_applications','total_application_percentage','pending','sentToBGV','sentToHR1','approvedEmp','approvedRider','rejected','totalcount'));
    }




    
    
    public function leveltwo_application_list(Request $request, $type)
{
    // dd($request->all());
    $from_date = $request->from_date ?? '';
    $to_date = $request->to_date ?? '';
    $roll_type = $request->roletype ?? '';
     $timeline = $request->timeline ?? '';
    $city_id = $request->city_id ?? '';
    $bgv_status = $request->bgv_status ?? '';
    $rider_status = $request->rider_status ?? '';

    // Start query
    $query = HRleveltwoDeliverymanAssignment::with('delivery_man')
        ->whereHas('delivery_man', function ($q) {
            $q->where('delete_status', 0)
              ->whereNotNull('reg_application_id');
        });

    // Role filter
        if (!empty($roll_type)) {
            if (in_array($roll_type, ['in-house', 'deliveryman', 'helper', 'adhoc'])) {
                $query->whereHas('delivery_man', function ($q) use ($roll_type) {
                    $q->where('work_type', $roll_type);
                });
            }
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
    
            // Overwrite the from_date/to_date to empty for consistency
            $from_date = null;
            $to_date = null;
        } else {

            if ($from_date) {
                $query->whereDate('created_at', '>=', $from_date);
            }
            
             if ($to_date) {
                $query->whereDate('created_at', '<=', $to_date);
            }
        }

    // City filter
    if (!empty($city_id)) {
        $query->whereHas('delivery_man', function ($q) use ($city_id) {
            $q->where('current_city_id', $city_id);
        });
    }

    // BGV status filter
    if (!empty($bgv_status)) {
        $query->whereHas('delivery_man', function ($q) use ($bgv_status) {
            $q->where('kyc_verify', $bgv_status);
        });
    }

    // ✅ Filter by card type (current_status)
    switch ($type) {
        case 'pending':
            $query->where('current_status', 'pending');
            break;
        case 'sent_to_bgv':
            $query->where('current_status', 'sent_to_bgv');
            break;
        case 'sent_to_hr1':
            $query->where('current_status', 'sent_to_hr1');
            break;
        case 'approved_employee':
            $query->where('current_status', 'approved')
                  ->whereHas('delivery_man', function ($q) {
                      $q->where('work_type', 'in-house');
                  });
            break;
        case 'approved_rider':
            $query->where('current_status', 'approved')
                  ->whereHas('delivery_man', function ($q) {
                      $q->where('work_type', 'deliveryman');
                  });
            break;
        case 'reject_by_hr2':
            $query->where('current_status', 'rejected');
            break;
        case 'total_application':
        default:
            // No status filter, show all
            break;
    }

    // Get data
    $lists = $query->orderBy('id', 'desc')->get();
    $cities = City::where('status', 1)->get();

    return view('hrstatus::level_two.application_list', compact(
        'lists',
        'type',
        'cities',
        'roll_type',
        'from_date',
        'to_date',
        'timeline',
        'city_id',
        'bgv_status',
        'rider_status'
    ));
}


    public function comment_store(Request $request)
    {
        // Validate input
        $request->validate([
            'dm_id'   => 'required|integer',
            'remarks' => 'required|string|max:500',
        ]);
    
        // Store the comment
        HRLevelTwoQueries::create([
            'dm_id'      => $request->dm_id,
            'remarks'    => $request->remarks,
            'comment_by' => auth()->id(),
            'comment_type'=>'hr_level_two',
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Comment saved successfully.',
        ]);
    }


    
    public function leveltwo_application_view(Request $request,$id){
        
        $application = Deliveryman::with('hrleveltwo_assign')
            ->where('delete_status', 0)
            ->where('id', $id)
            ->first();
    
        // If not found, redirect back with error
        if (!$application) {
            return back()->with('error', 'Application not found');
        }
        $queries = HRLevelTwoQueries::with('comment_by')->get();
        
    
        $cities = City::all();
        $areas = Area::where('city_id',$application->current_city_id)->get();
        $rider_types = RiderType::where('status', 1)->get();
        $logs = CandidateProgressLog::where('dm_id',$application->id)->where('department' , 'hr_level_two')->orderBy('created_at', 'desc')->get();
        


        return view('hrstatus::level_two.application_preview',compact('application','cities','areas', 'queries' ,'rider_types' ,'logs'));
    }
    
    
    
      public function verification(Request $request, $id, $status, $type)
{
    // Always get or create a new candidate record
    $candidate = CandidateKycUpdate::firstOrNew(['dm_id' => $id]);

    if (!auth()->user()->id) {
        return back()->with('error', 'Unauthorized.');
    }

    $now = now();
    $userId = auth()->user()->id;

    $fieldsToUpdate = [];
    $message = '';

    $isVerified = $status == 1; // 1 means verify, 0 means unverify

    switch ($type) {
        case "aadhar_front_verify":
            $fieldsToUpdate = [
                'aadhaar_front_verified'      => $isVerified ? 1 : 0,
                'aadhaar_front_approved_by'   => $isVerified ? $userId : null,
                'aadhaar_front_verified_at'   => $isVerified ? $now : null,
            ];
            $message = $isVerified ? 'Aadhar Front Verified successfully.' : 'Aadhar Front Unverified successfully.';
            break;

        case "aadhar_back_verify":
            $fieldsToUpdate = [
                'aadhaar_back_verified'      => $isVerified ? 1 : 0,
                'aadhaar_back_approved_by'   => $isVerified ? $userId : null,
                'aadhaar_back_verified_at'   => $isVerified ? $now : null,
            ];
            $message = $isVerified ? 'Aadhar Back Verified successfully.' : 'Aadhar Back Unverified successfully.';
            break;

        case "pan_verify":
            $fieldsToUpdate = [
                'pan_verified'      => $isVerified ? 1 : 0,
                'pan_approved_by'   => $isVerified ? $userId : null,
                'pan_verified_at'   => $isVerified ? $now : null,
            ];
            $message = $isVerified ? 'PAN Verified successfully.' : 'PAN Unverified successfully.';
            break;

        case "bank_verify":
            $fieldsToUpdate = [
                'bank_passbook_verified'      => $isVerified ? 1 : 0,
                'bank_passbook_approved_by'   => $isVerified ? $userId : null,
                'bank_passbook_verified_at'   => $isVerified ? $now : null,
            ];
            $message = $isVerified ? 'Bank Details Verified successfully.' : 'Bank Details Unverified successfully.';
            break;

        case "license_front_verify":
            $fieldsToUpdate = [
                'dl_front_verified'      => $isVerified ? 1 : 0,
                'dl_front_approved_by'   => $isVerified ? $userId : null,
                'dl_front_verified_at'   => $isVerified ? $now : null,
            ];
            $message = $isVerified ? 'License Front Verified successfully.' : 'License Front Unverified successfully.';
            break;

        case "license_back_verify":
            $fieldsToUpdate = [
                'dl_back_verified'      => $isVerified ? 1 : 0,
                'dl_back_approved_by'   => $isVerified ? $userId : null,
                'dl_back_verified_at'   => $isVerified ? $now : null,
            ];
            $message = $isVerified ? 'License Back Verified successfully.' : 'License Back Unverified successfully.';
            break;

        case "llr_verify":
            $fieldsToUpdate = [
                'llr_verified'      => $isVerified ? 1 : 0,
                'llr_approved_by'   => $isVerified ? $userId : null,
                'llr_verified_at'   => $isVerified ? $now : null,
            ];
            $message = $isVerified ? 'LLR Verified successfully.' : 'LLR Unverified successfully.';
            break;

        default:
            return back()->with('error', 'Invalid verification type.');
    }

    // Always set dm_id (important for firstOrNew)
    $candidate->dm_id = $id;

    // Set updated fields
    foreach ($fieldsToUpdate as $key => $value) {
        $candidate->$key = $value;
    }

    // Save the record (insert or update)
    $candidate->save();

    return back()->with('success', $message);
}


    
public function get_area_by_id(Request $request)
{
    $city_id = $request->city_id;

    $areas = Area::where('status', 1)->where('city_id', $city_id)->get();

    return response()->json($areas);
}

    
    
        public function destroy(Request $request)
        {
            // Validate that 'id' is provided
            $request->validate([
                'id' => 'required|integer|exists:hrleveltwo_deliveryman_assignments,id',
            ]);
        
            $id = $request->id;
        
            // Find the record by id
            $data = HRleveltwoDeliverymanAssignment::find($id);
        
            if ($data) {
                // Delete the record
                $data->delete();
        
                // Return success response
                return response()->json([
                    'success' => true,
                    'message' => 'Record deleted successfully.'
                ]);
            }
        
            // If record not found (redundant because of 'exists' validation, but good practice)
            return response()->json([
                'success' => false,
                'message' => 'Record not found.'
            ]);
        }

    
          public function update_details(Request $request)
    {
        
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_number' => 'required|string|max:15',
            'current_city_id' => 'nullable|exists:ev_tbl_city,id',
            'interested_city_id' => 'nullable',
            'gender' => 'nullable|string',
            'house_no' => 'nullable|string',
            'street_name' => 'nullable|string',
            'pincode' => 'nullable|string',
            'alternative_number' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'father_name' => 'nullable|string',
            'father_mobile_number' => 'nullable|string',
            'referal_person_name' => 'nullable|string',
            'referal_person_mobile' => 'nullable|string',
            'referal_person_relationship' => 'nullable|string',
            'spouse_name' => 'nullable|string',
            'spouse_mobile_number' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'social_links' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_holder_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422));
        }
    
        $validatedData = $validator->validated();
        
        try {
            $employee = Deliveryman::findOrFail($request->id);
        
    
        $employee->first_name = $request->first_name;
        $employee->last_name = $request->last_name;
        $employee->email = $request->email;
        $employee->mobile_number = $request->mobile_number;
        $employee->current_city_id = $request->current_city_id ?? null;
        $employee->interested_city_id = $request->interested_city_id ?? null;
        $employee->gender = $request->gender ?? null;
        $employee->house_no = $request->house_no ?? null;
        $employee->street_name = $request->street_name ?? null;
        $employee->pincode = $request->pincode ?? null;
        $employee->alternative_number = $request->alternative_number ?? null;
        $employee->date_of_birth = $request->date_of_birth ?? null;
        $employee->present_address = $request->present_address ?? null;
        $employee->permanent_address = $request->permanent_address ?? null;
        $employee->father_name = $request->father_name ?? null;
        $employee->father_mobile_number = $request->father_mobile_number ?? null;
        $employee->referal_person_name = $request->reference_name ?? null;
        $employee->referal_person_number = $request->reference_mobile ?? null;
        $employee->referal_person_relationship = $request->reference_relationship ?? null;
        $employee->spouse_name = $request->spouse_name ?? null;
        $employee->spouse_mobile_number = $request->spouse_mobile ?? null;
        $employee->blood_group = $request->blood_group ?? null;
        $employee->social_links = $request->social_links ?? null;
        $employee->bank_name = $request->bank_name ?? null;
        $employee->account_holder_name = $request->ac_holder_name ?? null;
        $employee->account_number = $request->bank_ac_no ?? null;
        $employee->ifsc_code = $request->ifsc_code ?? null;
        
        $employee->father_name = $request->guardian_name ?? null;
        $employee->father_mobile_number = $request->guardian_phone ?? null;
        
        $employee->emp_prev_company_id = $request->prev_rider_id ?? null;
        $employee->emp_prev_experience = $request->prev_company_experience ?? null;
        $employee->rider_type = $request->rider_type ?? null;
        $employee->vehicle_type = $request->vehicle_type ?? null;
        $employee->work_type = $request->role ?? null;

    
    
            $employee->save();
            $employee->refresh();
    
            return response()->json([
                'success' => true,
                'message' => 'Data updated successfully',
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to update employee: " . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to update: ' . $e->getMessage()
            ], 500);
        }
    }

public function update(Request $request)
{
    $status = $request->status;
    $remarks = $request->remarks ?? null;
    $id = $request->id;
    
    

        
        
    DB::beginTransaction();

    try {
        $candidate = Deliveryman::find($id);

        if (!$candidate) {
            return response()->json(['success' => false, 'message' => 'Candidate data not found']);
        }


        $sendToCandidateStatuses = ['approve_employee', 'approve_rider', 'approve_adhoc', 'approve_helper'];
        
        
        if (in_array($status, $sendToCandidateStatuses)) {
            
            $dm = Deliveryman::find($id);
            if (!$dm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rider not found.'
                ]);
            }
            
            if ($dm->kyc_verify != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'BGV status not verified. Please complete BGV verification before proceeding further.'
                ]);
            }

            $notVerified = [];
          
            if ($dm->aadhar_verify != 1) $notVerified[] = 'Aadhar';
            if ($dm->pan_verify != 1) $notVerified[] = 'PAN';
            if ($dm->bank_verify != 1) $notVerified[] = 'BANK';

            if ($dm->work_type !== "in-house" && $dm->lisence_verify != 1) {
                $notVerified[] = 'License';
            }
          

            if (!empty($notVerified)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The following fields are not verified or are missing: ' . implode(', ', $notVerified)
                ]);
            }
            
            
            
            $rider_id = $this->rider_generate_id($dm->id);
            if($rider_id == null){
                 return response()->json([
                    'success' => false,
                    'message' => 'Rider ID Generate Failed'
                ]);
            }
            
            
             
            
            $remarksMap = [
                'approve_employee' => 'Candidate has been approved for employment.',
                'approve_rider'    => 'Candidate has been approved for the rider position.',
                'approve_adhoc'    => 'Candidate has been approved for the ad-hoc assignment.',
                'approve_helper'   => 'Candidate has been approved for the helper role.',
            ];

            $remarks = $remarksMap[$status] ?? 'Candidate has been approved.';
            
          $this->status_handle_whatsapp_message($dm->id, 'approve');
            
           
            
            if (!empty($candidate->email)) {
                Mail::to($candidate->email)->send(new HRlevelTwoMail($candidate, $status, $remarks, 'candidate'));
            }
            
            $probation_from_date = date('Y-m-d', strtotime(now())); // e.g., 2025-01-01
            $probation_to_date   = date('Y-m-d', strtotime('+6 days')); // e.g., 2025-01-07

            if ($dm->work_type == "in-house") {
                $probation_from_date = null;
                $probation_to_date = null;
            }
                

        

            
           
            
            
            
             $dm->update([
                'emp_id'=>$rider_id,
                'emp_id_status'=>1,
                'rider_status' => 1,
                'approved_status' => 1,
                'approver_role' => auth()->user()->name,
                'approver_id' => auth()->user()->id,
                'as_approve_datetime'=> now(),
                'probation_from_date'=>$probation_from_date,
                'probation_to_date'=>$probation_to_date
            ]);
            
        if ($dm->fcm_token) {
            $notificationService = app(\App\Services\HRLevelTwoPushNotification::class);
            $notificationTitle = "Application Approved";
            $notificationBody  = "Congratulations! Your application has been approved.";
     
                $notificationService->sendToDeliveryMan(
                $dm->id,
                $dm->fcm_token,
                $notificationTitle,
                $notificationBody,
                auth()->user()->id,
                [
                    'type'     => 'application_approved',
                    'status'   => 'approved',
                    'rider_id' => $dm->id ?? 'Pending'
                ]
            );
            
        
        }
        

        


            
            
        }
        
        
        if($status== 'rejected'){
            
            
            $dm = Deliveryman::find($id);
            if (!$dm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rider not found.'
                ]);
            }
            
            if ($dm->kyc_verify != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'BGV status not verified. Please complete BGV verification before proceeding further.'
                ]);
            }

            $notVerified = [];
          
            if ($dm->aadhar_verify != 1) $notVerified[] = 'Aadhar';
            if ($dm->pan_verify != 1) $notVerified[] = 'PAN';
            if ($dm->bank_verify != 1) $notVerified[] = 'BANK';

            if ($dm->work_type !== "in-house" && $dm->lisence_verify != 1) {
                $notVerified[] = 'License';
            }
          

            if (!empty($notVerified)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The following fields are not verified or are missing: ' . implode(', ', $notVerified)
                ]);
            }
            
            
            $dm->update([
                'rider_status' => 2,
                'approved_status' => 2,
                'approver_role' => auth()->user()->name,
                'approver_id' => auth()->user()->id,
                'deny_remarks'=>$remarks,
                'as_approve_datetime'=> now()
            ]);
    
            if (!$remarks) {
                 return response()->json([
                    'success' => false,
                    'message' => 'Remarks Field is required. Please enter a Remarks'
                ]);
            }
            
        }
        
        
        
        if ($status == 'sent_back_to_hr1') {
        
            $dm = Deliveryman::find($id);
        
            if (!$dm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rider not found.'
                ]);
            }
        
            $dm->hr_level_one_status = 'pending';
        
            $dm->hr_level_two_changed_by = json_encode([
                'id'        => auth()->id(),
                'datetime' => now()->format('Y-m-d H:i:s') // formatted datetime
            ]);
            
        
            $dm->save();
        
        }


        // Send email to HRs
        $hrUsers = User::where('delete_status', 0)->where('role', 4)->get();
        foreach ($hrUsers as $hr) {
            if (!empty($hr->email)) {
                Mail::to($hr->email)->send(new HRlevelTwoMail($candidate, $status, $remarks, 'hr'));
            }
        }

        // Send email to Admins
        $adminUsers = User::where('delete_status', 0)->where('role', 1)->get();
        foreach ($adminUsers as $admin) {
            if (!empty($admin->email)) {
                Mail::to($admin->email)->send(new HRlevelTwoMail($candidate, $status, $remarks, 'admin'));
            }
        }

        // Map status for HRleveltwoDeliverymanAssignment
        $mapped_status = match ($status) {
            'approve_rider' => 'approved',
            'approve_employee' => 'approved',
            'approve_adhoc' => 'approved',
            'approve_helper' => 'approved',
            'sent_back_to_hr1' => 'sent_to_hr1',
            'rejected' => 'rejected',
            default => 'pending',
        };
        
        // Log progress
        CandidateProgressLog::create([
            'dm_id' => $id,
            'remarks' => $remarks,
            'application_status' => $mapped_status,
            'department' => 'hr_level_two',
            'created_by' => auth()->user()->id,
        ]);



        // Update current_status in HRleveltwoDeliverymanAssignment table
        
        HRleveltwoDeliverymanAssignment::where('dm_id', $id)
            ->update(['current_status' => $mapped_status]);

        DB::commit();
 
        return response()->json(['success' => true, 'message' => 'Application status changed successfully']);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('HR Level Two Update Error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Something went wrong. Please try again.']);
    }
}



  private function rider_generate_id($id)
    {
        $dm = Deliveryman::find($id);
    
        if (!$dm || !$dm->current_city || !$dm->current_city->short_code) {
            return null;
        }
    
        $city_code = strtoupper(trim($dm->current_city->short_code));
    
        $riderType = match ($dm->work_type) {
            'deliveryman' => 'R',
            'in-house'    => 'E',
            'adhoc'       => 'A',
            'helper'      => 'H',
            default       => 'N',
        };
    
        $year = date('y'); // e.g. 25
    
        $prefix = 'GDM' . $riderType . $year . $city_code;
    
        $lastEmpId = DB::table('ev_tbl_delivery_men')
            ->where('delete_status', 0)
            ->whereNotNull('emp_id')
            ->select(DB::raw('RIGHT(emp_id, 5) AS emp_number'))
            ->orderByRaw('CAST(emp_number AS UNSIGNED) DESC')
            ->value('emp_number');
    
        $lastSerial = $lastEmpId ? (int)$lastEmpId : 0;
        $newSerial = str_pad((string)($lastSerial + 1), 5, '0', STR_PAD_LEFT);
    
        $new_emp_id = $prefix . $newSerial;
    
        // dd($lastEmpId, $new_emp_id); 
    
        $dm->emp_id = strtoupper($new_emp_id);
        $dm->save();
    
        return $dm->emp_id;
    }


// public function fetchFilteredStats(Request $request)
// {
//     $role = $request->input('role'); // 'Employee' or 'Rider'
//     $fromDate = $request->input('from_date');
//     $toDate = $request->input('to_date');

//     // Map the correct work_type for each role
//     $workType = $role === 'Employee' ? 'in-house' : ($role === 'Rider' ? 'deliveryman' : null);

//     $assignments = HRleveltwoDeliverymanAssignment::with('delivery_man')
//         ->whereHas('delivery_man', function ($q) use ($workType) {
//             if ($workType) {
//                 $q->where('work_type', $workType);
//             }
//         })
//         ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
//             $q->whereBetween('created_at', [$fromDate, $toDate]);
//         })
//         ->get();

//     // PHP level filtering based on role + status
//     $filtered = $assignments->filter(function ($item) use ($role) {
//         if (!$item->delivery_man) return false;

//         if ($role === 'Employee') {
//             return $item->current_status === 'approved_employee';
//         }

//         if ($role === 'Rider') {
//             return $item->current_status === 'approved_rider';
//         }

//         return false;
//     });

//     // Status count
//     $data = [
//         'total' => $assignments->count(),
//         'pending' => $assignments->where('current_status', 'pending')->count(),
//         'sent_to_bgv' => $assignments->where('current_status', 'sent_to_bgv')->count(),
//         'sent_to_hr1' => $assignments->where('current_status', 'sent_to_hr1')->count(),
//         'approved_employee' => $assignments->where('current_status', 'approved_employee')->count(),
//         'approved_rider' => $assignments->where('current_status', 'approved_rider')->count(),
//         'rejected' => $assignments->where('current_status', 'reject_by_hr2')->count(),
//     ];

//     return response()->json($data);
// }


public function fetchFilteredStats(Request $request)
{
    $role = $request->input('role'); // Employee, Rider, Adhoc, Helper
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');

$fromDateTime = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
$toDateTime = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

    // Role → work_type mapping
    $roleMap = [
        'Employee' => 'in-house',
        'Rider' => 'deliveryman',
        'Adhoc' => 'adhoc',
        'Helper' => 'helper',
    ];

    $workType = $roleMap[$role] ?? null;

    $assignments = HRleveltwoDeliverymanAssignment::with('delivery_man')
        ->whereHas('delivery_man', function ($q) use ($workType) {
            if ($workType) {
                $q->where('work_type', $workType);
            }
        })
    ->when($fromDateTime && $toDateTime, function ($q) use ($fromDateTime, $toDateTime) {
        $q->whereBetween('created_at', [$fromDateTime, $toDateTime]);
    })
        ->get();

    $data = [
        'total' => $assignments->count(),
        'pending' => $assignments->where('current_status', 'pending')->count(),
        'sent_to_bgv' => $assignments->where('current_status', 'sent_to_bgv')->count(),
        'sent_to_hr1' => $assignments->where('current_status', 'sent_to_hr1')->count(),
        // Approved employees
        'approved_employee' => $assignments->filter(function ($item) {
            return $item->current_status === 'approved'
                && optional($item->delivery_man)->work_type === 'in-house';
        })->count(),

        // Approved riders
        'approved_rider' => $assignments->filter(function ($item) {
            return $item->current_status === 'approved'
                && optional($item->delivery_man)->work_type === 'deliveryman';
        })->count(),
        'rejected' => $assignments->where('current_status', 'rejected')->count(),
    ];

    return response()->json($data);
}


// public function export_data(Request $request)
// {
//     // Get filter parameters
//     $status = $request->status; // pending, sent_to_bgv, etc.
//     $fromDate = $request->from_date;
//     $toDate = $request->to_date;
//     $timeline = $request->timeline;
    
//     // Get selected IDs if any
//     $selectedIds = json_decode($request->input('selected_ids', '[]'), true);
    
//     // Get selected fields
//     $selectedFields = json_decode($request->input('fields', '[]'), true);
   
    
//     // Generate filename with timestamp
//     $filename = 'hr_level_two_export_' . now()->format('Ymd_His') . '.xlsx';
    
//     return Excel::download(new HRLevelTwoExport($status, $fromDate, $toDate, $timeline, $selectedIds, $selectedFields), $filename);
// }

public function export_data(Request $request)
{
    // dd($request->all());
    
    // Get filter parameters
    $status = $request->status;
    $fromDate = $request->from_date;
    $toDate = $request->to_date;
    $timeline = $request->timeline;
    $roleType = $request->roletype; // Add role type filter
    
    // Get selected IDs if any
    $selectedIds = json_decode($request->input('selected_ids', '[]'), true);
    
    // Get selected fields
    $selectedFields = json_decode($request->input('fields', '[]'), true);
    
    // Generate filename with timestamp
    $filename = 'hr_level_two_export-' . now()->format('d M Y') . '.xlsx';
    
    return Excel::download(
        new HRLevelTwoExport($status ,$fromDate, $toDate, $timeline, $selectedIds, $selectedFields, $roleType),
        $filename
    );
}

// private function sendApprovalMessage($mobile)
// {
//     // $dm = Deliveryman::where('mobile_number', $mobile)->first();
    
//     // if (!$dm) {
//     //     \Log::error("WhatsApp: Rider not found for mobile {$mobile}");
//     //     return;
//     // }
    
//   $phone = preg_replace('/\D/', '', $mobile);
//      $firstName = "TestUser";
//     $lastName = "Rider";

//     // $phone = '91' . $dm->mobile_number; // Employee number with country code
//     $message = "Dear \n\n" .
//               "Congratulations! 🎉 You have been approved as a rider with GreenDriveConnect.\n\n" .
//               "Details:\n" .
//               "Name:{$firstName} {$lastName}\n "  . 
//               "Approved At: " . now()->format('Y-m-d H:i:s') . "\n\n" .
//               "Welcome to the team!\n" .
//               "GreenDriveConnect";

//       $api_key = env('WHATSAPP_API_KEY'); 
//         $url = env('WHATSAPP_API_URL'); 


//     $postdata = [
//         "contact" => [
//             [
//                 "number"  => $phone,
//                 "message" => $message,
//             ],
//         ],
//     ];

//     $curl = curl_init();
//     curl_setopt_array($curl, [
//         CURLOPT_URL => $url,
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_CUSTOMREQUEST => 'POST',
//         CURLOPT_POSTFIELDS => json_encode($postdata),
//         CURLOPT_HTTPHEADER => [
//             'Api-key: ' . $api_key,
//             'Content-Type: application/json',
//         ],
//     ]);

//     $response = curl_exec($curl);
//     curl_close($curl);

//     $response_data = json_decode($response, true);

//     if (!isset($response_data['status']) || $response_data['status'] != 'success') {
//         \Log::error('WhatsApp send failed', ['response' => $response_data]);
//     } else {
//         \Log::info('WhatsApp sent successfully', ['response' => $response_data]);
//     }
// }


public function status_handle_whatsapp_message($id, $type)
    {
        $dm = Deliveryman::where('id', $id)->first();
    
        if (!$dm) {
            Log::error("Deliveryman not found with ID: " . $id);
            return false;
        }
    
        $phone = '+917305392961';
           if ($dm->work_type == 'deliveryman') {
             $role = "Delivery Partner";
            } elseif ($dm->work_type == 'in-house') {
                $role = "Employee";
            } else {
                $role = "Member";
            }
        
            $message = "Dear " . $dm->first_name . " " . $dm->last_name . ",\n\n" .
                       "✅ Your role as *" . $role . "* has been approved by the Admin! 🎉\n\n" .
                       "Best regards,\n" .
                       "GreenDriveConnect";

    
        $api_key = env('WHATSAPP_API_KEY');
        Log::info('whatsappResponse Data Api Key: ' . $api_key);
        $url = 'https://whatshub.in/api/whatsapp/send';
    
        $postdata = [
            "contact" => [
                [
                    "number" => $phone,
                    "message" => $message,
                ],
            ],
        ];
    
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postdata),
            CURLOPT_HTTPHEADER => [
                'Api-key: ' . $api_key,
                'Content-Type: application/json',
            ],
        ]);
    
        $response = curl_exec($curl);
        curl_close($curl);
    
        $response_data = json_decode($response, true);
        Log::info('whatsappResponse Data: ' . json_encode($response_data));
    
        // return $response_data;
    }






}
