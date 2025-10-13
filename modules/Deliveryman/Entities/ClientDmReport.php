<?php

namespace Modules\Deliveryman\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientDmReport extends Model
{
    use HasFactory;

    protected $table = 'ev_client_based_dm_working_reports';

    public $timestamps = true;

    protected $fillable = [
        'client_id',
        'driver_id',
        'chass_serial_no',
        'start_time',
        'end_time',
        'latitude',
        'longitude',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'driver_id' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];
}
