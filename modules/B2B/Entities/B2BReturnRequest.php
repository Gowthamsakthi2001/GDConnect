<?php

namespace Modules\B2B\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\B2B\Entities\B2BRider;
use Modules\B2B\Entities\B2BAgent;
class B2BReturnRequest extends Model

{
    use HasFactory;
    
    protected $table = 'b2b_tbl_return_request';
    public $timestamps = true;

    protected $fillable = [
       'return_reason',
        'rider_id',
        'assign_id',
        'chassis_number',
        'register_number',
        'rider_name',
        'client_business_name',
        'rider_mobile_no',
        'contact_no',
        'contact_email',
        'description',
        'status',
        'created_at',
        'updated_at',
        'closed_by',
        'created_by'
    ];
    
    public function rider()
    {
        return $this->belongsTo(B2BRider::class, 'rider_id', 'id');
    }
    
    public function agent()
    {
        return $this->belongsTo(B2BAgent::class, 'closed_by', 'id');
    }
    
    public function assignment()
    {
        return $this->belongsTo(B2BVehicleAssignment::class, 'assign_id', 'id');
    }
    
}