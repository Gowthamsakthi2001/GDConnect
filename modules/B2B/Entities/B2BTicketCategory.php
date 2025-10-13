<?php

namespace Modules\B2B\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class B2BTicketCategory extends Model

{
    use HasFactory;
    
    protected $table = 'b2b_tbl_ticket_categories';
public $timestamps = true;
    protected $fillable = [
        'name',
        'parent_id',
        'status'
    ];
    
}