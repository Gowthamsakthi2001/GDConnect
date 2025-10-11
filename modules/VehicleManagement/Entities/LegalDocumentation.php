<?php

namespace Modules\VehicleManagement\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Entities\Vendor;

class LegalDocumentation extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type_id',
        'vehicle_id',
        'issue_date',
        'expiry_date',
        'charge_paid',
        'vendor_id',
        'commission',
        'notify_before',
        'email',
        'document_file_path',
    ];

    protected static function newFactory()
    {
        return \Modules\VehicleManagement\Database\factories\LegalDocumentationFactory::new();
    }

    public function document_type()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function getCurrentStatusAttribute()
    {

        if ($this->expiry_date < now()) {
            return '<span class="badge bg-danger py-2 px-4 rounded-pill">'.localize('Expired').'</span>';
        } elseif ($this->expiry_date <= now()->addDays(30)) {
            return '<span class="badge bg-warning py-2 px-4 rounded-pill">'.localize('Expire Soon').'</span>';
        } else {
            return '<span class="badge bg-success py-2 px-4 rounded-pill">'.localize('Available').'</span>';
        }
    }

    /**
     * Get the file url attribute.
     *
     * @return ?string
     */
    public function getDocumentFileUrlAttribute(): ?string
    {
        return $this->document_file_path ? storage_asset($this->document_file_path) : null;
    }
}
