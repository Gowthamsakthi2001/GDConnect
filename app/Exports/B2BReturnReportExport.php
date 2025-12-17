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

class B2BReturnReportExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
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
        $accountability_type = [],
        $status = [],
        $vehicle_model = [],
        $vehicle_make = []
    ) {
        $this->date_range  = $date_range;
        $this->from_date   = $from_date;
        $this->to_date     = $to_date;

        // Always convert to array safely
        $this->vehicle_type = (array) $vehicle_type;
        $this->vehicle_model = (array) $vehicle_model;
        $this->vehicle_make  = (array) $vehicle_make;
        $this->city          = (array) $city;
        $this->zone          = (array) $zone;
        $this->vehicle_no    = (array) $vehicle_no;

        $this->accountability_type = (array) $accountability_type;

        // Status can arrive as ["running,returned"]
        if (!empty($status) && is_string($status)) {
            $this->status = explode(',', $status);
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

        $query = DB::table('b2b_tbl_return_request as rr')
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
            ->leftJoin('users as agent', 'agent.id', '=', 'rr.closed_by')
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
                'agent.name as closed_by',//
                'rr.created_at as created_at',//
                'rr.closed_at as closed_at',//
                'rr.odometer_value',//
                'rr.odometer_image',//
                'rr.vehicle_front',//
                'rr.vehicle_back',//
                'rr.vehicle_top',//
                'rr.vehicle_left',//
                'rr.vehicle_right',//
                'rr.vehicle_battery',//
                'rr.vehicle_charger',//
                
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
    
     public function chunkSize(): int
    {
        return 500; // optimal chunk size
    }
    
    public function map($row): array
    {
        $this->sl++;

        $createdAt = $row->created_at
            ? Carbon::parse($row->created_at)->format('d M Y h:i A')
            : '-';

        $closedAt = $row->closed_at
            ? Carbon::parse($row->closed_at)->format('d M Y h:i A')
            : '-';

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
            $row->closed_by ?? '-',
            $row->odometer_value ?? '-',
            $row->odometer_image ? asset('public/b2b/odometer_images/' . $row->odometer_image) : '-',
            $row->vehicle_front ? asset('public/b2b/vehicle_front/' . $row->vehicle_front) : '-',
            $row->vehicle_back ? asset('public/b2b/vehicle_back/' . $row->vehicle_back) : '-',
            $row->vehicle_top ? asset('public/b2b/vehicle_top/' . $row->vehicle_top) : '-',
            $row->vehicle_left ? asset('public/b2b/vehicle_left/' . $row->vehicle_left) : '-',
            $row->vehicle_right ? asset('public/b2b/vehicle_right/' . $row->vehicle_right) : '-',
            $row->vehicle_battery ? asset('public/b2b/vehicle_battery/' . $row->vehicle_battery) : '-',
            $row->vehicle_charger ? asset('public/b2b/vehicle_charger/' . $row->vehicle_charger) : '-',
            $createdAt,
            $closedAt,
        ];
    }

    public function headings(): array
    {
        return [
            'SL NO',
            'Request ID',
            'Accountability Type',
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
            'Agent Name',
            'Odometer Value',
            'Odometer Image',
            'Vehicle Front Image',
            'Vehicle Back Image',
            'Vehicle Top Image',
            'Vehicle Left Image',
            'Vehicle Right Image',
            'Vehicle Battery Image',
            'Vehicle Charger Image',
            'Created Date & Time',
            'Completed Date & Time',
        ];
    }
}
