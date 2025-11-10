<?php

namespace Modules\Inventory\Entities;

use App\Traits\FormatTimestamps;
use App\Traits\GenerateCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use FormatTimestamps;
    use GenerateCode;
    use HasFactory;

    /**
     * The mass assignable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    // cast attributes
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the vendor's code.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getCodeAttribute($value)
    {
        return setting('vendor.code_prefix').$value;
    }

    // created generate code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendor) {
            $vendor->code = $vendor->generateCode();
        });
    }
}
