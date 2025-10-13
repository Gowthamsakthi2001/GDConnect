<?php

namespace Modules\Inventory\Entities;

use App\Traits\FormatTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLocation extends Model
{
    use FormatTimestamps;
    use HasFactory;

    protected $fillable = [
        'name',
        'room',
        'self',
        'drawer',
        'capacity',
        'dimension',
        'is_active',
    ];

    protected $casts = [
        'room' => 'integer',
        'self' => 'integer',
        'drawer' => 'integer',
        'capacity' => 'integer',
        'dimension' => 'integer',
        'is_active' => 'boolean',

    ];
}
