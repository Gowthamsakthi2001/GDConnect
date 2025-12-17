<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\B2B\Entities\B2BReportAccident;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\MasterManagement\Entities\CustomerLogin;

class B2BDeploymentReportExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $date_range;
    protected $from_date;
    protected $to_date;
    protected $vehicle_type = [];
    protected $city = [];
    protected $zone = [];
    protected $vehicle_no = [];
    protected $vehicle_make = [];
    protected $vehicle_model = [];
    protected $status = [];
    protected $accountability_type = [];
    protected $sl = 0;

    public function __construct(
        $date_range, 
        $from_date, 
        $to_date, 
        $vehicle_type = [], 
        $city = [], 
        $zone = [], 
        $vehicle_no = [], 
        $status = [], 
        $accountability_type = [],
        $vehicle_model = [],
        $vehicle_make = []
    ) {
        $this->date_range   = $date_range;
        $this->from_date    = $from_date;
        $this->to_date      = $to_date;

        // ALWAYS convert to array safely
        $this->vehicle_type = (array) $vehicle_type;
        $this->vehicle_model = (array) $vehicle_model;
        $this->vehicle_make = (array) $vehicle_make;
        $this->city = (array) $city;
        $this->zone = (array) $zone;
        $this->vehicle_no = (array) $vehicle_no;
        $this->status = (array) $status;
        $this->accountability_type = (array) $accountability_type;
    }

    public function query()
    {   
        $guard = Auth::guard('master')->check() ? 'master' : 'zone';
        $user  = Auth::guard($guard)->user();

        $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');

        $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
            ->where('city_id', $user->city_id)
            ->pluck('id');
            
        $query = DB::table('b2b_tbl_vehicle_assignments as ass')
            ->join('ev_tbl_asset_master_vehicles as vh', 'vh.id', '=', 'ass.asset_vehicle_id')
            ->leftJoin('b2b_tbl_vehicle_requests as vhr', 'vhr.req_id', '=', 'ass.req_id')
            ->leftJoin('ev_tbl_accountability_types as actype', 'actype.id', '=', 'vhr.account_ability_type')
            ->leftJoin('vehicle_qc_check_lists as qc', 'qc.id', '=', 'vh.qc_id')
            ->leftJoin('ev_tbl_vehicle_models as vm', 'vm.id', '=', 'vh.model')
            ->leftJoin('vehicle_types as vt', 'vt.id', '=', 'vh.vehicle_type')
            ->leftJoin('ev_tbl_city as cty', 'cty.id', '=', 'qc.location')
            ->leftJoin('zones as zn', 'zn.id', '=', 'qc.zone_id')
            ->leftJoin('b2b_tbl_riders as rider', 'rider.id', '=', 'ass.rider_id')
            ->leftJoin('users as agent', 'agent.id', '=', 'ass.assigned_agent_id')
            ->leftJoin('ev_tbl_customer_logins as cml', 'cml.id', '=', 'vhr.created_by')
            ->leftJoin('ev_tbl_customer_master as cm', 'cm.id', '=', 'cml.customer_id')
            ->leftJoin('b2b_tbl_recovery_request as rec', 'rec.assign_id', '=', 'ass.id') // guess FK; if different adjust
            ->where('vh.is_status', 'accepted');
        
         // Restrict to customer login scope
            if ($customerLoginIds->isNotEmpty()) {
                $query->whereIn('vhr.created_by', $customerLoginIds);
            }

            if ($guard === 'master') {
                $query->where('vhr.city_id', $user->city_id);
            }

            if ($guard === 'zone') {
                $query->where('vhr.city_id', $user->city_id)
                      ->where('vhr.zone_id', $user->zone_id);
            }

            if (!empty($this->zone)) {
                $query->whereIn('vhr.zone_id', $this->zone);
            }
            
        // selected ids
        if (!empty($this->selectedIds)) {
            $query->whereIn('ass.id', $this->selectedIds);
        }

        // city filter
        if (!empty($this->city) && !in_array('all', $this->city)) {
            $query->whereIn('cty.id', $this->city);
        }

        // zone filter
        if (!empty($this->zone) && !in_array('all', $this->zone)) {
            $query->whereIn('zn.id', $this->zone);
        }

        // status
        if (!empty($this->status) && !in_array('all', $this->status)) {
            $query->whereIn('ass.status', $this->status);
        }

        // vehicle_type (from qc table)
        if (!empty($this->vehicle_type) && !in_array('all', $this->vehicle_type)) {
            $query->whereIn('qc.vehicle_type', $this->vehicle_type);
        }

        // vehicle_model (from qc)
        if (!empty($this->vehicle_model) && !in_array('all', $this->vehicle_model)) {
            $query->whereIn('qc.vehicle_model', $this->vehicle_model);
        }

        // vehicle_make (from vm)
        if (!empty($this->vehicle_make) && !in_array('all', $this->vehicle_make)) {
            $query->whereIn('vm.make', $this->vehicle_make);
        }
        
        if (!empty($this->vehicle_no) && !in_array('all', $this->vehicle_no)) {
            $query->whereIn('vh.id', $this->vehicle_no);
        }
        
        // accountability_type (from qc)
        if (!empty($this->accountability_type) && !in_array('all', $this->accountability_type)) {
            $query->whereIn('qc.accountability_type', $this->accountability_type);
        }

        // customer_id filter
        if (!empty($this->customer_id) && !in_array('all', $this->customer_id)) {
            if (in_array(1, $this->accountability_type)) {
                $query->whereIn('vh.client', $this->customer_id);
            } else {
                $query->whereIn('qc.customer_id', $this->customer_id);
            }
        }

        // date filters
        $from = $this->from_date;
        $to   = $this->to_date;

        switch ($this->date_range) {
            case 'yesterday':
                $from = $to = now()->subDay()->toDateString();
                break;
            case 'last7':
                $from = now()->subDays(6)->toDateString();
                $to   = now()->toDateString();
                break;
            case 'last30':
                $from = now()->subDays(29)->toDateString();
                $to   = now()->toDateString();
                break;
            case 'custom':
                break;
            default:
                $from = $to = now()->toDateString();
                break;
        }

        if ($from && $to) {
            $query->whereBetween('ass.created_at', [$from, $to]);
        } elseif ($from) {
            $query->whereDate('ass.created_at', '>=', $from);
        } elseif ($to) {
            $query->whereDate('ass.created_at', '<=', $to);
        }

        // Return only latest assignment per asset_vehicle_id (same as your reference)
        return $query
            ->whereIn('ass.id', function ($q) {
                $q->selectRaw('MAX(id)')
                    ->from('b2b_tbl_vehicle_assignments')
                    ->groupBy('asset_vehicle_id');
            })
            ->orderBy('ass.id', 'desc')
            ->select([
                'ass.id',
                'ass.req_id',
                'ass.asset_vehicle_id',
                'ass.handover_type',
                'ass.odometer_value',
                'ass.odometer_image',
                'ass.vehicle_front',
                'ass.vehicle_back',
                'ass.vehicle_top',
                'ass.vehicle_left',
                'ass.vehicle_right',
                'ass.vehicle_battery',
                'ass.vehicle_charger',
                'ass.created_at',
                'ass.status',
                'vhr.battery_type',
                'actype.name as accountability_type',
                'vh.chassis_number',
                'vh.permanent_reg_number as vehicle_no',
                'vt.name as vehicle_type_name',
                'vm.vehicle_model',
                'vm.make',
                'cty.city_name',
                'zn.name as zone_name',
                'agent.name as agent_name',
                'agent.email as agent_email',
                'agent.address as agent_address',
                'rider.name as rider_name',
                'rider.mobile_no',
                
                'cm.trade_name as customer_name',
                'cm.phone as customer_contact',
                'cm.email as customer_email',
                'cm.start_date',
                'cm.end_date',
                // include recovery created_by_type for status logic if needed
                'rec.created_by_type as recovery_created_by_type'
            ]);
    }

    public function map($row): array
{
    $this->sl++;
    
        $battery_type = '';
        if($row->battery_type == 1){
            $battery_type = 'Self-Charging';
        }
        else if($row->battery_type == 1){
            $battery_type = 'Portable';
        }else{
            $battery_type = '-';
        }
    // Safe created_at formatting
    $createdAt = $row->created_at 
        ? Carbon::parse($row->created_at)->format('d M Y h:i A') 
        : '-';

    // Status mapping
    $status = match ($row->status) {
        'running'            => 'Running',
        'accident'           => 'Accident',
        'under_maintenance'  => 'Under Maintenance',
        'recovery_request'   => $row->recovery_created_by_type === 'b2b-admin-dashboard'
                                ? 'GDM Recovery Initiated'
                                : 'Client Recovery Initiated',
        'recovered'          => 'Recovered',
        'return_request'     => 'Return Request',
        'returned'           => 'Returned',
        default              => 'Unknown',
    };

    return [
        $this->sl,
        $row->req_id ?? '-',
        $row->accountability_type ?? '-',
        $row->vehicle_no ?? '-',
        $row->chassis_number ?? '-',
        $row->asset_vehicle_id ?? '-',
        $row->make ?? '-',
        $row->vehicle_model ?? '-',
        $row->vehicle_type_name ?? '-',
        $battery_type ?? '-',  // battery type not available in select
        $row->city_name ?? '-',
        $row->zone_name ?? '-',
        $row->customer_name ?? '-',
        $row->rider_name ?? '-',       // FIXED
        $row->mobile_no ?? '-',
        $row->agent_name ?? '-',
        $row->odometer_value ?? '-',

        $row->odometer_image 
            ? asset('public/b2b/odometer_images/' . $row->odometer_image) 
            : '-',

        $row->vehicle_front 
            ? asset('public/b2b/vehicle_front/' . $row->vehicle_front) 
            : '-',

        $row->vehicle_back 
            ? asset('public/b2b/vehicle_back/' . $row->vehicle_back) 
            : '-',

        $row->vehicle_top 
            ? asset('public/b2b/vehicle_top/' . $row->vehicle_top) 
            : '-',

        $row->vehicle_left 
            ? asset('public/b2b/vehicle_left/' . $row->vehicle_left) 
            : '-',

        $row->vehicle_right 
            ? asset('public/b2b/vehicle_right/' . $row->vehicle_right) 
            : '-',

        $row->vehicle_battery 
            ? asset('public/b2b/vehicle_battery/' . $row->vehicle_battery) 
            : '-',

        $row->vehicle_charger 
            ? asset('public/b2b/vehicle_charger/' . $row->vehicle_charger) 
            : '-',

        $createdAt,   // Requested Date
        $createdAt,   // Assignment Date (if different give me field)
        $status
    ];
}

    
    public function chunkSize(): int
    {
        return 500;
    }
    
    public function headings(): array
    {
        return [
            'SL NO','Request ID','Accountability name','Vehicle Number','Chassis Number','Vehicle ID',
            'Vehicle Make','Vehicle Model','Vehicle Type','Battery Type','City','Zone','Customer Name',
            'Rider Name','Rider Number','Agent Name','Odometer value','Odometer Image',
            'Vehicle Front Image','Vehicle Back Image','Vehicle Top Image','Vehicle Left Image',
            'Vehicle Right Image','Vehicle Battery Image','Vehicle Charger Image','Requested Date',
            'Assignment Date','Status',
     
        ];
    }
}
