<?php

namespace Modules\Leads\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Leads\Database\factories\LeadsFactory;
use Modules\LeadSource\Entities\LeadSource;
use Modules\City\Entities\City;
use Modules\City\Entities\Area;
class leads extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_leads';

    // protected $fillable = ['telecaller_status', 'source', 'assigned', 'f_name', 'l_name','phone_number','current_city','intrested_city','vehicle_type','lead_sources','register_date','active_status','task','description'];
    protected $guarded = [];
    protected $casts = [
        'telecaller_status' => 'string',
        'source' => 'integer',
        'assigned' => 'integer',
        'f_name' => 'string',
        'l_name' => 'string',
        'phone_number' => 'string',
        'current_city' => 'integer',
        'intrested_city' => 'string',
        'vehicle_type' => 'integer',
        'lead_sources' => 'integer',
        'register_date' => 'date',
        'active_status' => 'string',
        'task' => 'string',
        'description' => 'string',
    ];
    
    public function get_source()
    {
        return $this->belongsTo(LeadSource::class, 'source');
    }
    public function get_city()
    {
        return $this->belongsTo(City::class, 'current_city');
    }
    public function get_area()
    {
        return $this->belongsTo(Area::class, 'intrested_city');
    }
}
