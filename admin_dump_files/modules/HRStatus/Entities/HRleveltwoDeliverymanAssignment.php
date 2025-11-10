<?php

namespace Modules\HRStatus\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Deliveryman\Entities\Deliveryman;

class HRleveltwoDeliverymanAssignment extends Model
{
    use HasFactory;

    protected $table = 'hrleveltwo_deliveryman_assignments';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'dm_id',
        'assign_at',
        'verified_at',
        'current_status',
        'assigned_dep',
        'assigned_by'
    ];

    protected $casts = [
        'assign_at' => 'datetime',
        'verified_at' => 'datetime',
    ];
    
    public function delivery_man()
    {
        return $this->belongsTo(Deliveryman::class, 'dm_id');
    }
    
    
    
}
