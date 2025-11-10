<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table ="ev_master_holidays";
    protected $fillable = [
        'title',
        'date',
        'description',
        'type',
        'is_recurring',
        'is_active',
        'recurring_group_id'
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean'
    ];

    // Scope for active holidays
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
