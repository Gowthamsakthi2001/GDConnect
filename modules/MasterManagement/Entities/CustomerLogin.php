<?php

namespace Modules\MasterManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MasterManagement\Entities\CustomerMaster;
use Modules\Zones\Entities\Zones;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Modules\City\Entities\City;


class CustomerLogin extends Authenticatable implements CanResetPassword
{
    use HasFactory;
    use CanResetPasswordTrait;

    protected $table = 'ev_tbl_customer_logins';

    protected $fillable = [
        'customer_id',
        'email',
        'password',
        'type',
        'city_id', //updated by gowtham.s
        'zone_id',
        'status',
        'created_by',
        'created_at',
        'updated_at',
    ];

    public function customer_relation()
    {
        return $this->belongsTo(CustomerMaster::class, 'customer_id');
    }
    
    
    public function zone()
    {
        return $this->belongsTo(Zones::class, 'zone_id');
    }
    
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    } 
    
    
}
