<?php

namespace Modules\Employee\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Employee\Database\factories\EmployeeFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_code',
        'name',
        'payroll_type',
        'department_id',
        'position_id',
        'nid',
        'phone',
        'email',
        'email2',
        'phone2',
        'join_date',
        'blood_group',
        'dob',
        'working_slot_from',
        'working_slot_to',
        'father_name',
        'mother_name',
        'present_contact',
        'present_address',
        'permanent_contact',
        'permanent_address',
        'present_city',
        'permanent_city',
        'contact_person_name',
        'contact_person_mobile',
        'reference_name',
        'reference_mobile',
        'reference_email',
        'reference_address',
        'avatar_path',
    ];

    protected static function boot()
    {
        parent::boot();
        static::created(function ($employee) {
            $employee->employee_code = 'EMP'.\str_pad($employee->id, 4, '0', STR_PAD_LEFT);
            $employee->save();
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    protected static function newFactory()
    {
        return EmployeeFactory::new();
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
