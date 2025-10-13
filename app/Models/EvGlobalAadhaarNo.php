<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvGlobalAadhaarNo extends Model
{
    use HasFactory;
    protected $table = 'ev_global_aadhaar_no';
    protected $primaryKey = 'id';
    protected $fillable = ['aadhaar_no', 'status'];
    protected $casts = [
        'status' => 'integer',
    ];
    
     protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s'); // Customize the format as needed
    }
}
