<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EvDeliveryMan;
use Carbon\Carbon;
class LoginTimeRecord extends Model
{
    use HasFactory;
    protected $table = 'ev_delivery_man_logs';

    protected $fillable = [
        'user_id',
        'user_type',
        'punched_in',
        'punched_out',
        'status',
        'punchin_latitude',
        'punchin_longitude',  // Corrected from 'punched_in_longtitude' to 'punchin_longitude'
        'punchedout_longitude',  // Corrected from 'punuchout_longtitude' to 'punchedout_longitude'
        'punchout_latitude',
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
        return $this->belongsTo(EvDeliveryMan::class, 'user_id');
    }
}
