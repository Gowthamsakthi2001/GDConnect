<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvLeadSource extends Model
{
    use HasFactory;

    // Define the table associated with the model (optional if it follows Laravel's naming convention)
    protected $table = 'ev_tbl_lead_source';

    // Define the fillable properties (attributes that can be mass assigned)
    protected $fillable = [
        'source_name',
        'status',
        // Add other fields if necessary
    ];

    // If you want to handle timestamps automatically
    public $timestamps = true;

    // Optionally, you can customize the created_at and updated_at column names
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // Casts for attributes
    protected $casts = [
        'status' => 'boolean', // Assuming status is a boolean field
        'created_at' => 'datetime', // Cast created_at to a Carbon instance
        'updated_at' => 'datetime', // Cast updated_at to a Carbon instance
        // Add other fields and their types if necessary
    ];
}
