<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvRiderType extends Model
{
    use HasFactory;

    // Specify the table name if it doesn't follow Laravel's naming conventions
    protected $table = 'ev_rider_types';

    // Define which attributes are mass assignable
    protected $fillable = [
        'type',
        'status'
    ];

    // Set the primary key field (optional if 'id' is the primary key)
    protected $primaryKey = 'id';

    // Disable auto-incrementing if 'id' is not an integer (optional)
    public $incrementing = false;
    protected $keyType = 'string';

    // Specify the attributes that should be cast to native types
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => 'boolean',
    ];
}

