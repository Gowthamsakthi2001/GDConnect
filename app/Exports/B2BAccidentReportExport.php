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

class B2BAccidentReportExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $from_date;
    protected $to_date;
    protected $datefilter;
    protected $selectedIds;
    protected $selectedFields;
    protected $city = [];
    protected $zone = [];
    protected $accountability_type = [];
    protected $status = [];
    protected $vehicle_model = [];
    protected $vehicle_make = [];
    protected $vehicle_type = [];
    public function __construct($from_date, $to_date,$datefilter, $selectedIds = [], $selectedFields = [], $city = [], $zone = [], $accountability_type=[],$status = [],$vehicle_model =[],$vehicle_type=[],$vehicle_make =[])
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->datefilter        = $datefilter;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = (array)$city;
        $this->zone           = (array)$zone;
        $this->accountability_type           = (array)$accountability_type;
        $this->status           = (array)$status;
        $this->vehicle_model           = (array)$vehicle_model;
        $this->vehicle_type           = (array)$vehicle_type;
        $this->vehicle_make           = (array)$vehicle_make;
    }

    public function query()
    {
        $guard = Auth::guard('master')->check() ? 'master' : 'zone';
        $user = Auth::guard($guard)->user();

        $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');

        $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
            ->where('city_id', $user->city_id)
            ->pluck('id');

        $query = DB::table('b2b_tbl_report_accident as ar')
            ->join('b2b_tbl_vehicle_assignments as va', 'ar.assign_id', '=', 'va.id')
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
            ->select([
                'vr.req_id as req_id',
                'r.name as rider_name',
                'r.llr_number as llr_number',
                'r.driving_license_number as driving_license_number',
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
                'ar.accident_type as accident_type',
                'ar.location_of_accident as location_of_accident',
                'ar.status as status',
                'ar.vehicle_damage as vehicle_damage',
                'ar.rider_injury_description',
                'ar.accident_attachments',
                'ar.police_report',
                'ar.third_party_injury_description',
                'ar.description',
                'ar.created_at as created_at',
                'ar.updated_at as updated_at'
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
            $query->whereIn('ar.id', $this->selectedIds);
        } else {
            if (!empty($this->city)) {
                $query->whereIn('vr.city_id', $this->city);
            }

            if (!empty($this->zone)) {
                $query->whereIn('vr.zone_id', $this->zone);
            }

            if ($this->from_date) {
                $query->whereDate('ar.created_at', '>=', $this->from_date);
            }

            if ($this->to_date) {
                $query->whereDate('ar.created_at', '<=', $this->to_date);
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
                    $query->whereBetween('ar.created_at', [$from, $to]);
                }
            }
        }

        return $query->orderBy('ar.id', 'desc');
    }
    
    public function chunkSize(): int
    {
        return 500;
    }

    public function map($row): array
    {
        $mapped = [];
        
        if ($row->status === 'claim_closed') {
            $aging = \Carbon\Carbon::parse($row->created_at)
                        ->diffForHumans(\Carbon\Carbon::parse($row->updated_at), true);
        } else {
            $aging = \Carbon\Carbon::parse($row->created_at)
                        ->diffForHumans(now(), true);
        }

        foreach ($this->selectedFields as $key) {
            switch ($key) {
                case 'req_id':
                    $mapped[] = $row->req_id ?? '-';
                    break;

                case 'rider_name':
                    $mapped[] = $row->rider_name ?? '-';
                    
                    break;

                case 'accountability_type':
                        $mapped[] = $row->accountability_type ?? '-';
                        break;
                        
                case 'vehicle_no':
                    $mapped[] = $row->vehicle_no ?? '-';
                    break;

                case 'chassis_number':
                    $mapped[] = $row->chassis_number ?? '-';
                    break;
                
                case 'vehicle_type':
                    $mapped[] = $row->vehicle_type ?? '-';
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
                case 'aging':
                    $mapped[] = $aging ?? '-';
                    break;
                // case 'poc_name':
                //     $mapped[] = $row->assignment->rider->customerlogin->customer_relation->trade_name ?? '-';
                //     break;
                
                // case 'poc_number':
                //     $mapped[] = $row->assignment->rider->customerlogin->customer_relation->phone ?? '-';
                //     break;
                    
                case 'city':
                    $mapped[] = $row->city ?? '-';
                    break;

                case 'zone':
                    $mapped[] = $row->zone ?? '-';
                    break;

                case 'accident_type':
                    $mapped[] = $row->accident_type ?? '-';
                    break;
                
                case 'location':
                    $mapped[] = $row->location_of_accident ?? '-';
                    break;
                
                case 'rider_llr_number':
                    $mapped[] = $row->llr_number ?? '-';
                    break;
                    
                case 'rider_license_number':
                    $mapped[] = $row->driving_license_number ?? '-';
                    break;
                case 'vehicle_damage':
                    $mapped[] = $row->vehicle_damage ?? '-';
                    break;
                
                case 'rider_injury_description':
                    $mapped[] = $row->rider_injury_description ?? '-';
                    break;
                    
                case 'third_party_injury_description':
                    $mapped[] = $row->third_party_injury_description ?? '-';
                    break;
                
                case 'accident_attachments':
                    $attachments = json_decode($row->accident_attachments, true); // decode JSON
                    if (is_array($attachments) && !empty($attachments)) {
                        $urls = [];
                        foreach ($attachments as $file) {
                            $urls[] = asset('b2b/accident_reports/attachments/' . $file);
                        }
                        $mapped[] = implode(", ", $urls); // join multiple URLs
                    } else {
                        $mapped[] = '-';
                    }
                    break;
                
                case 'police_report':
                    $report = json_decode($row->police_report, true); // decode JSON
                    if (!empty($report['name'])) {
                        $mapped[] = asset('b2b/accident_reports/police_reports/' . $report['name']);
                    } else {
                        $mapped[] = '-';
                    }
                    break;
                    
                case 'description':
                    $mapped[] = $row->description ?? '-';
                    break;
                
                case 'created_by':
                    $mapped[] = $row->created_by ?? '-';
                    break;

                case 'status':
                    $mapped[] = ucfirst($row->status ?? '-');
                    break;

                case 'created_at':
                    $mapped[] = $row->created_at
                        ? Carbon::parse($row->created_at)->format('d M Y h:i A')
                        : '-';
                    break;
                
                case 'updated_at':
                    $mapped[] = $row->updated_at
                        ? Carbon::parse($row->updated_at)->format('d M Y h:i A')
                        : '-';
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
            'vehicle_no'    => 'Vehicle Number',
            'chassis_number'=> 'Chassis Number',
            'vehicle_model' => 'Vehicle Model',
            'vehicle_make'  => 'Vehicle Make',
            'vehicle_type'  => 'Vehicle Type',
            'rider_name'    => 'Rider Name',
            'mobile_no'     => 'Mobile No',
            'city'          => 'City',
            // 'poc_name'      => 'POC Name',
            // 'poc_number'   => 'POC Contact',
            'accountability_type'     => 'Accountability Type',
            'zone'          => 'Zone',
            'accident_type' => 'Accident Type',
            'location'      => 'Location',
            'rider_license_number' => 'Rider License Number',
            'rider_llr_number' => 'Rider llr Number',
            'vehicle_damage' => 'Vehicle Damage',
            'rider_injury_description' => 'Rider Injury Description',
            'third_party_injury_description' => 'Third Party Injury Description',
            'accident_attachments' => 'Accident Attachments',
            'police_report' => 'Police Report',
            'description'   => 'Description',
            'created_by'    => 'Created By',
            'status'        => 'Status',
            'created_at'    => 'Created Date & Time',
            'updated_at'    => 'Updated Date & Time',
            'aging'         => 'aging'
        ];

        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }
}
