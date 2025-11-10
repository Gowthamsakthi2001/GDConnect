<?php

namespace Modules\HRStatus\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\MasterManagement\Entities\CustomerMaster;
use Modules\AssetMaster\Entities\LocationMaster; //updated by Mugesh.B
use App\Models\User;

class RiderOnboardingList extends Model
{
    protected $table = 'rider_onboarding_lists';

    protected $fillable = [
        'role_type',
        'dm_id',
        'customer_master_id',
        'onboard_date',
        'city_id',
        'hub_id',
        'status',
        'created_by'
    ];

    public $timestamps = true;

    public $incrementing = true;

    public function deliveryman()
    {
        return $this->belongsTo(Deliveryman::class, 'dm_id');
    }

    public function customer()
    {
        return $this->belongsTo(CustomerMaster::class, 'customer_master_id');
    }
    
     public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
         public function location_relation()
    {
        return $this->belongsTo(LocationMaster::class, 'city_id','id');
    }
}
