<?php

namespace Modules\MasterManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MasterManagement\Entities\CustomerMaster;
class CustomerOperationalHub extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_customer_operational_hubs';

    protected $fillable = [
        'customer_id',
        'hub_name',
        'status',
        'created_at',
        'updated_at',
    ];

    public function customer()
    {
        return $this->belongsTo(CustomerMaster::class, 'customer_id');
    }
}
