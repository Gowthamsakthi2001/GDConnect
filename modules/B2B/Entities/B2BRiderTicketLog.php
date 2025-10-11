<?php

namespace Modules\B2B\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class B2BRiderTicketLog extends Model

{
    use HasFactory;
    
    protected $table = 'b2b_tbl_rider_ticket_logs';
    public $timestamps = true;
    protected $fillable = [
        'ticket_id',
        'status',
        'remarks',
        'action_by',
        'type',
    ];
    
}