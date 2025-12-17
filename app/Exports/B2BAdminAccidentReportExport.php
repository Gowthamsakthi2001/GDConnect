<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Modules\B2B\Entities\B2BReportAccident;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class B2BAdminAccidentReportExport implements FromQuery, WithHeadings, WithMapping,WithChunkReading
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

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = [], $zone = [], $status = [],$accountability_type = [],$customer_id=[], $vehicle_type=[] , $vehicle_model=[],$vehicle_make=[], $date_filter)
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
        $query = DB::table('b2b_tbl_report_accident as ar')
            ->join('b2b_tbl_vehicle_assignments as ass', 'ass.id', '=', 'ar.assign_id')
            ->join('b2b_tbl_vehicle_requests as vhr', 'vhr.req_id', '=', 'ass.req_id')
            ->leftJoin('ev_tbl_asset_master_vehicles as vh', 'vh.id', '=', 'ass.asset_vehicle_id')
            ->leftJoin('vehicle_qc_check_lists as qc', 'qc.id', '=', 'vh.qc_id')
            ->leftJoin('ev_tbl_vehicle_models as vm', 'vm.id', '=', 'qc.vehicle_model')
            ->leftJoin('vehicle_types as vt', 'vt.id', '=', 'qc.vehicle_type')
            ->leftJoin('ev_tbl_accountability_types as actype', 'actype.id', '=', 'vhr.account_ability_type')
            ->leftJoin('ev_tbl_city as cty', 'cty.id', '=', 'vhr.city_id')
            ->leftJoin('zones as zn', 'zn.id', '=', 'vhr.zone_id')
            ->leftJoin('b2b_tbl_riders as rider', 'rider.id', '=', 'ass.rider_id')
            ->leftJoin('ev_tbl_customer_logins as cml', 'cml.id', '=', 'rider.created_by')
            ->leftJoin('ev_tbl_customer_master as cm', 'cm.id', '=', 'cml.customer_id')
            ->where('vh.is_status', 'accepted')
            ->select([
                'vhr.req_id',
                'rider.name as rider_name',
                'rider.llr_number as llr_number',
                'rider.driving_license_number as driving_license_number',
                'actype.name as accountability_name',
                'vh.permanent_reg_number as permanent_reg_number',
                'qc.chassis_number as chassis_number',
                'vt.name as vehicle_type_name',
                'vm.vehicle_model as vehicle_model',
                'vm.make as vehicle_make',
                'rider.mobile_no as rider_mobile_no',
                'cml.email as created_by',
                'cm.trade_name as client_name',
                'cm.phone as client_phone',
                'cty.city_name',
                'zn.name as zone_name',
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
    
        if (!empty($this->selectedIds)) {
    
            $query->whereIn('ar.id', $this->selectedIds);
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
                $query->whereIn('ar.status', (array) $this->status);
            }
    
            if (!empty($this->date_filter)) {
    
                switch ($this->date_filter) {
    
                    case 'today':
                        $query->whereDate('ar.created_at', today());
                        break;
    
                    case 'week':
                        $query->whereBetween('ar.created_at', [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]);
                        break;
    
                    case 'last_15_days':
                        $query->whereBetween('ar.created_at', [
                            now()->subDays(14)->startOfDay(),
                            now()->endOfDay(),
                        ]);
                        break;
    
                    case 'month':
                        $query->whereMonth('ar.created_at', now()->month)
                            ->whereYear('ar.created_at', now()->year);
                        break;
    
                    case 'year':
                        $query->whereYear('ar.created_at', now()->year);
                        break;
                }
            }
    
            if (!empty($this->from_date)) {
                $query->whereDate('ar.created_at', '>=', $this->from_date);
            }
    
            if (!empty($this->to_date)) {
                $query->whereDate('ar.created_at', '<=', $this->to_date);
            }
        }
    
        return $query->orderBy('ar.id', 'desc');
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
                    $mapped[] = $row->rider_mobile_no ?? '-';
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
                    $attachments = json_decode($row->accident_attachments, true); 
                    if (is_array($attachments) && !empty($attachments)) {
                        $urls = [];
                        foreach ($attachments as $file) {
                            $urls[] = asset('b2b/accident_reports/attachments/' . $file);
                        }
                        $mapped[] = implode(", ", $urls); 
                    } else {
                        $mapped[] = '-';
                    }
                    break;
                
                case 'police_report':
                    $report = json_decode($row->police_report, true);
                    if (!empty($report['name'])) {
                        $mapped[] = asset('b2b/accident_reports/police_reports/' . $report['name']);
                    } else {
                        $mapped[] = '-';
                    }
                    break;
                    
                case 'description':
                    $mapped[] = $row->description ?? '-';
                    break;
                
                case 'aging':
                    $mapped[] = $aging ?? '-';
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
            'aging'         => 'Aging'
        ];

        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }
    public function chunkSize(): int
    {
        return 500;
    }
}
