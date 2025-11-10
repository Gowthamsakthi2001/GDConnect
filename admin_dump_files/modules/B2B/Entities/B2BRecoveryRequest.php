<?php

namespace Modules\B2B\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


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
        'chassis_number',
        'rider_id',
        'rider_name',
        'client_name',
        'contact_person_name',
        'contact_no',
        'contact_email',
        'description',
        'accident_photos',
        'terms_condition',
        'created_by',
        'created_at',
        'status',
        'updated_at'
    ];
    
    public function rider()
    {
        return $this->belongsTo(B2BRider::class, 'rider_id', 'id');
    }
    
    public function assignment()
    {
        return $this->belongsTo(B2BVehicleAssignment::class, 'assign_id', 'id');
    }
    
}