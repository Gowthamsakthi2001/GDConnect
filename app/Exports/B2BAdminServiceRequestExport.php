<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Modules\B2B\Entities\B2BServiceRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class B2BAdminServiceRequestExport implements FromQuery,WithMapping,WithHeadings, WithChunkReading
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

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = [], $zone = [], $status =[] ,$accountability_type = [],$customer_id=[] , $vehicle_type=[] , $vehicle_model=[],$vehicle_make=[], $date_filter)
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
        $query = DB::table('b2b_tbl_service_request as vsr')
            ->leftJoin('b2b_tbl_vehicle_assignments as ass', 'vsr.assign_id', '=', 'ass.id')
            ->leftJoin('b2b_tbl_vehicle_requests as vhr', 'vhr.req_id', '=', 'ass.req_id')
            ->leftJoin('ev_tbl_asset_master_vehicles as vh', 'vh.id', '=', 'ass.asset_vehicle_id')
            ->leftJoin('vehicle_qc_check_lists as vqc', 'vqc.id', '=', 'vh.qc_id')
            ->leftJoin('ev_tbl_vehicle_models as vmd', 'vmd.id', '=', 'vqc.vehicle_model')
            ->leftJoin('ev_tbl_accountability_types as actype', 'actype.id', '=', 'vhr.account_ability_type')
            ->leftJoin('b2b_tbl_riders as rider', 'rider.id', '=', 'vhr.rider_id')
            ->leftJoin('ev_tbl_city as cty', 'cty.id', '=', 'vhr.city_id')
            ->leftJoin('zones as zn', 'zn.id', '=', 'vhr.zone_id')
            ->leftJoin('vehicle_types as vt', 'vt.id', '=', 'vqc.vehicle_type')
            ->leftJoin('ev_tbl_customer_logins as cml', 'cml.id', '=', 'vsr.created_by')
            ->leftJoin('ev_tbl_customer_master as cm', 'cm.id', '=', 'cml.customer_id')
            ->select([
                'vsr.*',
                'vhr.req_id as req_id',
                'actype.name as accountability_name',
                'rider.name as rider_name',
                'rider.mobile_no as rider_mobile',
                'cty.city_name as city_name',
                'zn.name as zone_name',
                'vt.name as vehicle_type_name',
                'cm.trade_name as client_name',
                'cm.id as client_id',
                'ass.asset_vehicle_id',
                'vh.permanent_reg_number',
                'vh.chassis_number',
                'vqc.vehicle_model',
                'vmd.vehicle_model as vehicle_model_name',
                'vmd.make as vehicle_make',
            ]);
    
        if (!empty($this->selectedIds)) {
            $query->whereIn('vsr.id', $this->selectedIds);
        }
        else {
    
            if (!empty($this->city) && !in_array('all', $this->city)) {
                $query->whereIn('vhr.city_id', $this->city);
            }
        
            if (!empty($this->zone) && !in_array('all', $this->zone)) {
                $query->whereIn('vhr.zone_id', $this->zone);
            }
            if (!empty($this->vehicle_model) && !in_array('all',$this->vehicle_model)) {
                $query->whereIn('vqc.vehicle_model', $this->vehicle_model);
            }
        
            if (!empty($this->vehicle_type) && !in_array('all', $this->vehicle_type)) {
                $query->whereIn('vqc.vehicle_type', $this->vehicle_type);
            }
            
            if (!empty($this->vehicle_make) && !in_array('all', $this->vehicle_make)) {
                $query->whereIn('vmd.make', $this->vehicle_make);
            }
        
            if (!empty($this->status) && !in_array('all', $this->status)) {
                $query->whereIn('vsr.status', (array)$this->status);
            }
        
            if (!empty($this->accountability_type) && !in_array('all', $this->accountability_type)) {
                $query->whereIn('vhr.account_ability_type', $this->accountability_type);
            }
        
            if (!empty($this->customer_id) && !in_array('all', $this->customer_id)) {
                $query->whereIn('cm.id', $this->customer_id);
            }
            
            if (!empty($this->date_filter)) {
                switch ($this->date_filter) {
            
                    case 'today':
                        $query->whereDate('vsr.created_at', today());
                        break;
            
                    case 'week':
                        $query->whereBetween('vsr.created_at', [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]);
                        break;
    
                    case 'last_15_days':
                        $query->whereBetween('vsr.created_at', [now()->subDays(14)->startOfDay(),now()->endOfDay()]);
                        break;
                    case 'month':
                        $query->whereMonth('vsr.created_at', now()->month)
                              ->whereYear('vsr.created_at', now()->year);
                        break;
            
                    case 'year':
                        $query->whereYear('vsr.created_at', now()->year);
                        break;
                }
            }
        
            if (!empty($this->from_date)) {
                $query->whereDate('vsr.created_at', '>=', $this->from_date);
            }
        
            if (!empty($this->to_date)) {
                $query->whereDate('vsr.created_at', '<=', $this->to_date);
            }
        }
    
        return $query
            ->orderBy('vsr.id', 'desc'); 
    }

    
    public function map($row): array
    {
        $mapped = [];
        if ($row->status === 'closed') {
            $created   = \Carbon\Carbon::parse($row->created_at);
            $completed = \Carbon\Carbon::parse($row->updated_at);
        } else {
            $created   = \Carbon\Carbon::parse($row->created_at);
            $completed = now();
        }
    
        $diffInDays    = $created->diffInDays($completed);
        $diffInHours   = $created->diffInHours($completed);
        $diffInMinutes = $created->diffInMinutes($completed);
    
        if ($diffInDays > 0) {
            $aging = $diffInDays . ' days';
        } elseif ($diffInHours > 0) {
            $aging = $diffInHours . ' hours';
        } else {
            $aging = $diffInMinutes . ' mins';
        }
    
        foreach ($this->selectedFields as $key) {
            switch ($key) {
    
                case 'req_id':
                    $mapped[] = $row->req_id ?? '-';
                    break;
    
                case 'ticket_id':
                    $mapped[] = $row->ticket_id ?? '-';
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
                    $mapped[] = $row->vehicle_model_name ?? '-';
                    break;
    
                case 'vehicle_make':
                    $mapped[] = $row->vehicle_make ?? '-';
                    break;
    
                case 'mobile_no':
                    $mapped[] = $row->rider_mobile ?? '-';
                    break;
    
                case 'client':
                    $mapped[] = $row->client_name ?? '-';
                    break;
    
                case 'city':
                    $mapped[] = $row->city_name ?? '-';
                    break;
    
                case 'zone':
                    $mapped[] = $row->zone_name ?? '-';
                    break;
    
                case 'repair_type':
                    $mapped[] = $row->repair_type == 1
                        ? 'Breakdown Repair'
                        : ($row->repair_type == 2 ? 'Running Repair' : '-');
                    break;
    
                case 'location':
                    $mapped[] = $row->gps_pin_address ?? '-';
                    break;
    
                case 'created_by':
                    $mapped[] = ucfirst($row->type ?? '-');
                    break;
    
                case 'status':
                    $mapped[] = ucfirst($row->status ?? '-');
                    break;
    
                case 'created_at':
                    $mapped[] = $row->created_at
                        ? \Carbon\Carbon::parse($row->created_at)->format('d M Y h:i A')
                        : '-';
                    break;
    
                case 'updated_at':
                    $mapped[] = $row->updated_at
                        ? \Carbon\Carbon::parse($row->updated_at)->format('d M Y h:i A')
                        : '-';
                    break;
    
                case 'aging':
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
        $headers = [];

        $customHeadings = [
            'req_id'        => 'Request ID',
            'ticket_id'     => 'Ticket ID',
            'accountability_type'    => 'Accountablity Type',
            'vehicle_no'    => 'Vehicle Number',
            'chassis_number'=> 'Chassis Number',
            'vehicle_type'    => 'Vehicle Type',
            'vehicle_model'    => 'Vehicle Model',
            'vehicle_make'    => 'Vehicle Make',
            'rider_name'    => 'Rider Name',
            'mobile_no'     => 'Mobile No',
            'client'        => 'Client Name',
            'city'          => 'City',
            'zone'          => 'Zone',
            'poc_name'      => 'POC Name',
            'poc_number'    => 'POC Number',
            'description'   => 'Description',
            'repair_type'   => 'Repair Type',
            'location'      => 'Location',
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
        return 1000; 
    }
}

