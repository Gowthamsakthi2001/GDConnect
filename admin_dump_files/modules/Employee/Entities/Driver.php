<?php

namespace Modules\Employee\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Database\factories\DriverFactory;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'driver_code',
        'phone',
        'license_type_id',
        'license_num',
        'license_issue_date',
        'nid',
        'license_expiry_date',
        'authorization_code',
        'dob',
        'joining_date',
        'working_time_slot',
        'leave_status',
        'present_address',
        'permanent_address',
        'avatar_path',
        'is_active',
    ];

    protected static function newFactory()
    {
        return DriverFactory::new();
    }

    protected static function boot()
    {
        parent::boot();
        static::created(function ($driver) {
            $driver->driver_code = 'DRI'.\str_pad($driver->id, 4, '0', STR_PAD_LEFT);
            $driver->save();
        });
    }

    /**
     * Get the file url attribute.
     *
     * @return ?string
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar_path ? storage_asset($this->avatar_path) : null;
    }
}
