<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Modules\B2B\Entities\B2BVehicleRequests; 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class B2BAdminDeploymentRequestExport implements FromQuery,WithMapping,WithHeadings, WithChunkReading
{
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;
    protected $city = [];
    protected $zone = [];
    protected $status = [];
    protected $accountability_type = [];
    protected $customer_id = [];
    protected $vehicle_type = []; 
    protected $datefilter = []; 

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = [], $zone = [], $status = [],$accountability_type = [],$customer_id=[] , $vehicle_type=[] , $datefilter=[]) //updated by logesh
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = (array) $city;
        $this->zone           = (array) $zone;
        $this->status         = (array) $status;
        $this->accountability_type = (array) $accountability_type; 
        $this->customer_id = (array) $customer_id; 
        $this->datefilter = $datefilter; 
        $this->vehicle_type =(array) $vehicle_type;
    }
    
    public function query()
    {
        $query = \DB::table('b2b_tbl_vehicle_requests as vhr')
            ->leftJoin('ev_tbl_accountability_types as actype', 'actype.id', '=', 'vhr.account_ability_type')
            ->leftJoin('b2b_tbl_riders as rider', 'rider.id', '=', 'vhr.rider_id')
            ->leftJoin('ev_tbl_city as cty', 'cty.id', '=', 'vhr.city_id')
            ->leftJoin('zones as zn', 'zn.id', '=', 'vhr.zone_id')
            ->leftJoin('vehicle_types as vt', 'vt.id', '=', 'vhr.vehicle_type')
            ->leftJoin('ev_tbl_customer_logins as cml', 'cml.id', '=', 'vhr.created_by')
            ->leftJoin('ev_tbl_customer_master as cm', 'cm.id', '=', 'cml.customer_id')
            ->select([
                'vhr.*',
                'actype.name as accountability_name',
                'rider.name as rider_name',
                'rider.mobile_no as rider_mobile',
                'cty.city_name as city_name',
                'zn.name as zone_name',
                'vt.name as vehicle_type_name',
                'cm.trade_name as client_name',
                'cm.id as client_id'
            ]);
    
        if (!empty($this->selectedIds)) {
            $query->whereIn('vhr.id', $this->selectedIds);
        } else {
            if (!empty($this->city) && !in_array('all', $this->city)) {
                $query->whereIn('vhr.city_id', $this->city);
            }
    
            if (!empty($this->zone) && !in_array('all', $this->zone)) {
                $query->whereIn('vhr.zone_id', $this->zone);
            }
            if (!empty($this->status) && !in_array('all', $this->status)) {
                $query->whereIn('vhr.status', $this->status);
            }
            if (!empty($this->vehicle_type) && !in_array('all', $this->vehicle_type)) {
                $query->whereIn('vhr.vehicle_type', $this->vehicle_type);
            }
            if (!empty($this->accountability_type) && !in_array('all', $this->accountability_type)) {
                $query->whereIn('vhr.account_ability_type', $this->accountability_type);
            }
            if (!empty($this->customer_id) && !in_array('all', $this->customer_id)) {
                $query->whereIn('cm.id', $this->customer_id);
            }
            if (!empty($this->datefilter)) {
                switch ($this->datefilter) {
                    case 'today':
                        $query->whereDate('vhr.created_at', today());
                        break;
    
                    case 'week':
                        $query->whereBetween('vhr.created_at', [ now()->startOfWeek(), now()->endOfWeek() ]);
                        break;
    
                    case 'last_15_days':
                        $query->whereBetween('vhr.created_at', [ now()->subDays(14)->startOfDay(), now()->endOfDay() ]);
                        break;
    
                    case 'month':
                        $query->whereBetween('vhr.created_at', [ now()->startOfMonth(), now()->endOfMonth() ]);
                        break;
    
                    case 'year':
                        $query->whereBetween('vhr.created_at', [ now()->startOfYear(), now()->endOfYear() ]);
                        break;
    
                    case 'custom':
                        break;
                }
            }
            if (!empty($this->from_date) && !empty($this->to_date)) {
                $query->whereBetween('vhr.created_at', [$this->from_date, $this->to_date]);
            } else {
                if (!empty($this->from_date)) {
                    $query->whereDate('vhr.created_at', '>=', $this->from_date);
                }
                if (!empty($this->to_date)) {
                    $query->whereDate('vhr.created_at', '<=', $this->to_date);
                }
            }
        }
    
        return $query->orderBy('vhr.id', 'desc');
    }

    public function map($row): array
    {
        $mapped = [];

        foreach ($this->selectedFields as $key) {
            switch ($key) {
                case 'req_id':
                    $mapped[] = $row->req_id ?? '-';
                    break;
                case 'accountability_type':
                    $mapped[] = $row->accountability_name  ?? '-';
                    break;
                    
                case 'rider_name':
                    $mapped[] = $row->rider_name ?? '-';
                    break;

                case 'mobile_no':
                    $mapped[] = $row->rider_mobile ?? '-';
                    break;

                case 'client':
                    $mapped[] = $row->client_name  ?? '-';
                    break;

                case 'city':
                    $mapped[] = $row->city_name ?? '-';
                    break;

                case 'zone':
                    $mapped[] = $row->zone_name ?? '-';
                    break;

                case 'from_date':
                    $mapped[] = $row->start_date ?? '-';
                    break;

                case 'end_date':
                    $mapped[] = $row->end_date ?? '-';
                    break;

                case 'vehicle_type':
                    $mapped[] = $row->vehicle_type_name ?? '-';
                    break;

                case 'battery_type':
                    if ($row->battery_type == 1) {
                        $mapped[] = 'Removable';
                    } elseif ($row->battery_type == 2) {
                        $mapped[] = 'Non Removable';
                    } else {
                        $mapped[] = '-';
                    }
                    break;

                case 'status':
                    if ($row->status == 'pending') {
                        $mapped[] = 'Opened';
                    } elseif ($row->status == 'completed') {
                        $mapped[] = 'Closed';
                    } else {
                        $mapped[] = ucfirst($row->status) ?? '-';
                    }
                    break;

                case 'aging':
                    if ($row->status === 'completed' && $row->completed_at) {
                        $aging = Carbon::parse($row->created_at)->diffForHumans(Carbon::parse($row->completed_at), true);
                    } else {
                        $aging = Carbon::parse($row->created_at)->diffForHumans(now(), true);
                    }
                    $mapped[] = $aging ?? '-';
                    break;

                case 'created_at':
                    $mapped[] = $row->created_at ? Carbon::parse($row->created_at)->format('d M Y, h:i A') : '-';
                    break;

                case 'updated_at':
                    $mapped[] = $row->updated_at ? Carbon::parse($row->updated_at)->format('d M Y, h:i A') : '-';
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
            'rider_name'    => 'Rider Name',
            'mobile_no'     => 'Contact Details',
            'client'        => 'Client Name',
            'city'          => 'City',
            'zone'          => 'Zone',
            'from_date'     => 'Vehicle Duration From Date',
            'end_date'      => 'Vehicle Duration End Date',
            'vehicle_type'  => 'Vehicle Type',
            'battery_type'  => 'Battery Type', 
            'status'        => 'Status',
            'aging'         => 'Aging',
            'created_at'    => 'Created Date & Time',
            'updated_at'    => 'Updated Date & Time',
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
