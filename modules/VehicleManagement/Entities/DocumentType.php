<?php

namespace Modules\VehicleManagement\Entities;

use App\Traits\FormatTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use FormatTimestamps;
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
    ];
}
