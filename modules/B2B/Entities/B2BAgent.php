<?php

namespace Modules\B2B\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Modules\Zones\Entities\Zones;
use Modules\City\Entities\City;
use Modules\Role\Entities\Role;


class B2BAgent extends Authenticatable
{
   
    use HasApiTokens;
    use Notifiable;


    protected $table='users';
    protected $guard = 'agent';
    
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'emp_id',
        'email',
        'password',
        'profile_photo_path',
        'phone',
        'gender',
        'age',
        'address',
        'status',
        'delete_status',
        'role',
        'city_id',
        'zone_id',
        'mb_fcm_token'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */

    /**
     * Status list.
     */
     
    public function getAuthIdentifierName()
    {
        return 'id';
    }
    
    public static function statusList(): array
    {
        return [
            'Pending' => 'Pending',
            'Active' => 'Active',
            'Suspended' => 'Suspended',
        ];
    }

    /**
     * Gender List.
     */
    public static function genderList(): array
    {
        return [
            'Male' => 'Male',
            'Female' => 'Female',
            'Others' => 'Others',
        ];
    }
    
    
     public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    
    public function zone()
    {
        return $this->belongsTo(Zones::class, 'zone_id');
    }
    
    public function deploymentRequests()
    {
        return $this->hasMany(B2BVehicleRequests::class, 'closed_by');
    }
    
    public function returnRequests()
    {
        return $this->hasMany(B2BReturnRequest::class, 'closed_by');
    }
    
    public function get_role()
    {
        return $this->belongsTo(Role::class, 'role','id');
    }
}
