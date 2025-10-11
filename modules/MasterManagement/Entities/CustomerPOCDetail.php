<?php

namespace Modules\MasterManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MasterManagement\Entities\CustomerMaster;
class CustomerPOCDetail extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_customer_poc_details';

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'phone',
        'status',
        'created_at',
        'updated_at',
    ];

    public function customer()
    {
        return $this->belongsTo(CustomerMaster::class, 'customer_id');
    }
}
