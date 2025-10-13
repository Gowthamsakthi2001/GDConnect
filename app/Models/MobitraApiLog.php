<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobitraApiLog extends Model
{
    use HasFactory;

    protected $table = 'mobitra_api_logs'; 
    protected $primaryKey = 'id'; 

    public $timestamps = true; 
    protected $fillable = [
        'user_id',
        'api_user_id',
        'api_username',
        'api_endpoint',
        'status_code',
        'status_type'
    ];

}