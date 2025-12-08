<?php

namespace Modules\VehicleServiceTicket\Entities;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Modules\City\Entities\City;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\BusinessSetting;
use Modules\RiderType\Entities\RiderType;
use Modules\Deliveryman\Entities\Deliveryman;
use Illuminate\Support\Facades\Http;
use DateTimeInterface;
use Modules\VehicleServiceTicket\Entities\FieldProxyTicket;

class VehicleTicket extends Model
{
    use HasFactory;
    
    protected $table = 'vehicle_service_tickets';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'ticket_id',
        'vehicle_no',
        'city_id',
        'area_id',
        'vehicle_type',
        'poc_name',
        'poc_contact_no',
        'driver_name',
        'driver_number',
        'issue_remarks',
        'repair_type',
        'address',
        'gps_pin_address',
        'lat',
        'long',
        'image',
        'created_datetime',
        'created_by',
        'customer_id',
        'created_role',
        'dm_id',
        'web_portal_status',
        'platform',
        'ticket_status',
    ];

    protected $casts = [
        'created_datetime' => 'datetime',
        'ticket_status' => 'integer',
        'web_portal_status' => 'integer',
        'city_id' => 'integer',
        'area_id' => 'integer',
        'dm_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    
    public function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function zone()
    {
        return $this->belongsTo(Zones::class, 'zone_id', 'id');
    }
    
    public function field_proxy_relation()
    {
        return $this->belongsTo(FieldProxyTicket::class, 'ticket_id', 'greendrive_ticketid');
    }
    
    public function current_city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    public function interest_city()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
    public function user()
    {
        return $this->belongsTo(Deliveryman::class, 'dm_id');
    }
    
    
}