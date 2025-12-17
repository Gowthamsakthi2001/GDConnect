<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Modules\B2B\Entities\B2BRecoveryRequest;
use Illuminate\Support\Facades\DB;
use Modules\MasterManagement\Entities\RecoveryReasonMaster;//updated by Gowtham.S
use Carbon\Carbon;

class B2BAdminRecoveryRequestExport implements FromQuery, WithHeadings, WithMapping,WithChunkReading
{
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;
    protected $city=[];
    protected $zone=[];
    protected $accountability_type=[]; 
    protected $customer_id=[]; 
    protected $status=[]; 
    protected $vehicle_type=[]; 
    protected $vehicle_model=[]; 
    protected $vehicle_make=[]; 
    protected $date_filter; 

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = [], $zone = [],$status=[],$accountability_type = [],$customer_id=[] , $vehicle_type=[] , $vehicle_model=[],$vehicle_make=[], $date_filter)
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = (array)$city;
        $this->zone           = (array)$zone;
        $this->accountability_type = (array)$accountability_type; 
        $this->customer_id =(array) $customer_id; 
        $this->status = (array)$status; 
        $this->vehicle_type =(array) $vehicle_type; 
        $this->vehicle_model = (array)$vehicle_model; 
        $this->vehicle_make =(array) $vehicle_make; 
        $this->date_filter = $date_filter; 
    }

     public function query()
    {
        $query = \DB::table('b2b_tbl_recovery_request as vrr')
            ->leftJoin('b2b_tbl_vehicle_assignments as ass', 'ass.id', '=', 'vrr.assign_id')
            ->leftJoin('b2b_tbl_vehicle_requests as vhr', 'vhr.req_id', '=', 'ass.req_id')
            ->leftJoin('ev_tbl_asset_master_vehicles as vh', 'vh.id', '=', 'ass.asset_vehicle_id')
            ->leftJoin('vehicle_qc_check_lists as qc', 'qc.id', '=', 'vh.qc_id')
            ->leftJoin('ev_tbl_vehicle_models as vm', 'vm.id', '=', 'qc.vehicle_model')
            ->leftJoin('vehicle_types as vt', 'vt.id', '=', 'qc.vehicle_type')
            ->leftJoin('ev_tbl_accountability_types as actype', 'actype.id', '=', 'vhr.account_ability_type')
            ->leftJoin('b2b_tbl_riders as rider', 'rider.id', '=', 'ass.rider_id')
            ->leftJoin('users as agent', 'agent.id', '=', 'ass.assigned_agent_id')
            ->leftJoin('ev_tbl_city as cty', 'cty.id', '=', 'vhr.city_id')
            ->leftJoin('zones as zn', 'zn.id', '=', 'vhr.zone_id')
            ->leftJoin('ev_tbl_customer_logins as cml', 'cml.id', '=', 'rider.created_by')
            ->leftJoin('ev_tbl_customer_master as cm', 'cm.id', '=', 'cml.customer_id')
            ->leftJoin('ev_tbl_recovery_reason_master as reason', 'reason.id', '=', 'vrr.reason')
            ->where('vh.is_status', 'accepted')
            ->select([
                'vrr.*',
                'vhr.req_id',
                'actype.name as accountability_name',
                'rider.name as rider_name',
                'rider.mobile_no',
                'vh.permanent_reg_number',
                'qc.chassis_number',
                'vt.name as vehicle_type_name',
                'vm.vehicle_model',
                'vm.make as vehicle_make',
                'cm.trade_name as client_name',
                'cm.phone as client_phone',
                'cty.city_name',
                'zn.name as zone_name',
                'reason.label_name as recovery_reason',
                'agent.name as closed_by_name',
            ]);
        if (!empty($this->selectedIds)) {
            $query->whereIn('vrr.id', $this->selectedIds);
        } else {
            if (!empty($this->city) && !in_array('all', $this->city)) {
                $query->whereIn('vhr.city_id', $this->city);
            }
    
            if (!empty($this->zone) && !in_array('all', $this->zone)) {
                $query->whereIn('vhr.zone_id', $this->zone);
            }
            if (!empty($this->vehicle_model) && !in_array('all', $this->vehicle_model)) {
                $query->whereIn('qc.vehicle_model', $this->vehicle_model);
            }
    
            if (!empty($this->vehicle_type) && !in_array('all', $this->vehicle_type)) {
                $query->whereIn('qc.vehicle_type', $this->vehicle_type);
            }
    
            if (!empty($this->vehicle_make) && !in_array('all', $this->vehicle_make)) {
                $query->whereIn('vm.make', $this->vehicle_make);
            }
            if (!empty($this->accountability_type) && !in_array('all', $this->accountability_type)) {
                $query->whereIn('vhr.account_ability_type', (array) $this->accountability_type);
            }
            if (!empty($this->customer_id) && !in_array('all', $this->customer_id)) {
                $query->whereIn('cm.id', $this->customer_id);
            }
            if (!empty($this->status) && !in_array('all', $this->status)) {
                $query->whereIn('vrr.status', (array) $this->status);
            }
            if (!empty($this->date_filter)) {
                switch ($this->date_filter) {
    
                    case 'today':
                        $query->whereDate('vrr.created_at', today());
                        break;
    
                    case 'week':
                        $query->whereBetween('vrr.created_at', [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]);
                        break;
    
                    case 'last_15_days':
                        $query->whereBetween('vrr.created_at', [
                            now()->subDays(14)->startOfDay(),
                            now()->endOfDay(),
                        ]);
                        break;
    
                    case 'month':
                        $query->whereMonth('vrr.created_at', now()->month)
                              ->whereYear('vrr.created_at', now()->year);
                        break;
    
                    case 'year':
                        $query->whereYear('vrr.created_at', now()->year);
                        break;
                }
            }
    
            if (!empty($this->from_date)) {
                $query->whereDate('vrr.created_at', '>=', $this->from_date);
            }
    
            if (!empty($this->to_date)) {
                $query->whereDate('vrr.created_at', '<=', $this->to_date);
            }
        }
    
        return $query->orderBy('vrr.id', 'desc');
    }

    public function map($row): array
    {
        $mapped = [];
    
        $aging = ($row->status === 'closed' && $row->closed_at)
            ? \Carbon\Carbon::parse($row->created_at)
                ->diffForHumans(\Carbon\Carbon::parse($row->closed_at), true)
            : \Carbon\Carbon::parse($row->created_at)
                ->diffForHumans(now(), true);
        $statusText = '-';
        switch ($row->status) {
            case 'opened':
                $statusText = 'Opened';
                break;
            case 'closed':
                $statusText = 'Closed';
                break;
            case 'agent_assigned':
                $statusText = 'Agent Assigned';
                break;
            case 'not_recovered':
                $statusText = 'Not Recovered';
                break;
            default:
                $statusText = '-';
        }
        
        $agentStatusText = '-';
        switch ($row->agent_status) {
            case 'opened':
                $agentStatusText = 'Opened';
                break;
            case 'in_progress':
                $agentStatusText = 'In Progress';
                break;
            case 'location_reached':
                $agentStatusText = 'Location Reached';
                break;
            case 'location_revisited':
                $agentStatusText = 'Location Revisited';
                break;
            case 'recovered':
                $agentStatusText = 'Recovered';
                break;
            case 'not_recovered':
                $agentStatusText = 'Not Recovered';
                break;
            case 'closed':
                $agentStatusText = 'Closed';
                break;
            default:
                $agentStatusText = '-';
        }
        
        $closed_by = '-';
        if ($row->closed_by_type == 'recovery-agent') {
            $closed_by = DB::table('ev_tbl_delivery_men')
                ->where('id', $row->closed_by)
                ->select('first_name','last_name')
                ->first();
            $closed_by = $closed_by ? trim($closed_by->first_name . ' ' . $closed_by->last_name) : '-';
        } elseif ($row->closed_by_type == 'recovery-manager-dashboard') {
            $closed_by = DB::table('users')
                ->where('id', $row->closed_by)
                ->select('name')
                ->first();
            $closed_by = $closed_by ? trim($closed_by->name) : '-';
        } elseif ($row->closed_by_type == 'b2b-customer') {
            $closed_by = DB::table('ev_tbl_customer_logins')
                ->where('id', $row->closed_by)
                ->select('email')
                ->first();
            $closed_by = $closed_by ? trim($closed_by->email) : '-';
        }
    
        foreach ($this->selectedFields as $key) {
            switch ($key) {
    
                case 'req_id':
                    $mapped[] = $row->req_id ?? '-';
                    break;
    
                case 'accountability_type':
                    $mapped[] = $row->accountability_name ?? '-';
                    break;
    
                case 'rider_name':
                    $mapped[] = $row->rider_name ?? '-';
                    break;
    
                case 'vehicle_no':
                    $mapped[] = $row->permanent_reg_number ?? '-';
                    break;
    
                case 'chassis_number':
                    $mapped[] = $row->chassis_number ?? '-';
                    break;
    
                case 'vehicle_type':
                    $mapped[] = $row->vehicle_type_name ?? '-';
                    break;
    
                case 'vehicle_model':
                    $mapped[] = $row->vehicle_model ?? '-';
                    break;
    
                case 'vehicle_make':
                    $mapped[] = $row->vehicle_make ?? '-';
                    break;
    
                case 'mobile_no':
                    $mapped[] = $row->mobile_no ?? '-';
                    break;
    
                case 'poc_name':
                    $mapped[] = $row->client_name ?? '-';
                    break;
    
                case 'poc_number':
                    $mapped[] = $row->client_phone ?? '-';
                    break;
    
                case 'city':
                    $mapped[] = $row->city_name ?? '-';
                    break;
    
                case 'zone':
                    $mapped[] = $row->zone_name ?? '-';
                    break;
    
                case 'reason':
                    $mapped[] = $row->recovery_reason ?? '-';
                    break;
    
                case 'description':
                    $mapped[] = $row->description ?? '-';
                    break;
    
                case 'created_by':
                        if ($row->created_by_type == 'b2b-web-dashboard') {
                            $mapped[] = 'Customer';
                        } elseif ($row->created_by_type == 'b2b-admin-dashboard') {
                            $mapped[] = 'Admin';
                        } else {
                            $mapped[] = '-';
                        }
                    break;
    
                case 'status':
                    $mapped[] = $statusText;
                    break;
    
                case 'agent_status':
                    $mapped[] = !empty($row->agent_status)
                        ? ucfirst($row->agent_status)
                        : 'Not Assigned';
                    break;
    
                case 'created_at':
                    $mapped[] = $row->created_at
                        ? \Carbon\Carbon::parse($row->created_at)->format('d M Y h:i A')
                        : '-';
                    break;
    
                case 'closed_at':
                    $mapped[] = $row->closed_at
                        ? \Carbon\Carbon::parse($row->closed_at)->format('d M Y h:i A')
                        : '-';
                    break;
    
                case 'aging':
                    $mapped[] = $aging;
                    break;
    
                case 'recovery_images':
                    $files = is_string($row->images)
                        ? json_decode($row->images, true)
                        : ($row->images ?? []);
    
                    $mapped[] = !empty($files)
                        ? collect($files)->map(fn($f) =>
                            asset('b2b/recovery_comments/'.$f)
                        )->implode(', ')
                        : '-';
                    break;
    
                case 'recovery_video':
                    $mapped[] = !empty($row->videos)
                        ? asset('b2b/recovery_comments/'.$row->videos)
                        : '-';
                    break;
    
                case 'closed_by':
                    $mapped[] = $closed_by ?? '-';
                    break;
    
                default:
                    $mapped[] = $row->$key ?? '-';
            }
        }
    
        return $mapped;
    }


    public function headings(): array
    {
        $headers = [];

        $customHeadings = [
            'req_id'        => 'Request ID',
            'accountability_type'    => 'Accountablity Type', 
            'vehicle_no'    => 'Vehicle Number',
            'chassis_number'=> 'Chassis Number',
            'vehicle_type'    => 'Vehicle Type',
            'vehicle_model'    => 'Vehicle Model',
            'vehicle_make'    => 'Vehicle Make',
            'rider_name'    => 'Rider Name',
            'mobile_no'     => 'Mobile No',
            'city'          => 'City',
            'poc_name'      => 'POC Name',
            'poc_number'   => 'POC Contact',
            'zone'          => 'Zone',
            'reason'        => 'Reason',
            'description'   => 'Description',
            'recovery_images'=>'Recovery Images',
            'recovery_video' =>'Recovery Video',
            'created_by'    => 'Created By',
            'agent_status'  => 'Agent Status',
            'closed_by'     => 'Closed By',
            'status'        => 'Status',
            'created_at'    => 'Created Date & Time',
            'closed_at'    => 'Closed Date & Time',
            'aging'        => 'Aging'
        ];
        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }

    public function chunkSize(): int
    {
        return 1000; 
    }
}
