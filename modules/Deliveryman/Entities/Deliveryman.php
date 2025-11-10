<?php

namespace Modules\Deliveryman\Entities;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Deliveryman\Database\factories\DeliverymanFactory;
use Modules\VehicleManagement\Entities\VehicleType;
use Carbon\Carbon;
use Modules\Zones\Entities\Zones;
use Modules\City\Entities\City;
use Modules\City\Entities\Area;
use Modules\Clients\Entities\Client;
use Modules\Clients\Entities\ClientHub;
use Modules\B2B\Entities\B2BRecoveryRequest; //updated by logesh
use Modules\LeaveManagement\Entities\LeaveType;
use Modules\LeaveManagement\Entities\LeaveRequest;
use Modules\Deliveryman\Entities\DeliveryManLogs;
use Modules\HRStatus\Entities\HRleveltwoDeliverymanAssignment; //updated by Mugesh.B
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\BusinessSetting;
use Modules\RiderType\Entities\RiderType;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Deliveryman extends Authenticatable
{
    use HasFactory;

    protected $table = 'ev_tbl_delivery_men';

    protected $fillable = [
        'first_name', 'last_name', 'mobile_number', 'current_city_id', 'interested_city_id',
        'vehicle_type', 'lead_source_id', 'register_date_time', 'rider_status','rider_status_update_at', 'remarks',
        'apply_job_source', 'referral', 'referal_person_name', 'referal_person_number', 'job_agency',
        'photo', 'aadhar_card_front', 'aadhar_card_back', 'aadhar_number', 'pan_card_front',
        'pan_card_back', 'pan_number', 'driving_license_front', 'driving_license_back', 'bank_passbook',
        'bank_name', 'ifsc_code', 'account_number', 'account_holder_name', 'date_of_birth', 
        'present_address', 'permanent_address', 'father_name', 'father_mobile_number', 'mother_name',
        'mother_mobile_number', 'spouse_name', 'spouse_mobile_number', 'emergency_contact_person_1_name',
        'emergency_contact_person_1_mobile', 'emergency_contact_person_2_name', 'emergency_contact_person_2_mobile',
        'blood_group', 'remember_token', 'fcm_token', 'approved_status', 'approver_role',
        'approver_id', 'marital_status', 'rider_type','license_number','aadhar_verify','pan_verify','lisence_verify','bank_verify','vechile_id','aadhar_verify_date','pan_verify_date','lisence_verify_date','bank_verify_date','who_verify','who_verify_id','deny_remarks','client_id','hub_id','Chassis_Serial_No','ad_client_hub_created_by','ad_client_hub_approve_by','ad_client_hub_approve','ad_client_hub_approve_name', 'ad_client_hub_assign_at','ad_client_hub_deny_reason','ad_client_hub_status_at','work_type','emp_id_status','emp_id','work_status','active_date','adhoc_parmenant_date','delete_status','emp_prev_company_id','emp_prev_experience','social_links','bank_statements','reg_application_id'
            ,'reference_name','reference_mobile_number','reference_relationship','bgv_approve_id','who_aadhar_verify_id','who_pan_verify_id','who_license_verify_id','who_bank_verify_id',
            'bgv_approve_datetime','as_approve_datetime','probation_from_date','probation_to_date','job_status','job_status_resigned_remarks','job_status_resigned_at','job_status_resigned_by' ,'llr_verify_date' ,'who_llr_verify_id' ,'llr_verify'
        
    ];
    
    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'current_city_id' => 'integer',
        'interested_city_id' => 'integer',
        'lead_source_id' => 'integer',
        'register_date_time' => 'datetime',
        // 'date_of_birth' => 'date',
        'date_of_birth' => 'date:Y-m-d',
        'father_mobile_number' => 'string',
        'mother_mobile_number' => 'string',
        'spouse_mobile_number' => 'string',
        'emergency_contact_person_1_mobile' => 'string',
        'emergency_contact_person_2_mobile' => 'string',
        'approved_status' => 'string',
    ];

    //  public function actionBtn($tableId = 'delivery-man-table'): string
    // {
    //   return '
                
    //             <a href="' . route('admin.Green-Drive-Ev.delivery-man.preview', $this->id) . '" class="btn btn-warning-soft btn-sm me-1">
    //                 <i class="fas fa-eye"></i>
    //             </a>
    //             <a onclick="route_alert_with_input(\'' . route('admin.Green-Drive-Ev.delivery-man.whatsapp-message') . '\', \'' . $this->mobile_number . '\')" class="btn btn-success btn-sm me-1">
    //                 <i class="fab fa-whatsapp"></i>
    //             </a>
    //             <a href="' . route('admin.Green-Drive-Ev.delivery-man.edit', $this->id) . '" class="btn btn-success-soft btn-sm me-1">
    //                 <i class="fas fa-pen-to-square"></i>
    //             </a>
    //             <button onclick="route_alert(\'' . route('admin.Green-Drive-Ev.delivery-man.delete', $this->id) . '\', \'Delete this Deliveryman\')" class="btn btn-danger-soft btn-sm">
    //                 <i class="fas fa-trash"></i>
    //             </button>
    //             <a href="' . route('admin.Green-Drive-Ev.delivery-man.zone-asset', $this->id) . '" class="btn btn-primary-soft btn-sm me-1">
    //                 <i class="fas fa-bicycle"></i>
    //             </a>';
    // }
    
        public function openedRequest() //updated by logesh
    {
        return $this->hasMany(B2BRecoveryRequest::class, 'recovery_agent_id') ->where('agent_status', 'opened');
    }
    
    public function closedRequest() //updated by logesh
    {
        return $this->hasMany(B2BRecoveryRequest::class, 'recovery_agent_id') ->where('agent_status', 'closed');
    }
    
    public function actionBtn($tableId = 'delivery-man-table'): string
    {
      if ($this->delete_status == 1) {
            $icon = '<i class="fas fa-undo"></i>';
            $btnClass = 'btn-dark-soft btn-outline-dark';
            $btnText = 'Restore';
        } else {
           
            
            $icon = '<i class="fas fa-trash"></i>';
            $btnClass = 'btn-danger-soft';
            $btnText = 'Delete';
        }
        
       return '
                <div class="d-flex">
                    <a href="' . route('admin.Green-Drive-Ev.delivery-man.preview', $this->id) . '" class="btn btn-warning-soft btn-sm me-1">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a onclick="route_alert_with_input(\'' . route('admin.Green-Drive-Ev.delivery-man.whatsapp-message') . '\', \'' . $this->mobile_number . '\')" class="btn btn-success btn-sm me-1">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="' . route('admin.Green-Drive-Ev.delivery-man.edit', $this->id) . '" class="btn btn-success-soft btn-sm me-1">
                        <i class="fas fa-pen-to-square"></i>
                    </a>
                    <button onclick="route_alert(\'' . route('admin.Green-Drive-Ev.delivery-man.delete', $this->id) . '\', \'' . $btnText . ' this Deliveryman\')" class="btn ' . $btnClass . ' btn-sm me-1" title="' . $btnText . '">' . $icon . '
                    </button>
                    <a href="' . route('admin.Green-Drive-Ev.delivery-man.zone-asset', $this->id) . '" class="btn btn-primary-soft btn-sm me-1">
                        <i class="fas fa-bicycle"></i>
                    </a>
                </div>';
    }
    
    // public function actionBtn_new($tableId = 'employess-list-table'): string
    // {
    //   return '
                
    //             <a href="' . route('admin.Green-Drive-Ev.delivery-man.preview', $this->id) . '" class="btn btn-warning-soft btn-sm me-1">
    //                 <i class="fas fa-eye"></i>
    //             </a>
    //             <a onclick="route_alert_with_input(\'' . route('admin.Green-Drive-Ev.delivery-man.whatsapp-message') . '\', \'' . $this->mobile_number . '\')" class="btn btn-success btn-sm me-1">
    //                 <i class="fab fa-whatsapp"></i>
    //             </a>
    //             <a href="' . route('admin.Green-Drive-Ev.delivery-man.edit', $this->id) . '" class="btn btn-success-soft btn-sm me-1">
    //                 <i class="fas fa-pen-to-square"></i>
    //             </a>
    //             <button onclick="route_alert(\'' . route('admin.Green-Drive-Ev.delivery-man.delete', $this->id) . '\', \'Delete this Employee\')" class="btn btn-danger-soft btn-sm">
    //                 <i class="fas fa-trash"></i>
    //             </button>
    //             <a href="' . route('admin.Green-Drive-Ev.delivery-man.zone-asset', $this->id) . '" class="btn btn-primary-soft btn-sm me-1">
    //                 <i class="fas fa-bicycle"></i>
    //             </a>';
    // }
    
        public function actionBtn_new($tableId = 'employess-list-table'): string
        {
            if ($this->delete_status == 1) {
                $icon = '<i class="fas fa-undo"></i>';
                $btnClass = 'btn-dark-soft btn-outline-dark';
                $btnText = 'Restore';
            } else {
               
                
                $icon = '<i class="fas fa-trash"></i>';
                $btnClass = 'btn-danger-soft';
                $btnText = 'Delete';
            }
        
        return '
            <div class="d-flex">
                <a href="' . route('admin.Green-Drive-Ev.delivery-man.preview', $this->id) . '" class="btn btn-warning-soft btn-sm me-1">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="javascript:void(0);" onclick="route_alert_with_input(\'' . route('admin.Green-Drive-Ev.delivery-man.whatsapp-message') . '\', \'' . $this->mobile_number . '\')" class="btn btn-success btn-sm me-1">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="' . route('admin.Green-Drive-Ev.delivery-man.edit', $this->id) . '" class="btn btn-success-soft btn-sm me-1">
                    <i class="fas fa-pen-to-square"></i>
                </a>
                <button onclick="route_alert(\'' . route('admin.Green-Drive-Ev.delivery-man.delete', $this->id) . '\', \'' . $btnText . ' this Employee\')" class="btn ' . $btnClass . ' btn-sm me-1" title="' . $btnText . '">' . $icon . '
                </button>
            </div>';
        }


    public function vehicle_type()
{
    return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
}

    public function approveBtn($tableId = 'delivery-man-table'): string
    {
        if($this->approved_status == 1 ){
            return '<p>Approved</p>';
        }else if($this->approved_status == 2 ){
            return '<p>Denied</p>';
        }else{
            return '
            <button onclick="route_alert_approve(\'' . route('admin.Green-Drive-Ev.delivery-man.approve', ['id' => $this->id]) . '\', \'Approve this Deliveryman\')" class="btn btn-primary btn-sm">
                <svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M173.9 439.4l-166.4-166.4c-12.5-12.5-12.5-32.8 0-45.3l45.3-45.3c12.5-12.5 32.8-12.5 45.3 0L192 312.6l279.4-279.4c12.5-12.5 32.8-12.5 45.3 0l45.3 45.3c12.5 12.5 12.5 32.8 0 45.3L218.3 439.4c-12.5 12.5-32.8 12.5-45.3 0z"></path></svg>
            </button>
            <button onclick="route_deny(\'' . route('admin.Green-Drive-Ev.delivery-man.deny', ['id' => $this->id]) . '\', \'Deny this Deliveryman\')" class="btn btn-warning btn-sm">
                <svg class="svg-inline--fa fa-times" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><path fill="currentColor" d="M242.7 256l100.1-100.1c12.5-12.5 12.5-32.8 0-45.3L297.4 65.9c-12.5-12.5-32.8-12.5-45.3 0L152 166l-100.1-100.1c-12.5-12.5-32.8-12.5-45.3 0L9.3 110.6c-12.5 12.5-12.5 32.8 0 45.3L109.4 256l-100.1 100.1c-12.5 12.5-12.5 32.8 0 45.3L31.7 446.1c12.5 12.5 32.8 12.5 45.3 0L152 346l100.1 100.1c12.5 12.5 32.8 12.5 45.3 0l22.6-22.6c12.5-12.5 12.5-32.8 0-45.3L242.7 256z"></path></svg>
            </button>';
        }
        
    }
    public function approveBtn_new($tableId = 'employess-list-table'): string
    {
        if($this->approved_status == 1 ){
            return '<p>Approved</p>';
        }else if($this->approved_status == 2 ){
            return '<p>Denied</p>';
        }else{
            return '
            <button onclick="route_alert_approve(\'' . route('admin.Green-Drive-Ev.delivery-man.approve', ['id' => $this->id]) . '\', \'Approve this Employee\')" class="btn btn-primary btn-sm">
                <svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M173.9 439.4l-166.4-166.4c-12.5-12.5-12.5-32.8 0-45.3l45.3-45.3c12.5-12.5 32.8-12.5 45.3 0L192 312.6l279.4-279.4c12.5-12.5 32.8-12.5 45.3 0l45.3 45.3c12.5 12.5 12.5 32.8 0 45.3L218.3 439.4c-12.5 12.5-32.8 12.5-45.3 0z"></path></svg>
            </button>
            <button onclick="route_deny(\'' . route('admin.Green-Drive-Ev.delivery-man.deny', ['id' => $this->id]) . '\', \'Deny this Employee\')" class="btn btn-warning btn-sm">
                <svg class="svg-inline--fa fa-times" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><path fill="currentColor" d="M242.7 256l100.1-100.1c12.5-12.5 12.5-32.8 0-45.3L297.4 65.9c-12.5-12.5-32.8-12.5-45.3 0L152 166l-100.1-100.1c-12.5-12.5-32.8-12.5-45.3 0L9.3 110.6c-12.5 12.5-12.5 32.8 0 45.3L109.4 256l-100.1 100.1c-12.5 12.5-12.5 32.8 0 45.3L31.7 446.1c12.5 12.5 32.8 12.5 45.3 0L152 346l100.1 100.1c12.5 12.5 32.8 12.5 45.3 0l22.6-22.6c12.5-12.5 12.5-32.8 0-45.3L242.7 256z"></path></svg>
            </button>';
        }
        
    }
    public function zone()
    {
        return $this->belongsTo(Zones::class, 'zone_id', 'id');
    }
    
    public function current_city()
    {
        return $this->belongsTo(City::class, 'current_city_id');
    }
    
     public function interest_city()
    {
        return $this->belongsTo(Area::class, 'interested_city_id');
    }
    
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
    
     public function RiderType()
    {
        return $this->hasOne(RiderType::class,'id','rider_type');
    }
    
    
    public function get_approved_by()
    {
        return $this->belongsTo(User::class, 'approver_id', 'id');
    }
    
   public function work_status_handler(): string
    {
        if ($this->work_type == 'deliveryman') {
            return '<span class="badge bg-secondary">Deliveryman</span>';
        } else {
            return '<span class="badge bg-warning px-3">In-House</span>';
        }
    }

  public function leave_log_view_btn($tableId = 'leave-log-table'): string
  {
       return '
            <a href="' . route('admin.Green-Drive-Ev.delivery-man.preview', $this->id) . '" class="btn btn-info-soft btn-sm me-1">
                <i class="fas fa-eye"></i>
            </a>';
  }
  
  public function leave_no_of_days($tableId = 'leave-log-table'): string
  {
      $totalLeaveDays = LeaveType::where('leave_type', 'day')->sum('days');
      return $totalLeaveDays.' Days';
  }
  public function take_leave_total($tableId = 'leave-log-table'): string
  {
      $year = date('Y');
      $total_taken = LeaveRequest::whereYear('start_date',$year)->where('dm_id', $this->id)->where('approve_status',1)->sum('apply_days');

      return $total_taken.' Days';
  }
   public function balance_leave_total($tableId = 'leave-log-table'): string
  {
      $year = date('Y');
      $total_taken = LeaveRequest::whereYear('start_date',$year)->where('dm_id', $this->id)->where('approve_status',1)->sum('apply_days');
      $totalLeaveDays = LeaveType::where('leave_type', 'day')->sum('days');
      $balance_leaves = $totalLeaveDays - $total_taken;
      return $balance_leaves.' Days';
  }
    public function leave_total_permission_hr($tableId = 'leave-log-table'): string
  {
      $year = date('Y');
      $totalpermission_hr = LeaveRequest::whereYear('permission_date',$year)->where('dm_id', $this->id)->where('approve_status',1)->sum('permission_hr');

      return $totalpermission_hr.' Hours';
  }
  
          public function hrleveltwo_assign()
    {
        return $this->hasOne(HRleveltwoDeliverymanAssignment::class, 'dm_id','id');
    }
  
   public function actionBtn_visible_supervisor($tableId = 'supervisor-list-table'): string
    {
       if ($this->delete_status == 1) {
            $icon = '<i class="fas fa-undo"></i>';
            $btnClass = 'btn-dark-soft btn-outline-dark';
            $btnText = 'Restore';
        } else {
           
            
            $icon = '<i class="fas fa-trash"></i>';
            $btnClass = 'btn-danger-soft';
            $btnText = 'Delete';
        }
    
       return '
                <div class="d-flex">
                    <a href="' . route('admin.Green-Drive-Ev.delivery-man.preview', $this->id) . '" class="btn btn-warning-soft btn-sm me-1">
                        <i class="fas fa-eye"></i>
                    </a>
                      <a href="' . route('admin.Green-Drive-Ev.adhocmanagement.edit_adhoc', $this->id) . '" class="btn btn-success-soft btn-sm me-1">
                        <i class="fas fa-pen-to-square"></i>
                    </a>
                    <a onclick="route_alert_with_input(\'' . route('admin.Green-Drive-Ev.delivery-man.whatsapp-message') . '\', \'' . $this->mobile_number . '\')" class="btn btn-success btn-sm me-1">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="' . route('admin.Green-Drive-Ev.adhocmanagement.sp_asset_assign', $this->id) . '" class="btn btn-primary-soft btn-sm me-1">
                        <i class="fas fa-bicycle"></i>
                    </a>
                    <button onclick="route_alert(\'' . route('admin.Green-Drive-Ev.delivery-man.delete', $this->id) . '\', \'' . $btnText . ' this Adhoc\')" class="btn ' . $btnClass . ' btn-sm me-1" title="' . $btnText . '">' . $icon . '
                    </button>
                </div>';
    }
   
   public function actionBtn_visible_supervisor_approve($tableId = 'supervisor-list-table'): string
    {
      if($this->approved_status == 1 ){
            return '<p>Approved</p>';
        }else if($this->approved_status == 2 ){
            return '<p>Denied</p>';
        }else{
            return '
            <button onclick="ApproveOrRejectStatus(\'' . route('admin.Green-Drive-Ev.adhocmanagement.approve_status') . '\', ' . $this->id . ', \'Approve this Adhoc\', 1)" class="btn btn-primary btn-sm">
                <svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M173.9 439.4l-166.4-166.4c-12.5-12.5-12.5-32.8 0-45.3l45.3-45.3c12.5-12.5 32.8-12.5 45.3 0L192 312.6l279.4-279.4c12.5-12.5 32.8-12.5 45.3 0l45.3 45.3c12.5 12.5 12.5 32.8 0 45.3L218.3 439.4c-12.5 12.5-32.8 12.5-45.3 0z"></path></svg>
            </button>
            <button onclick="ApproveOrRejectStatus(\'' . route('admin.Green-Drive-Ev.adhocmanagement.adhoc_deny_status') . '\', ' . $this->id . ', \'Deny this Adhoc\', 0)" class="btn btn-warning btn-sm">
                <svg class="svg-inline--fa fa-times" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><path fill="currentColor" d="M242.7 256l100.1-100.1c12.5-12.5 12.5-32.8 0-45.3L297.4 65.9c-12.5-12.5-32.8-12.5-45.3 0L152 166l-100.1-100.1c-12.5-12.5-32.8-12.5-45.3 0L9.3 110.6c-12.5 12.5-12.5 32.8 0 45.3L109.4 256l-100.1 100.1c-12.5 12.5-12.5 32.8 0 45.3L31.7 446.1c12.5 12.5 32.8 12.5 45.3 0L152 346l100.1 100.1c12.5 12.5 32.8 12.5 45.3 0l22.6-22.6c12.5-12.5 12.5-32.8 0-45.3L242.7 256z"></path></svg>
            </button>';
        }
    }
    
    public function get_last_login_date($tableId = 'delivery-man-table'): string
    {
        $lastPunchIn = DB::table('ev_delivery_man_logs')->where('user_id', $this->id)
            ->orderBy('punched_in', 'desc')->first();
        
        if (!$lastPunchIn) {
            return '<span class="badge bg-secondary">No Login</span>'; 
        }
        $city = ''; 
    
        if (!empty($lastPunchIn->punchin_latitude) && !empty($lastPunchIn->punchin_longitude)) {
            $city = $this->get_punchin_city($lastPunchIn->punchin_latitude, $lastPunchIn->punchin_longitude);
        }
    
        $lastPunchInFormatted = \Carbon\Carbon::parse($lastPunchIn->punched_in)->format('d-m-Y H:i:s');
        $daysSinceLastPunch = now()->diffInDays(\Carbon\Carbon::parse($lastPunchIn->punched_in));
    
        if ($daysSinceLastPunch >= 3) {
            return '<span class="badge bg-danger">' . $lastPunchInFormatted . '</span> <br><span style="font-size: 10px;">' . $city . '</span>';
        } else {
            return '<span class="badge bg-success">' . $lastPunchInFormatted . '</span><br> <span style="font-size: 10px;">' . $city . '</span>';
        }
    }

    
    public function get_last_punched_out_date($tableId = 'delivery-man-table'): string
    {
        $lastPunchOut = DB::table('ev_delivery_man_logs')->where('user_id', $this->id)
            ->orderBy('punched_out', 'desc')->first();
        
        if (!$lastPunchOut || empty($lastPunchOut->punched_out)) {
            return '<span class="badge bg-secondary">No Logout</span>'; 
        }
        $city = ''; 
    
        if (!empty($lastPunchOut->punchout_latitude) && !empty($lastPunchOut->punchedout_longitude)) {
            $city = $this->get_punchin_city($lastPunchOut->punchout_latitude, $lastPunchOut->punchedout_longitude);
        }
    
        $lastPunchOutFormatted = \Carbon\Carbon::parse($lastPunchOut->punched_out)->format('d-m-Y H:i:s');
        $daysSinceLastPunch = now()->diffInDays(\Carbon\Carbon::parse($lastPunchOut->punched_out));
    
        if ($daysSinceLastPunch >= 3) {
            return '<span class="badge bg-danger">' . $lastPunchOutFormatted . '</span> <br><span style="font-size: 10px;">' . $city . '</span>';
        } else {
            return '<span class="badge bg-success">' . $lastPunchOutFormatted . '</span><br> <span style="font-size: 10px;">' . $city . '</span>';
        }
    }
    
     public function get_job_status(): string
    {
    
        if ($this->job_status == 'active') {
            return '<span class="badge bg-success">Active</span>';
        } else if ($this->job_status == 'resigned') {
            return '<span class="badge bg-danger">Resigned</span>';
        }else{
            return '<span class="badge bg-warning">N/A</span>';
        }
    }
    public function get_select_job_status(): string
    {
        $selectedActive = $this->job_status === 'active' ? 'selected' : '';
        $selectedResigned = $this->job_status === 'resigned' ? 'selected' : '';
    
        return '
            <select class="form-select" id="jobStatus_' . $this->id . '" name="job_status"
                onchange="ChangeJobStatus(' . $this->id . ', this.value, this, \'' . $this->job_status . '\')">
                <option value="active" ' . $selectedActive . '>Active</option>
                <option value="resigned" ' . $selectedResigned . '>Resigned</option>
            </select>
        ';
    }


  

    
   public function get_active_date($tableId = 'delivery-man-table'): string
    {
        $get_active_date = DB::table('ev_tbl_delivery_men')->where('id', $this->id)->first();
    
        if (!$get_active_date->active_date) {
            return '<span class="text-center" style="text-align:center;"> - </span>'; 
        }
    
        $FormattedDate = \Carbon\Carbon::parse($get_active_date->active_date);
        $Formatted = $FormattedDate->format('d-m-Y h:i:s A'); // Convert to 12-hour format with AM/PM
    
        // if ($FormattedDate->isPast()) { 
        //     return '<span class="badge bg-danger">' . $Formatted . '</span>';
        // } else {
        //     return '<span class="badge bg-success">' . $Formatted . '</span>';
        // }
        return '<span class="badge fw-medium" style="color:black;">' . $Formatted . '</span>';
    }

    
     public function get_last_login_date_for_all($dm_id)
    {
        $lastPunchIn = DB::table('ev_delivery_man_logs')
            ->where('user_id', $dm_id)
            ->orderBy('punched_in', 'desc')
            ->first();
    
        if (!$lastPunchIn) {
            return 'No Login';
        }
    
        return \Carbon\Carbon::parse($lastPunchIn->punched_in)->format('d-m-Y H:i:s');
    }
    
    public function get_punchin_city($lat, $long)
    {
        $api_key = BusinessSetting::where('key_name', 'google_map_api_key')->value('value');
    
        $geocodeResponse = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'latlng' => "{$lat},{$long}",
            'key' => $api_key,
        ]);
    
        $geocodeData = $geocodeResponse->json();
    
        if (!isset($geocodeData['status']) || $geocodeData['status'] !== 'OK' || empty($geocodeData['results'])) {
            return 'Unknown Address';
        }
    
        $components = $geocodeData['results'][0]['address_components'];
        $street_number = '';
        $route = '';
        $neighborhood = '';
        $city = '';
        $state = '';
        $postal_code = '';
        foreach ($components as $component) {
            if (in_array('street_number', $component['types'])) {
                $street_number = $component['long_name'];
            }
            if (in_array('route', $component['types'])) {
                $route = $component['long_name'];
            }
            if (in_array('sublocality_level_1', $component['types']) || in_array('neighborhood', $component['types'])) {
                $neighborhood = $component['long_name'];
            }
            if (in_array('locality', $component['types'])) {
                $city = $component['long_name'];
            }
            if (in_array('administrative_area_level_1', $component['types'])) {
                $state = $component['long_name'];
            }
            if (in_array('postal_code', $component['types'])) {
                $postal_code = $component['long_name'];
            }
        }
        // $formatted_address = trim("{$neighborhood}, {$street_number}, {$route}, {$city}, {$state}", ', ');
        $formatted_address = trim("{$neighborhood}, {$street_number}, {$route}, {$city}, {$state}", ', ');
    
        return $formatted_address ?: '';
    }

    function get_client_hub(){
        $hub = ClientHub::where('id',$this->hub_id)->where('client_id',$this->client_id)->first();
        $hub_name = '';
        if($hub){
            $hub_name = $hub->hub_name;
        }else{
            $hub_name = 'N/A';
        }
        return $hub_name;
    }
    
    
   





}
