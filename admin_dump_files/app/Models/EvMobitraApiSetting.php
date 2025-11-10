<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvMobitraApiSetting extends Model
{
    use HasFactory;

    protected $table = 'ev_mobitra_api_settings'; 
    protected $primaryKey = 'id'; 
    public $timestamps = true; 

    protected $fillable = [
        'key_name',
        'value'
    ];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
