<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\B2B\Entities\B2BReturnRequest;
use Modules\MasterManagement\Entities\CustomerLogin;
use Carbon\Carbon;

class B2BAdminReturnReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $date_range;
    protected $from_date;
    protected $to_date;
    protected $vehicle_type;
    protected $city;
    protected $zone;
    protected $vehicle_no;
    protected $accountability_type;
    protected $customer;
    protected $sl = 0;

    public function __construct($date_range, $from_date, $to_date, $vehicle_type, $city, $zone, $vehicle_no = [] ,$accountability_type ,$customer)
    {
        $this->date_range   = $date_range;
        $this->from_date    = $from_date;
        $this->to_date      = $to_date;
        $this->vehicle_type = $vehicle_type;
        $this->city         = $city;
        $this->zone         = $zone;
        $this->vehicle_no   = $vehicle_no;
        $this->accountability_type   = $accountability_type;
        $this->customer   = $customer;
    }

    public function collection()
    {
        $query = B2BReturnRequest::with([
            'assignment.rider',
            'assignment.vehicle.vehicle_type_relation',
            'assignment.vehicle.vehicle_model_relation',
            'assignment.vehicle.quality_check.customer_relation',
            'assignment.vehicle.quality_check.location_relation',
            'assignment.vehicle.quality_check.zone',
            'assignment.zone',
            'assignment.VehicleRequest',
            'agent'
        ]);

        //  Accountability Type Filter
        if (!empty($this->accountability_type)) {
            $query->whereHas('assignment.VehicleRequest', function ($q) {
                $q->where('account_ability_type', $this->accountability_type);
            });
        }

        //  Customer Filter
        if (!empty($this->customer)) {
            $query->whereHas('assignment.VehicleRequest.customerLogin.customer_relation', function ($subQuery) {
                $subQuery->where('id', $this->customer);
            });
        }

        //  City Filter
        if (!empty($this->city)) {
            $query->whereHas('assignment.vehicle.quality_check', function ($v) {
                $v->where('location', $this->city);
            });
        }

        //  Zone Filter
        if (!empty($this->zone)) {
            $query->whereHas('assignment.vehicle.quality_check', function ($v) {
                $v->where('zone_id', $this->zone);
            });
        }

        // Vehicle Type Filter
        if (!empty($this->vehicle_type)) {
            $query->whereHas('assignment.vehicle', function ($v) {
                $v->where('vehicle_type', $this->vehicle_type);
            });
        }

        //  Vehicle Number Filter (multi-select)
        if (!empty($this->vehicle_no)) {
            $vehicleNos = (array) $this->vehicle_no;
            $query->whereHas('assignment.vehicle', function ($v) use ($vehicleNos) {
                $v->whereIn('id', $vehicleNos);
            });
        }

        //  Date Range Filter
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
                // already passed
                break;
            default:
                $from = $to = now()->toDateString();
                break;
        }

        if ($from && $to) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
        }

        // Status Filter (Closed)
        $query->where('status', 'closed');

        return $query->orderByDesc('id')->get();
    }
    
    public function map($row): array
    {
        $this->sl++;




        $createdAt = $row->created_at ? Carbon::parse($row->created_at)->format('d M Y h:i A') : '-';
        $closedAt  = $row->closed_at ? Carbon::parse($row->closed_at)->format('d M Y h:i A') : '-';
        
        
        return [
            $this->sl,
            $row->assignment->VehicleRequest->req_id ?? '-',
            $row->assignment->vehicle->permanent_reg_number ?? '-',
            $row->assignment->vehicle->chassis_number ?? '-',
            $row->assignment->vehicle->vehicle_id ?? '-',
            $row->assignment->vehicle->vehicle_model_relation->make ?? '-',
            $row->assignment->vehicle->vehicle_type_relation->name ?? '-',
            $row->assignment->vehicle->quality_check->location_relation->city_name ?? '-',
            $row->assignment->vehicle->quality_check->zone->name ?? '-',
            $row->assignment->VehicleRequest->customerLogin->customer_relation->trade_name ?? '-',
            $row->assignment->rider->name ?? '-',
            $row->assignment->rider->mobile_no ?? '-',
            $row->agent->name ?? '-',
            $row->odometer_value ?? '-',
            $row->odometer_image ? asset('public/b2b/odometer_images/' . $row->odometer_image) : '-',
            $row->vehicle_front ? asset('public/b2b/vehicle_front/' . $row->vehicle_front) : '-',
            $row->vehicle_back ? asset('public/b2b/vehicle_back/' . $row->vehicle_back) : '-',
            $row->vehicle_top ? asset('public/b2b/vehicle_top/' . $row->vehicle_top) : '-',
            $row->vehicle_bottom ? asset('public/b2b/vehicle_bottom/' . $row->vehicle_bottom) : '-',
            $row->vehicle_left ? asset('public/b2b/vehicle_left/' . $row->vehicle_left) : '-',
            $row->vehicle_right ? asset('public/b2b/vehicle_right/' . $row->vehicle_right) : '-',
            $row->vehicle_battery ? asset('public/b2b/vehicle_battery/' . $row->vehicle_battery) : '-',
            $row->vehicle_charger ? asset('public/b2b/vehicle_charger/' . $row->vehicle_charger) : '-',
            $createdAt ,
            $closedAt
            
        ];
    }
    
    public function headings(): array
    {
        return [
            'SL NO',
            'Request ID',
            'Vehicle Number',
            'Chassis Number',
            'Vehicle ID',
            'Vehicle Make',
            'Vehicle Type',
            'City',
            'Zone',
            'Customer Name',
            'Rider Name',
            'Rider Number',
            'Agent Name' ,
            'Odometer value',
            'Odometer Image',
            'Vehicle Front Image',
            'Vehicle Back Image',
            'Vehicle Top Image',
            'Vehicle Bottom Image',
            'Vehicle Left Image',
            'Vehicle Right Image',
            'Vehicle Battery Image',
            'Vehicle Charger Image',
            'Created Date & Time',
            'Completed Date & Time'
        ];
    }
}
