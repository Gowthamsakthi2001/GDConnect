<?php

namespace Modules\B2B\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Modules\MasterManagement\Entities\CustomerLogin;
use Modules\Zones\Entities\Zones;
use Modules\City\Entities\City;

class B2BRider extends Authenticatable
{
    use HasApiTokens, Notifiable;
    
    
    protected $table = 'b2b_tbl_riders';
    protected $guard = 'rider';
    public $timestamps = true;
    
    protected $fillable = [
        'assign_zone_id',//updated by Gowtham.s
        'name',
        'mobile_no',
        'email',
        'dob',
        'adhar_front',
        'adhar_back',
        'adhar_number',
        'pan_front',
        'pan_back',
        'pan_number',
        'driving_license_front',
        'driving_license_back',
        'driving_license_number',
        'dl_expiry_date',
        'llr_image',
        'llr_number',
        'terms_condition',
        'profile_image',
        'created_by',
        'verified',
        'status' ,
        'verified_by',
        'createdby_city',//updated by Gowtham.s
        'fcm_token',
        'created_at',
        'updated_at'
    ];
    

    protected $hidden = ['remember_token'];
    
    
    public function getAuthIdentifierName()
    {
        return 'id';
    }
    
    public function vehicleRequest(){
        return $this->hasMany(B2BVehicleRequests::class, 'rider_id');
    }
    
    
    public function latestVehicleRequest()
    {
        return $this->hasOne(B2BVehicleRequests::class, 'rider_id')->latestOfMany('id');
    }

    public function customerLogin()
    {
        return $this->belongsTo(CustomerLogin::class, 'created_by', 'id');
    }
    
    public function city()
    {
        return $this->belongsTo(City::class, 'createdby_city', 'id');
    }
    
    public function zone()
    {
        return $this->belongsTo(Zones::class, 'assign_zone_id', 'id');
    }


}
