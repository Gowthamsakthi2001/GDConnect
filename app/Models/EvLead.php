<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvLead extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_leads';

    // protected $fillable = ['telecaller_status', 'source', 'assigned', 'f_name', 'l_name','phone_number','current_city','intrested_city','vehicle_type','lead_sources','register_date','active_status','task','description'];
    protected $guarded = [];
    protected $casts = [
        'telecaller_status' => 'string',
        'Source' => 'integer',
        'assigned' => 'integer',
        'f_name' => 'string',
        'l_name' => 'string',
        'phone_number' => 'string',
        'current_city' => 'integer',
        'intrested_city' => 'string',
        'vehicle_type' => 'integer',
        'lead_sources' => 'integer',
        'register_date' => 'date',
        'active_status' => 'string',
        'task' => 'string',
        'description' => 'string',
    ];
}
