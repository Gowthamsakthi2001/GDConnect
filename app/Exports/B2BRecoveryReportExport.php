<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\MasterManagement\Entities\CustomerLogin;
use Carbon\Carbon;

class B2BRecoveryReportExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $date_range;
    protected $from_date;
    protected $to_date;
    protected $vehicle_type = [];
    protected $city = [];
    protected $zone = [];
    protected $vehicle_no = [];
    protected $accountability_type = [];
    protected $vehicle_make = [];
    protected $vehicle_model = [];
    protected $status = [];
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

        // Safe casting
        $this->vehicle_type      = (array) $vehicle_type;
        $this->city              = (array) $city;
        $this->zone              = (array) $zone;
        $this->vehicle_no        = (array) $vehicle_no;
        $this->accountability_type = (array) $accountability_type;
        $this->vehicle_model     = (array) $vehicle_model;
        $this->vehicle_make      = (array) $vehicle_make;

        // Status sometimes arrives as ["running,returned"]
        if (!empty($status) && isset($status[0]) && is_string($status[0]) && str_contains($status[0], ',')) {
            $this->status = explode(',', $status[0]);
        } else {
            $this->status = (array) $status;
        }
    }

    public function query()
    {
        $guard = Auth::guard('master')->check() ? 'master' : 'zone';
        $user = Auth::guard($guard)->user();

        $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');

        $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
            ->where('city_id', $user->city_id)
            ->pluck('id');

        $query = DB::table('b2b_tbl_recovery_request as rr')
            ->join('b2b_tbl_vehicle_assignments as va', 'rr.assign_id', '=', 'va.id')
            ->join('b2b_tbl_vehicle_requests as vr', 'va.req_id', '=', 'vr.req_id')
            ->leftJoin('ev_tbl_accountability_types as actype', 'actype.id', '=', 'vr.account_ability_type')
            ->leftJoin('ev_tbl_city as c', 'vr.city_id', '=', 'c.id')
            ->leftJoin('zones as z', 'vr.zone_id', '=', 'z.id')
            ->leftJoin('b2b_tbl_riders as r', 'va.rider_id', '=', 'r.id')
            ->leftJoin('ev_tbl_customer_logins as cl', 'r.created_by', '=', 'cl.id')
            ->leftJoin('ev_tbl_customer_master as cm', 'cl.customer_id', '=', 'cm.id')
            ->leftJoin('ev_tbl_asset_master_vehicles as v', 'va.asset_vehicle_id', '=', 'v.id')
            ->leftJoin('vehicle_qc_check_lists as qc', 'qc.id', '=', 'v.qc_id')
            ->leftJoin('ev_tbl_vehicle_models as vm', 'qc.vehicle_model', '=', 'vm.id')
            ->leftJoin('vehicle_types as vt', 'qc.vehicle_type', '=', 'vt.id')
            ->leftJoin('ev_tbl_customer_master as cr', 'cl.customer_id', '=', 'cr.id')
            ->leftJoin('ev_tbl_delivery_men as agent', 'agent.id', '=', 'rr.recovery_agent_id')
            ->select([
                'vr.req_id as req_id',//
                'r.name as rider_name',//
                'actype.name as accountability_type',//
                'v.permanent_reg_number as vehicle_no',//
                'v.chassis_number as chassis_number',//
                'v.id as vehicle_id',//
                'vt.name as vehicle_type',//
                'vm.vehicle_model as vehicle_model',//
                'vm.make as vehicle_make',//
                'r.mobile_no as mobile_no',//
                'cr.trade_name as created_by',//
                'cr.trade_name as poc_name',
                'cr.phone as poc_number',
                'c.city_name as city',//
                'z.name as zone',//
                'rr.description',//
                'rr.terms_condition',//
                'agent.first_name',//
                'agent.last_name',//
                'rr.created_at as created_at',//
                'rr.closed_by_type',//
                'rr.video',//
                'rr.images',//
                'rr.status',//
                'rr.agent_status',//
                'rr.created_by_type',//
                'rr.recovery_agent_id',//
                
            ]);

        if ($customerLoginIds->isNotEmpty()) {
            $query->whereIn('r.created_by', $customerLoginIds);
        }

        if ($guard === 'master') {
            $query->where('r.createdby_city', $user->city_id);
        }

        if ($guard === 'zone') {
            $query->where('r.createdby_city', $user->city_id)
                ->whereIn('r.assign_zone_id', $user->zone_id);
        }

        if (!empty($this->selectedIds)) {
            $query->whereIn('rr.id', $this->selectedIds);
        } else {
            if (!empty($this->city)) {
                $query->whereIn('vr.city_id', $this->city);
            }

            if (!empty($this->zone)) {
                $query->whereIn('vr.zone_id', $this->zone);
            }

            if ($this->from_date) {
                $query->whereDate('rr.created_at', '>=', $this->from_date);
            }

            if ($this->to_date) {
                $query->whereDate('rr.created_at', '<=', $this->to_date);
            }

            if (!empty($this->vehicle_model)) {
                $query->whereIn('qc.vehicle_model', $this->vehicle_model);
            }

            if (!empty($this->vehicle_type)) {
                $query->whereIn('qc.vehicle_type', $this->vehicle_type);
            }

            if (!empty($this->vehicle_make)) {
                $query->whereIn('vm.make', $this->vehicle_make);
            }
            
            if (!empty($this->vehicle_no)) {
                $query->whereIn('v.id', $this->vehicle_no);
            }
            
            // DATE FILTERS
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
                    // already passed via constructor
                    break;
                default:
                    $from = $to = now()->toDateString();
                    break;
            }

            if ($from && $to) {
                $query->whereBetween(DB::raw('DATE(rr.created_at)'), [$from, $to]);
            }
        
        }

        return $query->orderBy('rr.id', 'desc');
    }

    public function map($row): array
    {
        $this->sl++;

        $created_by = match ($row->created_by_type) {
            'b2b-web-dashboard'  => 'Customer',
            'b2b-admin-dashboard' => 'GDM',
            default => 'Unknown'
        };

        $terms = $row->terms_condition ? 'Accepted' : 'Not Accepted';

        $statusText = match ($row->status) {
            'opened'          => 'Opened',
            'closed'          => 'Closed',
            'agent_assigned'  => 'Agent Assigned',
            'not_recovered'   => 'Not Recovered',
            default           => '-',
        };

        $agentStatusText = match ($row->agent_status) {
            'opened'            => 'Opened',
            'in_progress'       => 'In Progress',
            'reached_location'  => 'Location Reached',
            'revisit_location'  => 'Location Revisited',
            'recovered'         => 'Recovered',
            'not_recovered'     => 'Not Recovered',
            'rider_contacted'   => 'Rider Contacted',
            'hold'              => 'Hold',
            'closed'            => 'Closed',
            default             => '-',
        };

        $agentName = $row->recovery_agent_id
            ? trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''))
            : '-';

        $createdAt = $row->created_at
            ? Carbon::parse($row->created_at)->format('d M Y h:i A')
            : '-';

        $closed_by = match ($row->closed_by_type) {
            'recovery-agent' => trim(($row->user->first_name ?? '') . ' ' . ($row->user->last_name ?? '')),
            'recovery-manager-dashboard' => $row->user->name ?? '-',
            default => '-'
        };

        $videoPath = $row->video
            ? asset('public/b2b/recovery_comments/' . $row->video)
            : '-';

        $ImagesPath = '-';
        if (!empty($row->images)) {
            $imgs = json_decode($row->images, true);
            if (is_array($imgs)) {
                $ImagesPath = implode(', ', array_map(fn($img) =>
                    asset("public/b2b/recovery_comments/$img")
                , $imgs));
            }
        }

        return [
            $this->sl,
            $row->req_id ?? '-',
            $row->accountability_type ?? '-',
            $row->vehicle_no ?? '-',
            $row->chassis_number ?? '-',
            $row->vehicle_id ?? '-',
            $row->vehicle_type ?? '-',
            $row->vehicle_model ?? '-',
            $row->vehicle_make ?? '-',
            $row->city ?? '-',
            $row->zone ?? '-',
            $row->description ?? '-',
            $terms,
            $row->created_by ?? '-',
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
    
    public function chunkSize(): int
        {
            return 500; // or 1000, depends on your requirement
        }
        
    public function headings(): array
    {
        return [
            'SL NO',
            'Request ID',
            'Accountability Name',
            'Vehicle Number',
            'Chassis Number',
            'Vehicle ID',
            'Vehicle Type',
            'Vehicle Model',
            'Vehicle Make',
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
}
