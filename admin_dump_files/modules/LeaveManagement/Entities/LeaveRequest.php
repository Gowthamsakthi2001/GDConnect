<?php

namespace Modules\LeaveManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\LeaveManagement\Database\factories\ClientFactory;
use Modules\Zones\Entities\Zones;
use Carbon\Carbon;
use Modules\LeaveManagement\Entities\LeaveType;
use Modules\Deliveryman\Entities\Deliveryman;
class LeaveRequest extends Model
{
    use HasFactory;
    protected $table = 'ev_leave_requests';
    protected $fillable = [
        'dm_id',
        'leave_id',
        'approve_status',
        'reject_status',
        'rejection_reason',
        'start_date',
        'end_date',
        'remarks',
        'apply_days',
        'permission_date',
        'start_time',
        'end_time',
        'permission_hr',
        'created_at',
        'updated_at',
        'req_status'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'rejection_reason'=>'string'
    ];
    
    
    public function leave(){
        return $this->belongsTo(LeaveType::class, 'leave_id');
    }
    
    public function deliveryman(){
        return $this->belongsTo(Deliveryman::class, 'dm_id');
    }

      public function actionBtn($tableId = 'leave-request-table')
        {
            return '
                <button onclick="ApproveOrRejectStatus(\'' . route('admin.Green-Drive-Ev.leavemanagement.leave_approve_or_reject') . '\', ' . $this->id . ', \'Approve this Leave\', 1)" class="btn btn-primary btn-sm">
                    <svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M173.9 439.4l-166.4-166.4c-12.5-12.5-12.5-32.8 0-45.3l45.3-45.3c12.5-12.5 32.8-12.5 45.3 0L192 312.6l279.4-279.4c12.5-12.5 32.8-12.5 45.3 0l45.3 45.3c12.5 12.5 12.5 32.8 0 45.3L218.3 439.4c-12.5 12.5-32.8 12.5-45.3 0z"></path></svg>
                </button>
                <button onclick="ApproveOrRejectStatus(\'' . route('admin.Green-Drive-Ev.leavemanagement.leave_approve_or_reject') . '\', ' . $this->id . ', \'Reject this Leave\', 0)" class="btn btn-danger btn-sm">
                    <svg class="svg-inline--fa fa-times" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><path fill="currentColor" d="M242.7 256l100.1-100.1c12.5-12.5 12.5-32.8 0-45.3L297.4 65.9c-12.5-12.5-32.8-12.5-45.3 0L152 166l-100.1-100.1c-12.5-12.5-32.8-12.5-45.3 0L9.3 110.6c-12.5 12.5-12.5 32.8 0 45.3L109.4 256l-100.1 100.1c-12.5 12.5-12.5 32.8 0 45.3L31.7 446.1c12.5 12.5 32.8 12.5 45.3 0L152 346l100.1 100.1c12.5 12.5 32.8 12.5 45.3 0l22.6-22.6c12.5-12.5 12.5-32.8 0-45.3L242.7 256z"></path></svg>
                </button>';
        }
        
      public function statusHandler()
        {
            if ($this->approve_status == 1) {
                return '<span class="btn btn-success btn-sm">Approved</span>';
            } else {
                return '<span class="btn btn-danger btn-sm">Rejected</span>';
            }
        }

}