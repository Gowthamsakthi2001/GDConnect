<?php

namespace Modules\MasterManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MasterManagement\Database\factories\AssetMasterVehicleFactory;
use Modules\MasterManagement\Entities\CustomerPOCDetail;
use Modules\MasterManagement\Entities\CustomerOperationalHub;
use Modules\MasterManagement\Entities\BusinessConstitutionType;
use App\Models\EVState;
use Modules\MasterManagement\Entities\CustomerLogin;
use Modules\City\Entities\City;

class CustomerMaster extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_customer_master';

    protected $fillable = [
        'id',
        'name',
        'trade_name',
        'email',
        'phone',
        'customer_type',
        'business_type',
        'business_const_type',
        'accountability_type_id',
        'address',
        'city_id',
        'state_id',
        'gst_no',
        'pan_no',
        'poc_name',
        'poc_email',
        'adhaar_front_img',
        'adhaar_back_img',
        'pan_img',
        'gst_img',
        'business_proof_img',
        'company_logo',
        'profile_img',
        'status',
        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
      'id'=>'string'    
    ];
    
    public function constitution_type()
    {
        return $this->belongsTo(BusinessConstitutionType::class, 'business_const_type');
    }
    
    public function poc_details()
    {
        return $this->hasMany(CustomerPOCDetail::class, 'customer_id');
    }
    
    public function customerlogins()
    {
        return $this->hasMany(CustomerLogin::class, 'customer_id');
    }
    
    public function operationalHubs()
    {
        return $this->hasMany(CustomerOperationalHub::class, 'customer_id');
    }
    
        public function cities()
    {
        return $this->belongsTo(City::class, 'city_id' , 'id');
    }
    
    public function states()
    {
        return $this->belongsTo(EVState::class, 'state_id' , 'id');
    }

}
