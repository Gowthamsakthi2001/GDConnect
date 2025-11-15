<?php

namespace Modules\HRStatus\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\City\Entities\City;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\City\Entities\Area;
use App\Helpers\CustomHandler;
use App\Models\BgvComment;
use App\Models\BgvDocument;
use Modules\RiderType\Entities\RiderType;
use App\Models\HrQuery;
use App\Models\User;
use App\Models\BusinessSetting;
use Spatie\Permission\Models\Role; // Updated by logesh
use Modules\Zones\Entities\Zones; // Updated by logesh
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\RecoveryAgentChanged;
use Illuminate\Support\Facades\Mail;

class HRStatusController extends Controller
{
    
    public function hr_dashboard_show(Request $request)
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
        
         return view('hrstatus::show_hr_dashboard',compact('login_user_role','cities','city_id','from_date','to_date','total_application_count','pending_application_count',
            'completed_application_count','rejected_application_count','hold_application_count','pending_percentage','completed_percentage','rejected_percentage','hold_percentage',
            'total_hr_approve_count','total_hr_probation_count','total_hr_probation_count','total_hr_live_count','hr_approve_percentage','hr_probation_percentage','hr_reject_percentage','hr_live_percentage','todays_applications','total_application_percentage'));
    }
    
    
    
    // public function recruiter_list(Request $request)
    // {
    //     $from_date = $request->from_date ?? '';
    //     $to_date = $request->to_date ?? '';
    //     $roll_type = $request->roll_type ?? '';
    //     $city_id = $request->city_id ?? '';
    //     $bgv_status = $request->bgv_status ?? '';
    //     $rider_status = $request->rider_status ?? '';
    
    //     $query = Deliveryman::where('delete_status', 0)->whereNotNull('register_date_time');
    
    //     if ($roll_type != "") {
    //         $query->where('work_type', $roll_type);
    //     }
        
    //     if (!empty($from_date) && !empty($to_date)) {
    //         $query->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
    //     }
        
    //     if (!empty($city_id)) {
    //         $query->where('current_city_id', $city_id);
    //     }
    //     if (!empty($bgv_status)) {
    //         $query->where('kyc_verify', $bgv_status);
    //     }
        
    
    //     $lists = $query->orderBy('id','desc')->get();
    //     $cities = City::where('status',1)->get();
    //     // dd($lists);
        
    //     return view('hrstatus::recruiter_list', compact('lists','cities','roll_type','from_date', 'to_date','city_id','bgv_status','rider_status'));
    // }
    
    public function recruiter_list(Request $request)
{
    if ($request->ajax()) {
        $from_date    = $request->from_date ?? '';
        $to_date      = $request->to_date ?? '';
        $roll_type    = $request->roll_type ?? '';
        $city_id      = $request->city_id ?? '';
        $bgv_status   = $request->bgv_status ?? '';
        $rider_status = $request->rider_status ?? '';
        $searchValue  = $request->input('search.value'); // DataTables search

        // Base query
        $query = Deliveryman::with(['current_city', 'RiderType'])
            ->where('delete_status', 0)
            ->whereNotNull('register_date_time');

        if ($roll_type != "") {
            $query->where('work_type', $roll_type);
        }
        if (!empty($from_date) && !empty($to_date)) {
            $query->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $query->where('current_city_id', $city_id);
        }
        if ($bgv_status !== "") {
            $query->where('kyc_verify', $bgv_status);
        }
        if ($rider_status !== "") {
            $query->where('rider_status', $rider_status);
        }

        // 🔹 Apply global search
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('first_name', 'like', "%{$searchValue}%")
                  ->orWhere('last_name', 'like', "%{$searchValue}%")
                  ->orWhere('email', 'like', "%{$searchValue}%")
                  ->orWhere('reg_application_id', 'like', "%{$searchValue}%")
                  ->orWhere('emp_id', 'like', "%{$searchValue}%");
            });
        }

        // 🔹 Count before filtering
        $recordsTotal = Deliveryman::where('delete_status', 0)
            ->whereNotNull('register_date_time')
            ->count();
        $recordsFiltered = $query->count();

        // 🔹 Pagination
        $start  = $request->input('start', 0);
        $length = $request->input('length', 10);

        // 🔹 Ordering
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $orderableColumns = [
            'id', 'reg_application_id', 'emp_id', 'photo', 'first_name', 'email',
            'current_city_id', 'register_date_time', 'work_type', 'rider_type_id',
            'probation_from_date', 'probation_to_date', 'rider_status', 'kyc_verify'
        ];
        $orderColumn = $orderableColumns[$orderColumnIndex] ?? 'id';
    
        $lists = $query->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        // 🔹 Format data
        $data = [];
        foreach ($lists as $key => $val) {
            $full_name = ($val->first_name ?? '') . ' ' . ($val->last_name ?? '');
            $image = $val->photo
                ? asset('public/EV/images/photos/'.$val->photo)
                : asset('public/admin-assets/img/person.png');

            // Application Status
            $applicationStatus = match($val->rider_status) {
                3       => '<button class="btn success-btn-custom btn-md px-5">Accepted</button>',
                2       => '<button class="btn reject-btn-custom btn-md px-5">Rejected</button>',
                1       => '<button class="btn live-btn-custom btn-md px-6">Live</button>',
                0       => '<button class="btn info-btn-custom btn-md px-5">Pending</button>',
                default => '<button class="btn btn-warning btn-md px-5">N/A</button>',
            };

            // BGV Status
            $bgvStatus = match($val->kyc_verify) {
                1       => '<button class="btn success-btn-custom btn-md px-6">&nbsp;Verified&nbsp;</button>',
                2       => '<button class="btn reject-btn-custom btn-md px-6">&nbsp;Rejected&nbsp;</button>',
                3       => '<button class="btn hold-btn-custom btn-md px-7">&nbsp;Hold&nbsp;</button>',
                default => '<button class="btn danger-btn-custom btn-md px-5">Not&nbsp;Verified</button>',
            };

            // Buttons
            $bgvCommentBtn = '<a href="'.route('admin.Green-Drive-Ev.hr_status.recruiter.bgv_comment_view', $val->id).'" class="me-1 icon-btn"><img src="'.asset('public/admin-assets/img/yellow_eye.jpg').'" class="rounded icon-btn"></a>';

            $bgvDocumentBtn = '<a href="'.route('admin.Green-Drive-Ev.hr_status.recruiter.bgv_documnet_view', $val->id).'" class="me-1 icon-btn"><img src="'.asset('public/admin-assets/img/green_eye.jpg').'" class="rounded icon-btn"></a>';

            $viewBtn = '<a href="'.route('admin.Green-Drive-Ev.hr_status.recruiter.preview', $val->id).'" class="me-1 icon-btn"><img src="'.asset('public/admin-assets/img/eye.jpg').'" class="rounded icon-btn"></a>';

            $queryBtn = '<div onclick="HrQuery_open_function('.$val->id.')"><img src="'.asset('public/admin-assets/img/blue_document.jpg').'" class="rounded icon-btn"></div>';

            $editBtn = '<a href="'.route('admin.Green-Drive-Ev.hr_status.edit_candidate', $val->id).'" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>';

            $deleteBtn = '<div onclick="route_alert(\''.route('admin.Green-Drive-Ev.delivery-man.delete', $val->id).'\',\'this Candidate\')"><img src="'.asset('public/admin-assets/img/delete_image.jpg').'" class="rounded icon-btn"></div>';

            $actionBtn = '';
            if ($val->approver_id == "" || $val->as_approve_datetime == "") {
                $actionBtn .= '<button class="btn success-btn-custom btn-md me-2 px-4" onclick="AcceptApplication_status(\''.route('admin.Green-Drive-Ev.delivery-man.application_status_approve', $val->id).'\', \'Approve this Candidate\')">Accept</button>';
                $actionBtn .= '<button class="btn danger-btn-custom btn-md" onclick="RejectApplication_status(\''.route('admin.Green-Drive-Ev.delivery-man.application_status_reject', $val->id).'\', \''.$val->id.'\', \'Reject this Candidate\')">Reject</button>';
            } else {
                if ($val->approved_status == 1) {
                    $actionBtn .= '<button class="btn btn-md me-2 px-4 w-100" style="border:1px solid #52c552 !important; color:#52c552 !important;">APPROVED</button>';
                } elseif ($val->approved_status == 2) {
                    $actionBtn .= '<a href="'.route('admin.Green-Drive-Ev.hr_status.reinitiate_candidate', $val->id).'" class="btn btn-md me-2 px-4 w-100" style="border:1px solid #f36263 !important; color:#f36263 !important;">REINITIATE</a>';
                } else {
                    $actionBtn .= '<button class="btn btn-md me-2 px-4 w-100" style="border:1px solid #f7f125 !important; color:#f7f125 !important;">N/A</button>';
                }
            }
            
                if ($val->work_type === 'in-house') {
                    $roleName = 'Employee';
                } elseif ($val->work_type === 'deliveryman') {
                    $roleName = 'Rider';
                } else {
                    $roleName = $val->work_type;
                }

            $data[] = [
                'id'                => $start + $key + 1,
                'candidate_id'      => $val->reg_application_id ?? '00000000000000',
                'gdm_id'            => $val->emp_id ?? '-',
                'image'             => '<div onclick="Profile_Image_View(\''.$image.'\')"><img src="'.$image.'" class="profile-image" /></div>',
                'full_name'         => $full_name,
                'email'             => $val->email ?? '',
                'location'          => $val->current_city->city_name ?? '',
                'submitted_at'      => $val->register_date_time ? date('d M Y', strtotime($val->register_date_time)) : '',
                'role'              => ucfirst($roleName) ?? '',
                'role_type'         => $val->RiderType->type ?? '',
                'probation_from'    => $val->probation_from_date ? date('d M Y', strtotime($val->probation_from_date)) : '-',
                'probation_to'      => $val->probation_to_date ? date('d M Y', strtotime($val->probation_to_date)) : '-',
                'application_status'=> $applicationStatus,
                'bgv_status'        => $bgvStatus,
                'bgv_comment'       => $bgvCommentBtn,
                'bgv_documents'     => $bgvDocumentBtn,
                'view'              => $viewBtn,
                'query'             => $queryBtn,
                'edit'              => $editBtn,
                'delete'            => $deleteBtn,
                'action'            => $actionBtn,
            ];
        }

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    $cities = City::where('status', 1)->get();
    return view('hrstatus::recruiter_list', compact('cities'));
}

    public function update_approve_candidate(Request $request)
    {
        $request->merge([
            'aadhar_number' => preg_replace('/\s+/', '', $request->aadhar_number),
        ]);
    
        $id = $request->dm_id;
        $dm = Deliveryman::findOrFail($id);
        // dd($dm);
    
        // Validation
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|size:13|regex:/^\+91[0-9]{10}$/|unique:ev_tbl_delivery_men,mobile_number,'. $id,
            'gender' => 'required|in:male,female',
            'house_no' => 'required|string|max:255',
            'street_name' => 'required|string|max:255',
            'pincode' => ['required','digits:6','regex:/^[1-9][0-9]{5}$/'],
            'alternative_number' => 'required|string|size:13|regex:/^\+91[0-9]{10}$/|unique:ev_tbl_delivery_men,alternative_number,'. $id,
            'email_id' => 'required|email|unique:ev_tbl_delivery_men,email,' . $id,
            'current_city_id' => 'required|integer',
            'interested_city_id' => 'required|integer',
            'role' => 'required|in:deliveryman,in-house,adhoc,helper',
            'photo' => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',//1MB Accept
            'aadhar_card_front' => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',//1MB Accept
            'aadhar_card_back' => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',//1MB Accept
            'aadhar_number' => 'required|digits:12|unique:ev_tbl_delivery_men,aadhar_number,' . $id,
            'pan_card_front' => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',
            'pan_number' => 'required|string|max:10',
            'driving_license_front' => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',//1MB Accept
            'driving_license_back' => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',//1MB Accept
            'bank_passbook' => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',//1MB Accept
            'bank_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string',
            'account_number' => 'required|string|max:20',
            'account_holder_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'present_address' => 'required|string|max:500',
            'permanent_address' => 'required|string|max:500',
            'father_name' => 'nullable|string|max:255',
            'father_mobile_number' => 'nullable|string|max:13',
            'mother_name' => 'nullable|string|max:255',
            'mother_mobile_number' => 'nullable|string|max:13',
            'referal_person_name' => 'nullable|string|max:255',
            'referal_person_number' => 'nullable|string|max:255',
            'referal_person_relationship' => 'nullable|string|max:255',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_mobile_number' => 'nullable|string|max:13',
            'emergency_contact_person_1_name' => 'nullable|string|max:255',
            'emergency_contact_person_1_mobile' => 'nullable|string|max:13',
            'emergency_contact_person_2_name' => 'nullable|string|max:255',
            'emergency_contact_person_2_mobile' => 'nullable|string|max:13',
            'blood_group' => 'required|string|max:3',
            'emp_prev_company_id' => 'nullable',
            'emp_prev_experience' => 'nullable',
            'social_links' => 'nullable',
            'bank_statements' => 'nullable|max:1024',//1MB Accept
            'license_number' => 'nullable|unique:ev_tbl_delivery_men,license_number,' . $id,
            'marital_status' => 'nullable',
        ]);
    
        // Conditional validation for LLR or License
        if ($request->role !== "in-house") {
            if (strtolower($request->is_llr) === "1" || strtolower($request->is_llr) === "true") {
                $validator->addRules([
                    'llr_number' => 'required|unique:ev_tbl_delivery_men,llr_number,' . $id,
                    'llr_image' => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',//1MB Accept
                ]);
            } else {
                $validator->addRules([
                    'license_number' => 'required|unique:ev_tbl_delivery_men,license_number,' . $id,
                    'driving_license_front' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:1024',//1MB Accept
                    'driving_license_back' => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',//1MB Accept
                ]);
            }
        }
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
        try {
            // Update individual fields
            $dm->first_name = $request->first_name;
            $dm->last_name = $request->last_name;
            $dm->mobile_number = $request->mobile_number;
            $dm->gender = $request->gender;
            $dm->house_no = $request->house_no;
            $dm->street_name = $request->street_name;
            $dm->pincode = $request->pincode;
            $dm->alternative_number = $request->alternative_number;
            $dm->email = $request->email_id;
            $dm->current_city_id = $request->current_city_id;
            $dm->interested_city_id = $request->interested_city_id;
            $dm->work_type = $request->role;
            $dm->aadhar_number = $request->aadhar_number;
            $dm->pan_number = $request->pan_number;
            $dm->license_number = $request->license_number ?? null;
            $dm->llr_number = $request->llr_number ?? null;
            $dm->bank_name = $request->bank_name;
            $dm->ifsc_code = $request->ifsc_code;
            $dm->account_number = $request->account_number;
            $dm->account_holder_name = $request->account_holder_name;
            $dm->date_of_birth = $request->date_of_birth;
            $dm->present_address = $request->present_address;
            $dm->permanent_address = $request->permanent_address;
            $dm->father_name = $request->father_name;
            $dm->father_mobile_number = $request->father_mobile_number;
            $dm->mother_name = $request->mother_name;
            $dm->mother_mobile_number = $request->mother_mobile_number;
            $dm->referal_person_name = $request->referal_person_name;
            $dm->referal_person_number = $request->referal_person_number;
            $dm->referal_person_relationship = $request->referal_person_relationship;
            $dm->spouse_name = $request->spouse_name;
            $dm->spouse_mobile_number = $request->spouse_mobile_number;
            $dm->emergency_contact_person_1_name = $request->emergency_contact_person_1_name;
            $dm->emergency_contact_person_1_mobile = $request->emergency_contact_person_1_mobile;
            $dm->emergency_contact_person_2_name = $request->emergency_contact_person_2_name;
            $dm->emergency_contact_person_2_mobile = $request->emergency_contact_person_2_mobile;
            $dm->blood_group = $request->blood_group;
            $dm->emp_prev_company_id = $request->emp_prev_company_id;
            $dm->emp_prev_experience = $request->emp_prev_experience;
            $dm->social_links = $request->social_links;
            $dm->marital_status = $request->marital_status;
            
            if (isset($request->role) && $request->role == "in-house") {
                $dm->work_type = 'in-house';
            } else if (isset($request->role) && $request->role == "adhoc") {
                $dm->work_type = 'adhoc';
            } else if (isset($request->role) && $request->role == "helper") {
                $dm->work_type = 'helper';
            } else {
                $dm->work_type = $request->role;
            }
            

            
            
    
            // Handle file uploads individually and delete previous files
            $fileFields = [
                'photo' => 'EV/images/photos',
                'aadhar_card_front' => 'EV/images/aadhar',
                'aadhar_card_back' => 'EV/images/aadhar',
                'pan_card_front' => 'EV/images/pan',
                'pan_card_back' => 'EV/images/pan',
                'driving_license_front' => 'EV/images/driving_license',
                'driving_license_back' => 'EV/images/driving_license',
                'llr_image' => 'EV/images/llr_images',
                'bank_passbook' => 'EV/images/bank_passbook',
                'bank_statements' => 'EV/images/bank_statements',
            ];
            
            
    
            foreach ($fileFields as $field => $path) {
                if ($request->hasFile($field)) {

                // Delete old file if exists
                if (!empty($dm->$field)) {
                    // Extract only the filename in case it's a full URL
                    $oldFileName = basename($dm->$field);
                    $oldFilePath = public_path($path . '/' . $oldFileName);
        
                    
        
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath); // Delete previous file
                    }
                }


                    $dm->$field = $this->uploadFile($request->file($field), $path);
                }
            }
            
            

    
            $riderType = match ($dm->work_type) {
                'deliveryman' => 'R',
                'in-house' => 'E',
                'adhoc' => 'A',
                'helper' => 'H',
                default => 'N/A',
            };
    

            if ($request->role == "in-house"){
                 $dm->vehicle_type = null;
                 $dm->rider_type = null;
                 $dm->license_number =  null;
                 $dm->driving_license_front = null;
                 $dm->driving_license_back = null;
                 $dm->llr_image = null;
            }
            else{
                 $dm->vehicle_type = $request->vehicle_type;
                 $dm->rider_type = $request->rider_type;
            }
            
            
            
            if ($dm->reg_application_id) {
                // Remove first 6 characters (GDMAPP) to get old numeric + type
                $rest = substr($dm->reg_application_id, 6); // e.g., "R00374"
                // Get numeric part (everything except last char)
                $numberPart = substr($rest, 1); // "00374"
                // Build new ID with correct prefix + numeric + rider type
                $dm->reg_application_id = 'GDMAPP' .$riderType. $numberPart ;
            }
                         

    
            $dm->save();
            
            try {
            // $dm = Deliveryman::find($id);
            // if (!$dm) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Rider not found.'
            //     ]);
            // }
            
            if ($dm->kyc_verify != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'BGV status not verified. Please complete BGV verification before proceeding further.'
                ]);
            }

            $notVerified = [];
          
            if (!$dm->aadhar_verify) $notVerified[] = 'Aadhar';
            if (!$dm->pan_verify) $notVerified[] = 'PAN';
            if (!$dm->bank_verify) $notVerified[] = 'BANK';

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
    
            $this->status_handle_whatsapp_message($dm->id, 'approve');
            
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
    
            return response()->json([
                'success' => true,
                'message' => 'Candidate updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
            
            // return response()->json([
            //     'success' => true,
            //     'message' => 'Candidate updated successfully',
            // ]);
    
        } catch (\Exception $e) {
            Log::error('Error updating Deliveryman: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while updating the Candidate. Please try again.',
            ], 500);
        }
    }
    
    public function reinitiate_candidate(Request $request , $id)
    {
         $city = City::where('status', 1)->get();
         
      $data = Deliveryman::find($id);
      $rider_types = RiderType::where('status', 1)->get();
      

        return view('hrstatus::reinitiate_candidate', compact('city', 'data' ,'rider_types'));
           
    }
    
    public function status_handle_whatsapp_message($id, $type)
    {
        $dm = Deliveryman::where('id', $id)->first();
    
        if (!$dm) {
            Log::error("Deliveryman not found with ID: " . $id);
            return false;
        }
    
        $phone = str_replace('+', '', $dm->mobile_number);
           if ($dm->work_type == 'deliveryman') {
             $role = "Delivery Partner";
            } elseif ($dm->work_type == 'in-house') {
                $role = "Employee";
            } else {
                $role = "Member";
            }
        
            $message = "Dear " . $dm->first_name . " " . $dm->last_name . ",\n\n" .
                       "Your role as *" . $role . "* has been approved by the Admin!\n\n" .
                       "Best regards,\n" .
                       "GreenDriveConnect";

        $apiKey = BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
        // $api_key = env('WHATSAPP_API_KEY');
        $api_key = $apiKey;
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
    
    // public function recruiter_preview(Request $request,$id)
    // {

    //     $dm = Deliveryman::where('delete_status', 0)->where('id',$id)->first();
    //     $cities = City::all();
    //     $areas = Area::where('city_id',$dm->current_city_id)->get();
    //     $rider_types = RiderType::where('status', 1)->get();
    //      if (!$dm) {
    //         return back()->with('error', 'Rider Not found');
    //     }

    //     return view('hrstatus::recruiter_preview', compact('dm','cities','areas','rider_types'));
    // }
    
     public function recruiter_preview(Request $request,$id)
    {

        $dm = Deliveryman::where('delete_status', 0)->where('id',$id)->first();
        // $cities = City::all();
        $areas = Area::where('city_id',$dm->current_city_id)->get();
        $rider_types = RiderType::where('status', 1)->get();
         if (!$dm) {
            return back()->with('error', 'Rider Not found');
        }
        $roles = Role::where('id',22)->get();
        $cities = City::where('id',$dm->current_city_id)->where('status',1)->get();
        $zones = Zones::where('city_id',$dm->current_city_id)->where('status',1)->get();
        return view('hrstatus::recruiter_preview', compact('dm','cities','areas','rider_types','roles','zones'));
    }
    
    public function update_teams(Request $request) //updated by Logesh
    {
        $request->validate([
            'id' => 'required|exists:ev_tbl_delivery_men,id',
            'team_type' => 'nullable',
            'city_id' =>'nullable',
            'zone_id' => 'nullable'
        ]);
        
        $agent = Deliveryman::where('id',$request->id)->first();
        
        if($agent->approved_status != 1){
           return response()->json([
            'status' => false,
            'message' => 'The employee status is not approved. Please approve the employee and try again.'
        ]); 
        }
        $agent->team_type = $request->team_type;
        $agent->zone_id = $request->zone_id;
        $agent->save();
        
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
 
        $candidateName = trim($agent->first_name . ' ' . ($agent->last_name ?? ''));
        $applicationID = $agent->reg_application_id ?? 'N/A';

        
        // Conditional log messages
        if (!empty($request->team_type)) {
            $shortDescription = 'Candidate Assigned to Recovery Team';
            $longDescription  = "User assigned candidate {$candidateName} (Application ID: {$applicationID}) to the Recovery Team through the HR Management.";
        } else {
            $shortDescription = 'Candidate Removed from Recovery Team';
            $longDescription  = "User removed candidate {$candidateName} (Application ID: {$applicationID}) from the Recovery Team through the HR Management.";
        }
        
        $managerEmails = User::where('role', 24)
            ->where('city_id', $agent->current_city_id)
            ->pluck('email')
            ->toArray();
        $adminEmails = User::whereIn('role', [1, 13])
            ->pluck('email')
            ->toArray();
        // Store audit log
        audit_log_after_commit([
            'module_id'         => 2,
            'short_description' => $shortDescription,
            'long_description'  => $longDescription,
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'recruiters.assign_recovery_team',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent(),
        ]);

        if (!empty($agent->email)) {
        $performedByName = optional(Auth::user())->name ?? 'System';
        $action = !empty($request->team_type) ? 'assigned' : 'removed';
    
        try {
            // Mail::to('gowthamsakthi2520@gmail.com')
            Mail::to($agent->email)
                ->cc([$managerEmails]) // optional: cc HR or other recipients
                ->bcc([$adminEmails])
                ->queue(new RecoveryAgentChanged($agent, $action, $performedByName, $roleName));
        } catch (\Exception $e) {
            // Optionally log mail sending failure � don't fail the whole request
            \Log::error('AgentTeamChanged mail failed: ' . $e->getMessage());
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Team updated successfully.'
        ]);
    }
    }
    
            public function add_candidate(Request $request)
    {
         $city = City::where('status', 1)->get();
         $rider_types = RiderType::where('status', 1)->get();
       
        return view('hrstatus::add_candidate' ,compact('city' ,'rider_types'));
        
        
    }
    
    public function edit_candidate(Request $request , $id)
    {
         $city = City::where('status', 1)->get();
         
      $data = Deliveryman::find($id);
      $rider_types = RiderType::where('status', 1)->get();
      

        return view('hrstatus::edit_candidate', compact('city', 'data' ,'rider_types'));
           
    }
    
    
           public function store_candidate(Request $request)
    {
        


        // Remove spaces from Aadhar number before validation
        $request->merge([
            'aadhar_number' => preg_replace('/\s+/', '', $request->aadhar_number),
        ]);
    
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => [
                'required',
                'string',
                'size:13', 
                'regex:/^\+91[0-9]{10}$/',
            ],
            'gender' => 'required|in:male,female',
            'house_no' => 'required|string|max:255',
            'street_name' => 'required|string|max:255',
            'pincode' => ['required', 'digits:6', 'regex:/^[1-9][0-9]{5}$/'],
            'alternative_number' => [
                'required',
                'string',
                'size:13',
                'regex:/^\+91[0-9]{10}$/',
                'unique:ev_tbl_delivery_men,alternative_number',
            ],
            'email_id' => 'required|email|unique:ev_tbl_delivery_men,email',
            'current_city_id' => 'required|integer',
            'interested_city_id' => 'required|integer',
            'role' => 'required|in:deliveryman,in-house,adhoc,helper',
            'photo' => 'required|mimes:jpg,jpeg,png,pdf', 
            'aadhar_card_front' => 'required|mimes:jpg,jpeg,png,pdf',
            'aadhar_card_back' => 'required|mimes:jpg,jpeg,png,pdf',
            'aadhar_number' => ['required', 'digits:12', 'unique:ev_tbl_delivery_men,aadhar_number'],
            'pan_card_front' => 'required|mimes:jpg,jpeg,png,pdf',
            'pan_number' => 'required|string|max:10',
            'driving_license_front' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'driving_license_back' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'bank_passbook' => 'required|mimes:jpg,jpeg,png,pdf',
            'bank_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string',
            'account_number' => 'required|string|max:20',
            'account_holder_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'present_address' => 'required|string|max:500',
            'permanent_address' => 'required|string|max:500',
            'father_name' => 'nullable|string|max:255',
            'father_mobile_number' => 'nullable|string|max:13',
            'mother_name' => 'nullable|string|max:255',
            'mother_mobile_number' => 'nullable|string|max:13',
            'referal_person_number' => 'nullable|string|max:255', 
            'referal_person_name' => 'nullable|string|max:255',
            'referal_person_relationship' => 'nullable|string|max:255',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_mobile_number' => 'nullable|string|max:13',
            'emergency_contact_person_1_name' => 'nullable|string|max:255',
            'emergency_contact_person_1_mobile' => 'nullable|string|max:13',
            'emergency_contact_person_2_name' => 'nullable|string|max:255',
            'emergency_contact_person_2_mobile' => 'nullable|string|max:13',
            'blood_group' => 'required|string|max:3',
            'emp_prev_company_id' => 'nullable',
            'emp_prev_experience' => 'nullable',
            'social_links' => 'nullable',
            'vehicle_type' => 'nullable',
            'rider_type' => 'nullable',
            'bank_statements' => 'nullable',
            'license_number' => 'nullable|unique:ev_tbl_delivery_men,license_number',
            'marital_status' => 'nullable',
        ]);
    
        // Conditional validation for LLR or License only if NOT in-house
        if ($request->role !== "in-house" && ($request->role == "deliveryman" || $request->role == "adhoc" || $request->role == "helper")) {
            if (strtolower($request->is_llr) === "1" || strtolower($request->is_llr) === "true") {
                $validator->addRules([
                    'llr_number' => 'required|unique:ev_tbl_delivery_men,llr_number',
                    'llr_image' => 'required|mimes:jpg,jpeg,png,pdf',
                ]);
            } else {
                $validator->addRules([
                    'license_number' => 'required|unique:ev_tbl_delivery_men,license_number',
                    'driving_license_front' => 'required|mimes:jpg,jpeg,png,pdf',
                    'driving_license_back' => 'required|mimes:jpg,jpeg,png,pdf',
                ]);
            }
        }
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422); 
        }
    
        try {
            $dm = new Deliveryman();
            $dm->first_name = $request->first_name;
            $dm->last_name = $request->last_name;
            $dm->mobile_number = $request->mobile_number;
            $dm->gender = $request->gender;
            $dm->house_no = $request->house_no;
            $dm->street_name = $request->street_name;
            $dm->pincode = $request->pincode;
            $dm->alternative_number = $request->alternative_number;
            $dm->email = $request->email_id;
            $dm->current_city_id = $request->current_city_id;
            $dm->interested_city_id = $request->interested_city_id;
            $dm->remarks = $request->remarks ?? null;
            $dm->work_type = $request->role;
            
            if ($request->role == "in-house"){
                 $dm->vehicle_type = null;
                  $dm->rider_type = null;
            }
            else{
                $dm->vehicle_type = $request->vehicle_type;
                  $dm->rider_type = $request->rider_type;
            }
            
    
            if ($request->hasFile('photo')) {
                $dm->photo = $this->uploadFile($request->file('photo'), 'EV/images/photos');
            }
            if ($request->hasFile('bank_statements')) {
                $dm->bank_statements = $this->uploadFile($request->file('bank_statements'), 'EV/images/bank_statements');
            }
            if ($request->hasFile('aadhar_card_front')) {
                $dm->aadhar_card_front = $this->uploadFile($request->file('aadhar_card_front'), 'EV/images/aadhar');
            }
            if ($request->hasFile('aadhar_card_back')) {
                $dm->aadhar_card_back = $this->uploadFile($request->file('aadhar_card_back'), 'EV/images/aadhar');
            }
            if ($request->hasFile('pan_card_front')) {
                $dm->pan_card_front = $this->uploadFile($request->file('pan_card_front'), 'EV/images/pan');
            }
            if ($request->hasFile('pan_card_back')) {
                $dm->pan_card_back = $this->uploadFile($request->file('pan_card_back'), 'EV/images/pan');
            }
    
            if (strtolower($request->is_llr) === "true" || strtolower($request->is_llr) === "1") {
                if ($request->hasFile('llr_image')) {
                    $dm->llr_image = $this->uploadFile($request->file('llr_image'), 'EV/images/llr_images');
                } else {
                    $dm->llr_image = null;
                }
            } else {
                if ($request->hasFile('driving_license_front')) {
                    $dm->driving_license_front = $this->uploadFile($request->file('driving_license_front'), 'EV/images/driving_license') ?? null;
                } else {
                    $dm->driving_license_front = null;
                }
            }
    
            if ($request->hasFile('driving_license_back')) {
                $dm->driving_license_back = $this->uploadFile($request->file('driving_license_back'), 'EV/images/driving_license');
            } else {
                $dm->driving_license_back = null;
            }
            if ($request->hasFile('bank_passbook')) {
                $dm->bank_passbook = $this->uploadFile($request->file('bank_passbook'), 'EV/images/bank_passbook');
            }
            $dm->license_number = $request->license_number ?? null;
            $dm->llr_number = $request->llr_number ?? null;
            $dm->aadhar_number = $request->aadhar_number ?? null;
            $dm->pan_number = $request->pan_number ?? null;
            $dm->bank_name = $request->bank_name ?? null;
            $dm->ifsc_code = $request->ifsc_code ?? null;
            $dm->account_number = $request->account_number ?? null;
            $dm->account_holder_name = $request->account_holder_name ?? null;
            $dm->date_of_birth = $request->date_of_birth ?? null;
            $dm->present_address = $request->present_address ?? null;
            $dm->permanent_address = $request->permanent_address ?? null;
            $dm->father_name = $request->father_name ?? null;
            $dm->father_mobile_number = $request->father_mobile_number ?? null;
            $dm->mother_name = $request->mother_name ?? null;
            $dm->mother_mobile_number = $request->mother_mobile_number;
            $dm->marital_status = $request->marital_status ?? 0;
            $dm->spouse_name = $request->spouse_name ?? null;
            $dm->spouse_mobile_number = $request->spouse_mobile_number ?? null;
            $dm->emergency_contact_person_1_name = $request->emergency_contact_person_1_name ?? null;
            $dm->emergency_contact_person_1_mobile = $request->emergency_contact_person_1_mobile ?? null;
            $dm->emergency_contact_person_2_name = $request->emergency_contact_person_2_name ?? null;
            $dm->emergency_contact_person_2_mobile = $request->emergency_contact_person_2_mobile ?? null;
            $dm->blood_group = $request->blood_group ?? null;
            $dm->emp_prev_company_id = $request->emp_prev_company_id ?? null;
            $dm->emp_prev_experience = $request->emp_prev_experience ?? null;
            $dm->social_links = $request->social_links ?? null;
            $dm->fcm_token = $request->fcm_token ?? null;
            $dm->referal_person_name = $request->referal_person_name ?? null;
            $dm->referal_person_number = $request->referal_person_number ?? null;
            $dm->referal_person_relationship = $request->referal_person_relationship ?? null;
            $dm->register_date_time = Carbon::now();
    
             if (isset($request->role) && $request->role == "in-house") {
                $dm->work_type = 'in-house';
            } else if (isset($request->role) && $request->role == "adhoc") {
                $dm->work_type = 'adhoc';
            } else if (isset($request->role) && $request->role == "helper") {
                $dm->work_type = 'helper';
            } else {
                $dm->work_type = $request->role;
            }
    
            $riderType = match ($dm->work_type) {
                'deliveryman' => 'R',
                'in-house' => 'E',
                'adhoc' => 'A',
                'helper' => 'H',
                default => 'N/A',
            };
    
            $id_start = 'GDMAPP' . $riderType;
    
            $lastId = Deliveryman::where('delete_status', 0)
                ->where('reg_application_id', 'like', $id_start . '%')
                ->orderByDesc('reg_application_id')
                ->value('reg_application_id');
    
            $lastSerial = $lastId ? (int)substr($lastId, -5) : 0;
    
            do {
                $lastSerial++;
                $newSerial = str_pad((string)$lastSerial, 5, '0', STR_PAD_LEFT);
                $reg_application_id = $id_start . $newSerial;
                $exists = Deliveryman::where('reg_application_id', $reg_application_id)->exists();
            } while ($exists);
    
            $dm->reg_application_id = $reg_application_id;
    
             
    
    
            $dm->save();
            
            $readableWorkType = match ($dm->work_type) {
                'in-house' => 'Employee',
                'deliveryman' => 'Deliveryman',
                'adhoc' => 'Adhoc',
                'helper' => 'Helper',
                default => ucfirst($dm->work_type),
            };
                
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    
            audit_log_after_commit([
                'module_id'         => 2,
                'short_description' => 'New Candidate Created!',
                'long_description'  => "A new candidate record was created through the HR Management module. The candidate was classified as a {$readableWorkType}, and the Application ID is {$dm->reg_application_id}.",
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'recruiters.store',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent(),
            ]);

    
            return response()->json([
                'success' => true,
                'message' => 'Candidate registered successfully',
            ]);
    
        } catch (\Exception $e) {
            Log::error('Error creating Deliveryman: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while creating the Rider. Please try again.',
            ], 500); 
        }
    }
    
    public function update_candidate(Request $request)
    {
        $request->merge([
            'aadhar_number' => preg_replace('/\s+/', '', $request->aadhar_number),
        ]);
    
        $id = $request->dm_id;
        $dm = Deliveryman::findOrFail($id);
        // dd($dm);
    
        // Validation
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|size:13|regex:/^\+91[0-9]{10}$/|unique:ev_tbl_delivery_men,mobile_number,' . $id,
            'gender' => 'required|in:male,female',
            'house_no' => 'required|string|max:255',
            'street_name' => 'required|string|max:255',
            'pincode' => ['required','digits:6','regex:/^[1-9][0-9]{5}$/'],
            'alternative_number' => 'required|string|size:13|regex:/^\+91[0-9]{10}$/|unique:ev_tbl_delivery_men,alternative_number,' . $id,
            'email_id' => 'required|email|unique:ev_tbl_delivery_men,email,' . $id,
            'current_city_id' => 'required|integer',
            'interested_city_id' => 'required|integer',
            'role' => 'required|in:deliveryman,in-house,adhoc,helper',
            'photo' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'aadhar_card_front' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'aadhar_card_back' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'aadhar_number' => 'required|digits:12|unique:ev_tbl_delivery_men,aadhar_number,' . $id,
            'pan_card_front' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'pan_number' => 'required|string|max:10',
            'driving_license_front' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'driving_license_back' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'bank_passbook' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'bank_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string',
            'account_number' => 'required|string|max:20',
            'account_holder_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'present_address' => 'required|string|max:500',
            'permanent_address' => 'required|string|max:500',
            'father_name' => 'nullable|string|max:255',
            'father_mobile_number' => 'nullable|string|max:13',
            'mother_name' => 'nullable|string|max:255',
            'mother_mobile_number' => 'nullable|string|max:13',
            'referal_person_name' => 'nullable|string|max:255',
            'referal_person_number' => 'nullable|string|max:255',
            'referal_person_relationship' => 'nullable|string|max:255',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_mobile_number' => 'nullable|string|max:13',
            'emergency_contact_person_1_name' => 'nullable|string|max:255',
            'emergency_contact_person_1_mobile' => 'nullable|string|max:13',
            'emergency_contact_person_2_name' => 'nullable|string|max:255',
            'emergency_contact_person_2_mobile' => 'nullable|string|max:13',
            'blood_group' => 'required|string|max:3',
            'emp_prev_company_id' => 'nullable',
            'emp_prev_experience' => 'nullable',
            'social_links' => 'nullable',
            'bank_statements' => 'nullable',
            'license_number' => 'nullable|unique:ev_tbl_delivery_men,license_number,' . $id,
            'marital_status' => 'nullable',
        ]);
    
        // Conditional validation for LLR or License
        if ($request->role !== "in-house") {
            if (strtolower($request->is_llr) === "1" || strtolower($request->is_llr) === "true") {
                $validator->addRules([
                    'llr_number' => 'required|unique:ev_tbl_delivery_men,llr_number,' . $id,
                    'llr_image' => 'nullable|mimes:jpg,jpeg,png,pdf',
                ]);
            } else {
                $validator->addRules([
                    'license_number' => 'required|unique:ev_tbl_delivery_men,license_number,' . $id,
                    'driving_license_front' => 'nullable|image|mimes:jpg,jpeg,png,pdf',
                    'driving_license_back' => 'nullable|mimes:jpg,jpeg,png,pdf',
                ]);
            }
        }
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
        try {
            // Update individual fields
            $dm->first_name = $request->first_name;
            $dm->last_name = $request->last_name;
            $dm->mobile_number = $request->mobile_number;
            $dm->gender = $request->gender;
            $dm->house_no = $request->house_no;
            $dm->street_name = $request->street_name;
            $dm->pincode = $request->pincode;
            $dm->alternative_number = $request->alternative_number;
            $dm->email = $request->email_id;
            $dm->current_city_id = $request->current_city_id;
            $dm->interested_city_id = $request->interested_city_id;
            $dm->work_type = $request->role;
            $dm->aadhar_number = $request->aadhar_number;
            $dm->pan_number = $request->pan_number;
            $dm->license_number = $request->license_number ?? null;
            $dm->llr_number = $request->llr_number ?? null;
            $dm->bank_name = $request->bank_name;
            $dm->ifsc_code = $request->ifsc_code;
            $dm->account_number = $request->account_number;
            $dm->account_holder_name = $request->account_holder_name;
            $dm->date_of_birth = $request->date_of_birth;
            $dm->present_address = $request->present_address;
            $dm->permanent_address = $request->permanent_address;
            $dm->father_name = $request->father_name;
            $dm->father_mobile_number = $request->father_mobile_number;
            $dm->mother_name = $request->mother_name;
            $dm->mother_mobile_number = $request->mother_mobile_number;
            $dm->referal_person_name = $request->referal_person_name;
            $dm->referal_person_number = $request->referal_person_number;
            $dm->referal_person_relationship = $request->referal_person_relationship;
            $dm->spouse_name = $request->spouse_name;
            $dm->spouse_mobile_number = $request->spouse_mobile_number;
            $dm->emergency_contact_person_1_name = $request->emergency_contact_person_1_name;
            $dm->emergency_contact_person_1_mobile = $request->emergency_contact_person_1_mobile;
            $dm->emergency_contact_person_2_name = $request->emergency_contact_person_2_name;
            $dm->emergency_contact_person_2_mobile = $request->emergency_contact_person_2_mobile;
            $dm->blood_group = $request->blood_group;
            $dm->emp_prev_company_id = $request->emp_prev_company_id;
            $dm->emp_prev_experience = $request->emp_prev_experience;
            $dm->social_links = $request->social_links;
            $dm->marital_status = $request->marital_status;
            
            if (isset($request->role) && $request->role == "in-house") {
                $dm->work_type = 'in-house';
            } else if (isset($request->role) && $request->role == "adhoc") {
                $dm->work_type = 'adhoc';
            } else if (isset($request->role) && $request->role == "helper") {
                $dm->work_type = 'helper';
            } else {
                $dm->work_type = $request->role;
            }
            

            
            
    
            // Handle file uploads individually and delete previous files
            $fileFields = [
                'photo' => 'EV/images/photos',
                'aadhar_card_front' => 'EV/images/aadhar',
                'aadhar_card_back' => 'EV/images/aadhar',
                'pan_card_front' => 'EV/images/pan',
                'pan_card_back' => 'EV/images/pan',
                'driving_license_front' => 'EV/images/driving_license',
                'driving_license_back' => 'EV/images/driving_license',
                'llr_image' => 'EV/images/llr_images',
                'bank_passbook' => 'EV/images/bank_passbook',
                'bank_statements' => 'EV/images/bank_statements',
            ];
            
            
    
            foreach ($fileFields as $field => $path) {
                if ($request->hasFile($field)) {

                // Delete old file if exists
                if (!empty($dm->$field)) {
                    // Extract only the filename in case it's a full URL
                    $oldFileName = basename($dm->$field);
                    $oldFilePath = public_path($path . '/' . $oldFileName);
        
                    
        
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath); // Delete previous file
                    }
                }


                    $dm->$field = $this->uploadFile($request->file($field), $path);
                }
            }
            
            

    
            $riderType = match ($dm->work_type) {
                'deliveryman' => 'R',
                'in-house' => 'E',
                'adhoc' => 'A',
                'helper' => 'H',
                default => 'N/A',
            };
    

            if ($request->role == "in-house"){
                 $dm->vehicle_type = null;
                 $dm->rider_type = null;
                 $dm->license_number =  null;
                 $dm->driving_license_front = null;
                 $dm->driving_license_back = null;
                 $dm->llr_image = null;
            }
            else{
                 $dm->vehicle_type = $request->vehicle_type;
                 $dm->rider_type = $request->rider_type;
            }
            
            
            
            if ($dm->reg_application_id) {
                // Remove first 6 characters (GDMAPP) to get old numeric + type
                $rest = substr($dm->reg_application_id, 6); // e.g., "R00374"
                // Get numeric part (everything except last char)
                $numberPart = substr($rest, 1); // "00374"
                // Build new ID with correct prefix + numeric + rider type
                $dm->reg_application_id = 'GDMAPP' .$riderType. $numberPart ;
            }
                         
            $originalData = $dm->getOriginal();
            // $dm->fill($request->all());


            $changes = [];
            $ignoredFields = ['updated_at', 'created_at', 'id'];
            
            foreach ($dm->getAttributes() as $field => $newValue) {
                if (!in_array($field, $ignoredFields)) {
                    $oldValue = $originalData[$field] ?? null;
                        
                    if (in_array($field, ['date_of_birth'])) {
                        $oldValue = $oldValue ? date('Y-m-d', strtotime($oldValue)) : null;
                        $newValue = $newValue ? date('Y-m-d', strtotime($newValue)) : null;
                    }
        
        
                    if ($oldValue != $newValue) {
                        // Mask sensitive data
                        if (in_array($field, ['aadhar_number', 'account_number'])) {
                            $oldValue = $oldValue ? substr($oldValue, 0, 4) . '****' : 'N/A';
                            $newValue = $newValue ? substr($newValue, 0, 4) . '****' : 'N/A';
                        }
            
                        $oldValue = $oldValue ?? 'N/A';
                        $newValue = $newValue ?? 'N/A';
            
                        $changes[] = ucfirst(str_replace('_', ' ', $field)) . " changed from '{$oldValue}' to '{$newValue}'";
                    }
                }
            }
    
            $dm->save();
            

            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
            

            
            $changesDescription = count($changes)
                ? implode('; ', $changes)
                : "Candidate details were updated with no significant data changes.";
            
          
            audit_log_after_commit([
                'module_id'         => 2,
                'short_description' => 'Candidate Details Updated',
                'long_description'  => "Candidate record (Application ID: {$dm->reg_application_id}) was updated in the HR Management module. {$changesDescription}",
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'recruiters.update',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent(),
            ]);


    
            return response()->json([
                'success' => true,
                'message' => 'Candidate updated successfully',
            ]);
    
        } catch (\Exception $e) {
            Log::error('Error updating Deliveryman: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while updating the Candidate. Please try again.',
            ], 500);
        }
    }



    public function recruiter_bgv_comment_view(Request $request,$id)
    {

        $dm = Deliveryman::where('delete_status', 0)->where('id',$id)->first();
        if (!$dm) {
            return back()->with('error', 'Rider Not found');
        }
        $comments = BgvComment::where('dm_id',$dm->id)->where('comment_type','bgv_vendor')->get();

        return view('hrstatus::hr_bgv_comments_view', compact('dm','comments'));
    }
    
    public function recruiter_bgv_document_view(Request $request,$id)
    {

        $dm = Deliveryman::where('delete_status', 0)->where('id',$id)->first();
        if (!$dm) {
            return back()->with('error', 'Rider Not found');
        }
        $documents = BgvDocument::where('dm_id',$dm->id)->where('doc_type','bgv_vendor')->get();

        return view('hrstatus::hr_bgv_documents_view', compact('dm','documents'));
    }
    
    public function recruiter_query_add(Request $request)
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
            
            $query = new HrQuery();
            $query->dm_id = $request->dm_id;
            $query->remarks = $request->remarks;
            $query->auth_id = auth()->id();
            $query->query_type = 'hr_query';
            $query->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Query Added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
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
        
        if(!empty($type) && $type == "approved_riders"){ 
            $query->where('rider_status',3);
        }
        
        if(!empty($type) && $type == "rejected_riders"){
            $query->where('rider_status',2);
        }
        
        if(!empty($type) && $type == "live_riders"){
            $query->where('rider_status',1);
        }
        
        if(!empty($type) && $type == "probation_riders"){ 
            $query->where('rider_status',3);
        }
        
        // dd($type,$from_date,$to_date,$roll_type,$city_id,$bgv_status);
    
        $lists = $query->orderBy('id','desc')->get();
        $cities = City::where('status',1)->get();
        return view('hrstatus::hr_dashboard_filter_data', compact('lists','cities','type','roll_type','from_date', 'to_date','city_id','bgv_status'));
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hrstatus::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hrstatus::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('hrstatus::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('hrstatus::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
    
            public function uploadFile($file, $directory)
    {
        $imageName = Str::uuid(). '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $imageName);
        return $imageName; // Return the name of the uploaded file
    }
}
