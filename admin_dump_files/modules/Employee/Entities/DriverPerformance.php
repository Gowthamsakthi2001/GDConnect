<?php

namespace Modules\Employee\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Database\factories\DriverPerformanceFactory;

class DriverPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'over_time_status',
        'salary_status',
        'ot_payment',
        'performance_bonus',
        'penalty_amount',
        'penalty_reason',
        'penalty_date',
        'insert_date',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    protected static function newFactory()
    {
        return DriverPerformanceFactory::new();
    }
}
