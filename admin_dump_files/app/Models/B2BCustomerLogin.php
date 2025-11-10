<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MasterManagement\Entities\CustomerMaster;
use Modules\City\Entities\City; //updated by Gowtham.s
use Modules\Zones\Entities\Zones; //updated by Gowtham.s


class B2BCustomerLogin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'ev_tbl_customer_logins';

    protected $fillable = [
        'customer_id',
        'email',
        'password',
        'type',
        'zone_id',
        'status',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
        public function customer_relation()
    {
        return $this->belongsTo(CustomerMaster::class, 'customer_id');
    }
    
     public function zone() //updated by Gowtham.s
    {
        return $this->belongsTo(Zones::class, 'zone_id');
    }
    
    public function city() //updated by Gowtham.s
    {
        return $this->belongsTo(City::class, 'city_id');
    } 
}
