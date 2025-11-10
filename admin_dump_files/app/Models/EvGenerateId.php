<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvGenerateId extends Model
{
    use HasFactory;
    protected $table = 'ev_generate_ids'; 
    protected $primaryKey = 'id'; 
    public $timestamps = true; 
    protected $fillable = [
        'temp_id',
        'permanent_id',
        'user_type',
        'user_id',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
