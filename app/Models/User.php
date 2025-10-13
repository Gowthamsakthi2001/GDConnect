<?php

namespace App\Models;

use App\Traits\ActionBtn;
use App\Traits\FormatTimestamps;
use App\Traits\WithCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Auth\Traits\HasProfilePhoto;
use Modules\City\Entities\City;
use Modules\Role\Entities\Role;
use Modules\Zones\Entities\Zones; //updated by Gowtham.s 
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use ActionBtn;
    use FormatTimestamps;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasRoles;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use WithCache;

    protected static $cacheKey = '_users_';

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
        'mb_fcm_token', //updated by Gowtham.s
        'login_type',//updated by Gowtham.s 
        'zone_id', //updated by Gowtham.s 
        'password_changed_at' //updated by Gowtham.s
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
     protected $casts = [ //updated by Gowtham.s
        'email_verified_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'password_checked_at' => 'datetime', // 
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Status list.
     */
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
    
    public function getZone()
    {
        return $this->belongsTo(Zones::class, 'zone_id');
    }
    
    public function get_role()
    {
        return $this->belongsTo(Role::class, 'role','id');
    }
}
