<?php

namespace Modules\VehicleServiceTicket\Entities;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FieldProxyLog extends Model
{
    use HasFactory;
    
    protected $table = 'field_proxy_logs';


    protected $primaryKey = 'id';

    public $timestamps = true;
    
    protected $fillable = [
        'fp_id',
        'status',
        'current_status',
        'remarks',
        'created_by',
        'type',
        'created_at',
        'updated_at'
    ];
}
