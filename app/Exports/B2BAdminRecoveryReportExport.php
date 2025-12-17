<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\B2B\Entities\B2BRecoveryRequest;
use Modules\MasterManagement\Entities\CustomerLogin;
use Carbon\Carbon;

class B2BAdminRecoveryReportExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $date_range;
    protected $from_date;
    protected $to_date;
    protected $vehicle_type=[];
    protected $vehicle_model=[];
    protected $vehicle_make=[];
    protected $city=[];
    protected $zone=[];
    protected $vehicle_no;
    protected $status=[];
    protected $customer_id=[];
    protected $accountability_type=[];
    protected $sl = 0;

    public function __construct($date_range, $from_date, $to_date, $vehicle_type=[],$vehicle_model=[],$vehicle_make=[], $city=[], $zone=[], $vehicle_no =[], $status=[] , $customer_id=[] ,$accountability_type=[])
    {
        $this->date_range   = $date_range;
        $this->from_date    = $from_date;
        $this->to_date      = $to_date;
         $this->vehicle_type = (array)$vehicle_type;
        $this->vehicle_model = (array)$vehicle_model;
        $this->vehicle_make = (array)$vehicle_make;
        $this->city         = (array)$city;
        $this->zone         = (array)$zone;
        $this->vehicle_no   = $vehicle_no;
        $this->status   = (array)$status;
        $this->customer_id   = (array)$customer_id;
        $this->accountability_type   = (array)$accountability_type;
        
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
            ->leftJoin('ev_tbl_delivery_men as recover_agent', 'recover_agent.id', '=', 'ass.assigned_agent_id')
            ->leftJoin('ev_tbl_city as cty', 'cty.id', '=', 'vhr.city_id')
            ->leftJoin('zones as zn', 'zn.id', '=', 'vhr.zone_id')
            ->leftJoin('ev_tbl_customer_logins as cml', 'cml.id', '=', 'rider.created_by')
            ->leftJoin('ev_tbl_customer_master as cm', 'cm.id', '=', 'cml.customer_id')
            ->leftJoin('ev_tbl_recovery_reason_master as reason', 'reason.id', '=', 'vrr.reason')
            ->where('vh.is_status', 'accepted')
            ->select([
                'vhr.req_id',
                'actype.name as accountability_name',
                'vh.permanent_reg_number',
                'qc.chassis_number',
                'vh.id as vehicle_id',
                'vt.name as vehicle_type_name',
                'vm.vehicle_model',
                'vm.make as vehicle_make',
                'cty.city_name',
                'zn.name as zone_name',
                'vrr.description as description',
                'vrr.terms_condition as terms_condition',
                'cm.trade_name as client_name',
                'cm.phone as client_phone',
                'rider.name as rider_name',
                'rider.mobile_no',
                'recover_agent.first_name as agent_first_name',
                'recover_agent.last_name as agent_last_name',
                'vrr.closed_by as closed_by',
                'vrr.closed_by_type as closed_by_type',
                'vrr.video as video',
                'vrr.images as images',
                'vrr.created_by_type as created_by_type',
                'vrr.created_at as created_at',
                'vrr.status as status',
                'vrr.agent_status as agent_status',
                'reason.label_name as recovery_reason'
            
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
                if ($this->vehicle_no) {
                $vehicleNos = (array) $this->vehicle_no;
                $query->whereIn('vh.id', $vehicleNos);
                }
            // Date range handling
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
                $query->whereDate('vrr.created_at', '>=', $from)
                    ->whereDate('vrr.created_at', '<=', $to);
            }
        }
    
        return $query->orderBy('vrr.id', 'desc');
    }
    
    public function map($row): array
    {
        $this->sl++;
       
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
    
        
        $agentName = trim(($row->agent_first_name ?? '') . ' ' . ($row->agent_last_name ?? ''));
        $agentName = $agentName ?: '-';
        
        $createdAt = $row->created_at ? Carbon::parse($row->created_at)->format('d M Y h:i A') : '-';
            
            
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

        
        $videoPath = '-';
        if (!empty($row->video)) {
            $videoPath = asset('public/b2b/recovery_comments/' . $row->video);
        }
        $ImagesPath = '-';

        if (!empty($row->images)) {
            $images = json_decode($row->images, true);
        
            if (is_array($images) && count($images) > 0) {
                $imageUrls = array_map(function ($img) {
                    return asset('public/b2b/recovery_comments/' . $img);
                }, $images);
                $ImagesPath = implode(', ', $imageUrls);
            }
        }
        
        $created_by = "Unknown";
        if($row->created_by_type == 'b2b-web-dashboard'){
            $created_by = 'Customer';
        }elseif($row->created_by_type == 'b2b-admin-dashboard'){
            $created_by = 'GDM';
        }
    
        return [
            $this->sl,
            $row->req_id ?? '-',
            $row->accountability_name ?? '-',
            $row->recovery_reason ?? '-',
            $row->permanent_reg_number ?? '-',
            $row->chassis_number ?? '-',
            $row->vehicle_id ?? '-',
            $row->vehicle_make ?? '-',
            $row->vehicle_model ?? '-',
            $row->vehicle_type_name ?? '-',
            $row->city_name ?? '-',
            $row->zone_name ?? '-',
            $row->description ?? '-',
            $row->terms_condition == 1 ? 'Accepted' : 'Not Accepted',
            $row->client_name ?? '-',
            $row->rider_name ?? '-',
            $row->mobile_no ?? '-',
            $agentName,
            $closed_by,
            $videoPath,
            $ImagesPath,
            $created_by,
            $createdAt,
            $statusText,
            $agentStatusText,
        ];
    }
    
    public function headings(): array
    {
        return [
            'SL NO',
            'Request ID',
            'Accountability Type',
            'Recovery Reason',
            'Vehicle Number',
            'Chassis Number',
            'Vehicle ID',
            'Vehicle Make',
            'Vehicle Model',
            'Vehicle Type',
            'City',
            'Zone',
            'Description',
            'Terms & Condition',
            'Customer Name',
            'Rider Name',
            'Rider Number',
            'Agent Name',
            'Closed By',
            'Video',
            'Images',
            'Created By',
            'Created Date & Time',
            'Status',
            'Agent Status'
        ];
    }
    public function chunkSize(): int
    {
        return 1000;
    }
}
