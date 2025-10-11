<?php

// app/Models/Notification.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'push_notifications'; // Specify the table name if it doesn't follow Laravel's conventions
    
    protected $fillable = [
        'data',
        'dm_id',
        'user_id',
        'status'
    ];

    protected $casts = [
        'data' => 'array',
        'status' => 'boolean'
    ];


}
