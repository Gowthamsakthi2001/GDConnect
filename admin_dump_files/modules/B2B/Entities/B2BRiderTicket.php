<?php

namespace Modules\B2B\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class B2BRiderTicket extends Model

{
    use HasFactory;
    
    protected $table = 'b2b_tbl_rider_tickets';
public $timestamps = true;
    protected $fillable = [
        'ticket_id',
        'rider_id',
        'subject',
        'category_id',
        'subcategory_id',
        'description',
        'status',
    ];
    
        public function category()
    {
        return $this->belongsTo(B2BTicketCategory::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(B2BTicketCategory::class, 'subcategory_id');
    }
}