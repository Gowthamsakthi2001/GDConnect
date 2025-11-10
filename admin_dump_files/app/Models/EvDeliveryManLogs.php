<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Deliveryman\Database\factories\DeliverymanFactory;
use Carbon\Carbon;
use Modules\Zones\Entities\Zones;
use Modules\City\Entities\City;
use Modules\City\Entities\Area;
use Modules\Clients\Entities\Client;
use Modules\Clients\Entities\ClientHub;
use Modules\LeaveManagement\Entities\LeaveType;
use Modules\LeaveManagement\Entities\LeaveRequest;
use Modules\Deliveryman\Entities\DeliveryManLogs;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\BusinessSetting;
use Modules\RiderType\Entities\RiderType;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Auth\User as Authenticatable;

class EvDeliveryManLogs extends Authenticatable
{
    use HasFactory;

    protected $table = 'ev_delivery_man_logs';

    protected $fillable = [
        'user_id','user_type','punched_in','punched_out','punchin_latitude','punchin_longitude','punchout_latitude','punchedout_longitude','client_id','probation_period','status'
    ];
  
}

