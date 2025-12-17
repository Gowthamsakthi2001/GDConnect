<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\MasterManagement\Entities\CustomerLogin;

class B2BReturnedListExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $from_date;
    protected $to_date;
    protected $datefilter;
    protected $selectedIds;
    protected $selectedFields;
    protected $city;
    protected $zone;
    protected $accountability_type;
    protected $status;
    protected $vehicle_model;
    protected $vehicle_make;
    protected $vehicle_type;

    public function __construct(
        $from_date,
        $to_date,
        $datefilter,
        $selectedIds = [],
        $selectedFields = [],
        $city = [],
        $zone = [],
        $accountability_type = [],
        $status = [],
        $vehicle_model = [],
        $vehicle_type = [],
        $vehicle_make = []
    ) {
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->datefilter = $datefilter;
        $this->selectedIds = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city = (array)$city;
        $this->zone = (array)$zone;
        $this->accountability_type = (array)$accountability_type;
        $this->status = (array)$status;
        $this->vehicle_model = (array)$vehicle_model;
        $this->vehicle_type = (array)$vehicle_type;
        $this->vehicle_make = (array)$vehicle_make;
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
            ->leftJoin('ev_tbl_vehicle_models as vm', 'v.model', '=', 'vm.id')
            ->leftJoin('vehicle_types as vt', 'v.vehicle_type', '=', 'vt.id')
            ->leftJoin('ev_tbl_customer_master as cr', 'cl.customer_id', '=', 'cr.id')
            ->leftJoin('users as agent', 'agent.id', '=', 'rr.closed_by')
            ->select([
                'vr.req_id as req_id',
                'r.name as rider_name',
                'actype.name as accountability_type',
                'v.permanent_reg_number as vehicle_no',
                'v.chassis_number as chassis_number',
                'vt.name as vehicle_type',
                'vm.vehicle_model as vehicle_model',
                'vm.make as vehicle_make',
                'r.mobile_no as mobile_no',
                'cl.email as created_by',
                'cr.trade_name as poc_name',
                'cr.phone as poc_number',
                'c.city_name as city',
                'z.name as zone',
                'rr.return_reason as reason',
                'rr.description as description',
                'rr.kilometer_value as odometer_value',
                'rr.kilometer_image as odometer_image',
                'rr.vehicle_front as vehicle_front',
                'rr.vehicle_back as vehicle_back',
                'rr.vehicle_top as vehicle_top',
                'rr.vehicle_bottom as vehicle_bottom',
                'rr.vehicle_left as vehicle_left',
                'rr.vehicle_right as vehicle_right',
                'rr.vehicle_battery as vehicle_battery',
                'rr.vehicle_charger as vehicle_charger',
                'rr.status as status',
                'rr.created_at as created_at',
                'agent.name as closed_by'
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
            
            $from='';
            $to = '';
            // DATE FILTERS
            if (!empty($this->datefilter)) {
                switch ($this->datefilter) {
                    case 'today':
                        $from = Carbon::today();
                        $to = Carbon::today();
                        break;

                    case 'week':
                        $from = Carbon::now()->startOfWeek();
                        $to = Carbon::now()->endOfWeek();
                        break;

                    case 'last_15_days':
                        $from = Carbon::now()->subDays(14)->startOfDay();
                        $to = Carbon::now()->endOfDay();
                        break;

                    case 'month':
                        $from = Carbon::now()->startOfMonth();
                        $to = Carbon::now()->endOfMonth();
                        break;

                    case 'year':
                        $from = Carbon::now()->startOfYear();
                        $to = Carbon::now()->endOfYear();
                        break;
                }

                if ($from && $to) {
                    $query->whereBetween('rr.created_at', [$from, $to]);
                }
            }
        }

        return $query->orderBy('rr.id', 'desc');
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function map($row): array
    {
        $mapped = [];
        
        foreach ($this->selectedFields as $key) {
            switch($key){
                case 'vehicle_front': 
                    $mapped[] = $row->vehicle_front ?asset('b2b/vehicle_front/' . $row->vehicle_front): '-';
                    break;
                case 'vehicle_back': $mapped[] = $row->vehicle_back ?asset('b2b/vehicle_back/' . $row->vehicle_back): '-'; break;
                
                case 'vehicle_top': $mapped[] = $row->vehicle_top ?asset('b2b/vehicle_top/' . $row->vehicle_top): '-'; break;
                
                case 'vehicle_bottom': $mapped[] = $row->vehicle_bottom ?asset('b2b/vehicle_bottom' . $row->vehicle_bottom): '-'; break;
                
                case 'vehicle_left': $mapped[] = $row->vehicle_left ?asset('b2b/vehicle_left/' . $row->vehicle_left): '-'; break;
                
                case 'vehicle_right': $mapped[] = $row->vehicle_right ?asset('b2b/vehicle_right/' . $row->vehicle_right): '-'; break;
                
                case 'vehicle_battery': $mapped[] = $row->vehicle_battery ?asset('b2b/vehicle_battery/' . $row->vehicle_battery): '-'; break;
                
                case 'vehicle_charger': $mapped[] = $row->vehicle_charger ?asset('b2b/vehicle_charger/' . $row->vehicle_charger): '-'; break;
                
                default:
                   $mapped[] = $row->$key ?? '-'; 
            }
            
        }

        return $mapped;
    }

    public function headings(): array
    {
        return array_map(fn ($key) =>
            ucwords(str_replace('_', ' ', $key)),
            $this->selectedFields
        );
    }
}
