<?php

namespace Modules\VehicleManagement\Entities;

use App\Traits\FormatTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Entities\Employee;

class PickupAndDrop extends Model
{
    use FormatTimestamps;
    use HasFactory;

    protected $fillable = [
        'route_id',
        'start_point',
        'end_point',
        'employee_id',
        'request_type',
        'type',
        'effective_date',
        'status',
        'is_approved',
    ];

    public function route()
    {
        return $this->belongsTo(VehicleRouteDetail::class, 'route_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
