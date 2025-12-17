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

class B2BServiceRequestExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
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

        $query = DB::table('b2b_tbl_service_request as sr')
            ->join('b2b_tbl_vehicle_assignments as va', 'sr.assign_id', '=', 'va.id')
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
            ->leftJoin('ev_tbl_repair_types as rt', 'rt.id', '=', 'sr.repair_type')
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
                'sr.ticket_id as ticket_id',
                'sr.description as description',
                'rt.name as repair_type',
                'sr.status as status',
                'sr.created_at as created_at',
                'sr.updated_at as updated_at',
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
            $query->whereIn('sr.id', $this->selectedIds);
        } else {
            if (!empty($this->city)) {
                $query->whereIn('vr.city_id', $this->city);
            }

            if (!empty($this->zone)) {
                $query->whereIn('vr.zone_id', $this->zone);
            }

            if ($this->from_date) {
                $query->whereDate('sr.created_at', '>=', $this->from_date);
            }

            if ($this->to_date) {
                $query->whereDate('sr.created_at', '<=', $this->to_date);
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

                    default:
                        $from = null;
                        $to = null;
                }

                if ($from && $to) {
                    $query->whereBetween('sr.created_at', [$from, $to]);
                }
            }
        }

        return $query->orderBy('sr.id', 'desc');
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
                case 'created_at': 
                    $mapped[] = $row->created_at 

                        ? \Carbon\Carbon::parse($row->created_at)->format('d M Y') 

                        : 'N/A';

                    break;
                
                case 'updated_at': 
                    $mapped[] = $row->updated_at 

                        ? \Carbon\Carbon::parse($row->updated_at)->format('d M Y') 

                        : 'N/A';

                    break;
                
                case 'aging': 
                    if ($row->status === 'closed') {
                            $created   = \Carbon\Carbon::parse($row->created_at);
                            $completed = \Carbon\Carbon::parse($row->updated_at);
                            $diffInDays = $created->diffInDays($completed);
                            $diffInHours = $created->diffInHours($completed);
                            $diffInMinutes = $created->diffInMinutes($completed);
                        
                            if ($diffInDays > 0) {
                                $aging = $diffInDays . ' days';
                            } elseif ($diffInHours > 0) {
                                $aging = $diffInHours . ' hours';
                            } else {
                                $aging = $diffInMinutes . ' mins';
                            }
                        } else {
                            $created   = \Carbon\Carbon::parse($row->created_at);
                            $now       = now();
                            $diffInDays = $created->diffInDays($now);
                            $diffInHours = $created->diffInHours($now);
                            $diffInMinutes = $created->diffInMinutes($now);
                        
                            if ($diffInDays > 0) {
                                $aging = $diffInDays . ' days';
                            } elseif ($diffInHours > 0) {
                                $aging = $diffInHours . ' hours';
                            } else {
                                $aging = $diffInMinutes . ' mins';
                            }
                        }
                    $mapped[] = $aging ?? '-'; 
                    break;
                    
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
