<?php

namespace Modules\B2B\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Deliveryman\Entities\Deliveryman;

class B2BRecoveryRequest extends Model
{
    use HasFactory;
    
    protected $table = 'b2b_tbl_recovery_request';
    public $timestamps = true;

    protected $fillable = [
        'reason',
        'assign_id',
        'datetime',
        'vehicle_id',
        'vehicle_number',
        'chassis_number',
        'rider_id',
        'rider_name',
        'client_name',
        'rider_mobile_no',
        'contact_no',
        'contact_email',
        'description',
        'images',
        'video',
        'terms_condition',
        'created_by',
        'created_at',
        'created_by_type',
        'status',
        'agent_status',
        'city_manager_id',
        'recovery_agent_id',
        'city_id',
        'zone_id',
        'is_agent_assigned',
        'not_recovered_reason',
        'faq_id',
        'created_by_type',
        'created_by',
        'closed_by',
        'closed_at',
        'created_at',
        'closed_by_type',
        'updated_at'
    ];
    
    public function rider()
    {
        return $this->belongsTo(B2BRider::class, 'rider_id', 'id');
    }
    
    public function recovery_agent() //updated by logesh
    {
        return $this->belongsTo(Deliveryman::class, 'recovery_agent_id', 'id');
    }
    
    public function assignment()
    {
        return $this->belongsTo(B2BVehicleAssignment::class, 'assign_id', 'id');
    }
    
      public function user()
    {
        if ($this->closed_by_type === 'recovery-agent') {
            return $this->belongsTo(Deliveryman::class, 'closed_by', 'id');
        }
        return $this->belongsTo(User::class, 'closed_by', 'id');
    }

    
    public function logs()
    {
        return $this->hasMany(B2BVehicleAssignmentLog::class, 'request_type_id', 'id')->where('request_type','recovery_request');
    }
}