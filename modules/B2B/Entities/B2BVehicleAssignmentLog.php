<?php

namespace Modules\B2B\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\B2B\Entities\B2BVehicleAssignment;//updated by Mugesh.B
use Modules\Deliveryman\Entities\Deliveryman; //updated by logesh
use App\Models\User; //updated by logesh
use Modules\MasterManagement\Entities\CustomerLogin;
use Modules\B2B\Entities\B2BRecoveryRequest;

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
        'location_lat',
        'location_lng',
        'updates_id',
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


    public function recovery_request() //updated by logesh
    {
        return $this->belongsTo(B2BRecoveryRequest::class, 'request_type_id');
    }
    
    public function getRecoveryUserAttribute() //updated by logesh
    {
        // Only handle recovery-request type
        if ($this->request_type !== 'recovery_request') {
            return null;
        }
    
        switch ($this->type) {
            case 'recovery-agent':
                return Deliveryman::find($this->action_by);
    
            case 'b2b-web-dashboard':
                return CustomerLogin::find($this->action_by);
            
            case 'b2b-customer':
                return CustomerLogin::find($this->action_by);
                
            case 'recovery-manager-dashboard':
                return User::find($this->action_by);
            
            case 'b2b-admin-dashboard':
                return User::find($this->action_by);
                
            default:
                return null;
        }
    }
    
}