<?php

namespace Modules\B2B\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\B2B\Entities\B2BVehicleAssignment;//updated by Mugesh.B
class B2BVehicleAssignmentLog extends Model

{
    use HasFactory;
    
    protected $table = 'b2b_tbl_vehicle_assignment_logs';
    public $timestamps = true;
    protected $fillable = [
       'assignment_id',
        'status',
        'current_status',
        'remarks',
        'action_by',
        'type',
        'request_type',
        'request_type_id',
        'created_at',
        'updated_at',
    ];
    
    public function accidentRequest()
    {
        return $this->belongsTo(AccidentRequest::class, 'request_type_id')
            ->where('request_type', 'accident');
    }

    
    public function assignment()
    {
        return $this->belongsTo(B2BVehicleAssignment::class, 'assignment_id');
    }

    
}