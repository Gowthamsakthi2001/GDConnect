<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    use HasFactory;

    protected $table = 'business_settings'; 
    protected $primaryKey = 'id'; 

    public $timestamps = true; 
    protected $fillable = [
        'key_name',
        'value',
        'created_at',
        'updated_at'
    ];

}
