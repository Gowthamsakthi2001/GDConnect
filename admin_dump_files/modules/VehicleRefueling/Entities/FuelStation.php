<?php

namespace Modules\VehicleRefueling\Entities;

use App\Traits\FormatTimestamps;
use App\Traits\GenerateCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Entities\Vendor;

class FuelStation extends Model
{
    use FormatTimestamps;
    use GenerateCode;
    use HasFactory;

    // set the mass assignable fields for the table
    protected $fillable = [
        'vendor_id',
        'code',
        'name',
        'contact_person',
        'contact_number',
        'address',
        'is_active',
    ];

    // cast
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the code.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getCodeAttribute($value)
    {
        return setting('fuel.station_code_prefix').$value;
    }

    // created generate code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendor) {
            $vendor->code = $vendor->generateCode();
        });
    }

    /**
     * Get the vendor that owns the FuelStation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }
}
