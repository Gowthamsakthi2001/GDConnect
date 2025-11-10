<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EVState extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_states'; 
    protected $primaryKey = 'id'; 

    public $timestamps = true; 
    protected $fillable = [
        'state_name',
        'state_code',
        'status',
        'created_at',
        'updated_at'
    ];

}
