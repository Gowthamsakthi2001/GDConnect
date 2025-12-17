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

class B2BClientAccidentReportExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $date_range;
    protected $from_date;
    protected $to_date;

    protected $vehicle_type = [];
    protected $city = [];
    protected $zone = [];
    protected $vehicle_no = [];
    protected $status = [];
    protected $accountability_type = [];
    protected $vehicle_model = [];
    protected $vehicle_make = [];

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

        $this->vehicle_type = (array) $vehicle_type;
        $this->city         = (array) $city;
        $this->zone         = (array) $zone;
        $this->vehicle_no   = (array) $vehicle_no;
        $this->vehicle_model = (array) $vehicle_model;
        $this->vehicle_make  = (array) $vehicle_make;
        $this->accountability_type = (array) $accountability_type;
        
        // Status safe handling
        if (!empty($status) && is_string($status[0])) {
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

        $query = DB::table('b2b_tbl_report_accident as ra')
            ->join('b2b_tbl_vehicle_assignments as va', 'ra.assign_id', '=', 'va.id')
            ->join('b2b_tbl_vehicle_requests as vr', 'va.req_id', '=', 'vr.req_id')
            ->leftJoin('ev_tbl_accountability_types as actype', 'actype.id', '=', 'vr.account_ability_type')
            ->leftJoin('ev_tbl_city as c', 'vr.city_id', '=', 'c.id')
            ->leftJoin('zones as z', 'vr.zone_id', '=', 'z.id')
            ->leftJoin('b2b_tbl_riders as r', 'va.rider_id', '=', 'r.id')
            ->leftJoin('ev_tbl_customer_logins as cl', 'r.created_by', '=', 'cl.id')
            ->leftJoin('ev_tbl_asset_master_vehicles as v', 'va.asset_vehicle_id', '=', 'v.id')
            ->leftJoin('vehicle_qc_check_lists as qc', 'qc.id', '=', 'v.qc_id')
            ->leftJoin('ev_tbl_vehicle_models as vm', 'qc.vehicle_model', '=', 'vm.id')
            ->leftJoin('vehicle_types as vt', 'qc.vehicle_type', '=', 'vt.id')
            ->leftJoin('ev_tbl_customer_master as cr', 'cl.customer_id', '=', 'cr.id')
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
                'cl.email as created_by',//
                'cr.trade_name as poc_name',
                'cr.phone as poc_number',
                'c.city_name as city',//
                'z.name as zone',//
                'ra.created_at',//
                'ra.location_of_accident',//
                'ra.accident_type',//
                'ra.description',//
                'ra.vehicle_damage',//
                'ra.rider_injury_description',//
                'ra.third_party_injury_description',//
                'ra.accident_attachments',//
                'ra.police_report',//
                'ra.status',//
                
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
            $query->whereIn('ra.id', $this->selectedIds);
        } else {
            if (!empty($this->city)) {
                $query->whereIn('vr.city_id', $this->city);
            }

            if (!empty($this->zone)) {
                $query->whereIn('vr.zone_id', $this->zone);
            }

            if ($this->from_date) {
                $query->whereDate('ra.created_at', '>=', $this->from_date);
            }

            if ($this->to_date) {
                $query->whereDate('ra.created_at', '<=', $this->to_date);
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
                $query->whereBetween(DB::raw('DATE(ra.created_at)'), [$from, $to]);
            }
        
        }

        return $query->orderBy('ra.id', 'desc');
    }
    
    public function chunkSize(): int
        {
            return 500; // or 1000, depends on your requirement
        }
        
    public function map($row): array
    {
        $this->sl++;

        $statusTextMap = [
            'claimed_initiated'         => 'Claimed Initiated',
            'insurer_visit_confirmed'   => 'Insurer Visit Confirmed',
            'inspection_completed'      => 'Inspection Completed',
            'approval_pending'          => 'Approval Pending',
            'repair_started'            => 'Repair Started',
            'repair_completed'          => 'Repair Completed',
            'invoice_submitted'         => 'Invoice Submitted',
            'payment_approved'          => 'Payment Approved',
            'claim_closed'              => 'Claim Closed (Settled)',
        ];

        $status = $statusTextMap[$row->status] ?? ($row->status ? ucfirst(str_replace('_', ' ', $row->status)) : '-');

        $createdAt = $row->created_at
            ? Carbon::parse($row->created_at)->format('d M Y h:i A')
            : '-';

        // ATTACHMENTS
        $accidentAttachments = '-';
        if (!empty($row->accident_attachments)) {
            $files = json_decode($row->accident_attachments, true);
            if (is_array($files) && count($files)) {
                $accidentAttachments = implode(', ', array_map(function ($file) {
                    return asset('public/b2b/accident_reports/attachments/' . $file);
                }, $files));
            }
        }

        // POLICE REPORT
        $policeReport = '-';
        if (!empty($row->police_report)) {
            $report = json_decode($row->police_report, true);
            if (!empty($report['name'])) {
                $policeReport = asset('public/b2b/accident_reports/police_reports/' . $report['name']);
            }
        }

        return [
            $this->sl,
            $row->req_id ?? '-',
            $row->accountability_type ?? '-',
            $row->vehicle_no ?? '-',
            $row->chassis_number ?? '-',
            $row->vehicle_id ?? '-',
            $row->vehicle_model ?? '-',
            $row->vehicle_make ?? '-',
            $row->vehicle_type ?? '-',
            $row->city ?? '-',
            $row->zone ?? '-',
            $row->created_by ?? '-',
            $row->rider_name ?? '-',
            $row->mobile_no ?? '-',
            $row->location_of_accident ?? '-',
            $row->accident_type ?? '-',
            $row->description ?? '-',
            $row->vehicle_damage ?? '-',
            $row->rider_injury_description ?? '-',
            $row->third_party_injury_description ?? '-',
            $accidentAttachments,
            $policeReport,
            $createdAt,
            $status
        ];
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
            'Vehicle Model',
            'Vehicle Make',
            'Vehicle Type',
            'City',
            'Zone',
            'Customer Name',
            'Rider Name',
            'Rider Number',
            'Location Of Accident',
            'Accident Type',
            'Description',
            'Vehicle Damage',
            'Rider Injury Description',
            'Third Party Injury Description',
            'Accident Attachments',
            'Police Report',
            'Created Date & Time',
            'Status',
        ];
    }
}
