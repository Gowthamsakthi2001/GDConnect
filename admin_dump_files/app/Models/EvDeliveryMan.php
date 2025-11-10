<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Deliveryman\Database\factories\DeliverymanFactory;
use Carbon\Carbon;
use Modules\Zones\Entities\Zones;
use Modules\City\Entities\City;
use Modules\City\Entities\Area;
use Modules\Clients\Entities\Client;
use Modules\Clients\Entities\ClientHub;
use Modules\LeaveManagement\Entities\LeaveType;
use Modules\LeaveManagement\Entities\LeaveRequest;
use Modules\Deliveryman\Entities\DeliveryManLogs;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\BusinessSetting;
use Modules\RiderType\Entities\RiderType;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Auth\User as Authenticatable;

class EvDeliveryMan extends Authenticatable
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
            'bgv_approve_datetime','as_approve_datetime','probation_from_date','probation_to_date' ,'llr_number' , 'llr_image'
        
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
  
}

