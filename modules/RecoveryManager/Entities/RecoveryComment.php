<?php

namespace Modules\RecoveryManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\MasterManagement\Entities\CustomerLogin;
use Modules\B2B\Entities\B2BRecoveryRequest;

class RecoveryComment extends Model

{
    use HasFactory;
    
    protected $table = 'b2b_recovery_remarks';
    public $timestamps = true;
    protected $fillable = [
       'req_id',
        'status',
        'comments',
        'user_id',
        'user_type',
        'location_lat',
        'location_lng',
        'updates_id',
        'created_at',
        'updated_at',
    ];
    
    protected $appends = ['recovery_user'];

    public function user()
    {
        if ($this->user_type === 'recovery-agent') {
            return $this->belongsTo(Deliveryman::class, 'user_id', 'id');
        }
    
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    public function getRecoveryUserAttribute() //updated by logesh
    {
    
        switch ($this->user_type) {
            case 'recovery-agent':
                return Deliveryman::find($this->user_id);
    
            case 'b2b-web-dashboard':
                return CustomerLogin::find($this->user_id);
            
            case 'b2b-customer':
                return CustomerLogin::find($this->user_id);
                
            case 'recovery-manager-dashboard':
                return User::find($this->user_id);
                
            case 'b2b-admin-dashboard':
                return User::find($this->user_id);
                
            default:
                return null;
        }
    }
    
    
}