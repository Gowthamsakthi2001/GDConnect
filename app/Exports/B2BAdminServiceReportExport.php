<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\B2B\Entities\B2BServiceRequest;
use Modules\MasterManagement\Entities\CustomerLogin;
use Carbon\Carbon;

class B2BAdminServiceReportExport implements FromQuery,WithMapping,WithHeadings, WithChunkReading
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

    public function __construct($date_range, $from_date, $to_date, $vehicle_type=[],$vehicle_model=[],$vehicle_make=[], $city=[], $zone=[], $vehicle_no = [] , $accountability_type=[] , $customer_id =[], $status=[])
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
        $query = DB::table('b2b_tbl_service_request as vsr')
            ->leftJoin('b2b_tbl_vehicle_assignments as ass', 'vsr.assign_id', '=', 'ass.id')
            ->leftJoin('b2b_tbl_vehicle_requests as vhr', 'vhr.req_id', '=', 'ass.req_id')
            ->leftJoin('ev_tbl_asset_master_vehicles as vh', 'vh.id', '=', 'ass.asset_vehicle_id')
            ->leftJoin('vehicle_qc_check_lists as vqc', 'vqc.id', '=', 'vh.qc_id')
            ->leftJoin('ev_tbl_vehicle_models as vmd', 'vmd.id', '=', 'vqc.vehicle_model')
            ->leftJoin('vehicle_types as vt', 'vt.id', '=', 'vqc.vehicle_type')
            ->leftJoin('ev_tbl_accountability_types as actype', 'actype.id', '=', 'vhr.account_ability_type')
            ->leftJoin('b2b_tbl_riders as rider', 'rider.id', '=', 'vhr.rider_id')
            ->leftJoin('ev_tbl_city as cty', 'cty.id', '=', 'vhr.city_id')
            ->leftJoin('zones as zn', 'zn.id', '=', 'vhr.zone_id')
            ->leftJoin('ev_tbl_customer_logins as cml', 'cml.id', '=', 'vsr.created_by')
            ->leftJoin('ev_tbl_customer_master as cm', 'cm.id', '=', 'cml.customer_id')
    
            ->select([
                'vsr.ticket_id as ticket_id',
                'actype.name as accountability_name',
                'vh.permanent_reg_number',
                'vh.chassis_number',
                'vh.id as vehicle_id',
                'vmd.vehicle_model as vehicle_model_name',
                'vmd.make as vehicle_make',
                'vt.name as vehicle_type_name',
                'cty.city_name',
                'zn.name as zone_name',
                'cm.trade_name as client_name',
                'rider.name as rider_name',
                'rider.mobile_no as rider_mobile',
                'vsr.created_at',
                'vsr.status',
                'vsr.current_status',
                'cm.id as client_id',
                'ass.asset_vehicle_id',
            ]);
    
        if (!empty($this->selectedIds)) {
            $query->whereIn('vsr.id', $this->selectedIds);
    
        } else {
    
            if (!empty($this->city) && !in_array('all', $this->city)) {
                $query->whereIn('vhr.city_id', $this->city);
            }

            if (!empty($this->zone) && !in_array('all', $this->zone)) {
                $query->whereIn('vhr.zone_id', $this->zone);
            }
    
            if (!empty($this->vehicle_model) && !in_array('all', $this->vehicle_model)) {
                $query->whereIn('vqc.vehicle_model', $this->vehicle_model);
            }
    
            if (!empty($this->vehicle_type) && !in_array('all', $this->vehicle_type)) {
                $query->whereIn('vqc.vehicle_type', $this->vehicle_type);
            }
    
            if (!empty($this->vehicle_make) && !in_array('all', $this->vehicle_make)) {
                $query->whereIn('vmd.make', $this->vehicle_make);
            }

            if (!empty($this->accountability_type) && !in_array('all', $this->accountability_type)) {
                $query->whereIn('vhr.account_ability_type', (array) $this->accountability_type);
            }
    
            if (!empty($this->customer_id) && !in_array('all', $this->customer_id)) {
                $query->whereIn('cm.id', $this->customer_id);
            }

            if (!empty($this->status) && !in_array('all', $this->status)) {
                $query->whereIn('vsr.status', (array) $this->status);
            }
    
            if (!empty($this->vehicle_no)) {
                $query->whereIn('vh.id', (array) $this->vehicle_no);
            }

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
    
            if (!empty($from) && !empty($to)) {
                $query->whereBetween(DB::raw('DATE(vsr.created_at)'), [$from, $to]);
            }
        }
    
        return $query->orderByDesc('vsr.id');
    }

    public function map($row): array
    {
        $this->sl++;
        $currentStatus = $row->current_status ?? '-';
        $ticketStatus  = $row->status ?? '-';

        return [
            $this->sl,
            $row->ticket_id ?? '-',
            $row->accountability_name ?? '-',
            $row->permanent_reg_number ?? '-',
            $row->chassis_number ?? '-',
            $row->vehicle_id ?? '-',
            $row->vehicle_make ?? '-',
            $row->vehicle_model_name ?? '-',
            $row->vehicle_type_name ?? '-',
            $row->city_name ?? '-',
            $row->zone_name ?? '-',
            $row->client_name ?? '-',
            $row->rider_name ?? '-',
            $row->rider_mobile ?? '-',
            $row->created_at ? Carbon::parse($row->created_at)->format('d M Y h:i A') : '-',
            ucfirst(str_replace('_', ' ', $currentStatus)),
            ucfirst(str_replace('_', ' ', $ticketStatus)),
        ];
    }
    
    public function headings(): array
    {
        return [
            'SL NO',
            'Ticket ID',
            'Accountability Type',
            'Vehicle Number',
            'Chassis Number',
            'Vehicle ID',
            'Vehicle Make',
            'Vehicle Model',
            'Vehicle Type',
            'City',
            'Zone',
            'Customer Name',
            'Rider Name',
            'Rider Number',
            'Created Date & Time',
            'Current Status',
            'Ticket Status',
        ];
    }
    
     public function chunkSize(): int
    {
        return 1000; 
    }
}
