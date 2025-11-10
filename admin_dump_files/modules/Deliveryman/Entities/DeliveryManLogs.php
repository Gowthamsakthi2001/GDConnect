<?php

namespace Modules\Deliveryman\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Deliveryman\Database\factories\DeliveryManLogsFactory;
use Modules\Deliveryman\Entities\Deliveryman;
use Carbon\Carbon;

class DeliveryManLogs extends Model
{
    protected $table = 'ev_delivery_man_logs';

    protected $fillable = [
        'user_id',
        'user_type',
        'punched_in',
        'punched_out',
        'status',
        'client_id'
    ];

    protected $casts = [
        'punched_in' => 'datetime',
        'punched_out' => 'datetime',
        'status'=>'integer'
    ];
   
    // Accessor for punched_in with custom format
    public function getPunchedInAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('UTC')->format('Y-m-d H:i:s');
    }

    // Accessor for punched_out with custom format
    public function getPunchedOutAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('UTC')->format('Y-m-d H:i:s');
    }

    // Accessor for created_at with custom format
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('UTC')->format('Y-m-d H:i:s');
    }

    // Accessor for updated_at with custom format
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('UTC')->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(Deliveryman::class, 'user_id');
    }
}
