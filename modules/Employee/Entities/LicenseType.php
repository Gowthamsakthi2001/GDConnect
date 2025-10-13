<?php

namespace Modules\Employee\Entities;

use App\Traits\FormatTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseType extends Model
{
    use FormatTimestamps;
    use HasFactory;

    protected $fillable = ['name'];
}
