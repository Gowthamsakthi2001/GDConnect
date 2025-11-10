<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvCity extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_city'; // Specify the table name if it doesn't follow Laravel's conventions

    // Specify the attributes that are mass assignable
    protected $fillable = [
        'city_name',
        'status',
    ];

    // Cast attributes to desired data types
    protected $casts = [
        'status' => 'boolean', // Cast status to boolean (1 for active, 0 for inactive)
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
