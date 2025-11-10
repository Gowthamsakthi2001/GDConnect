<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\B2B\Entities\B2BServiceRequest;
use Modules\MasterManagement\Entities\CustomerLogin;

class B2BAdminServiceReportExport implements FromCollection, WithHeadings, WithMapping
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
    protected $status;
    protected $sl = 0;

    public function __construct($date_range, $from_date, $to_date, $vehicle_type, $city, $zone, $vehicle_no = [] , $accountability_type , $customer , $status)
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
        $this->status   = $status;
    }

        public function collection()
        {
        $query = B2BServiceRequest::with([
        'assignment.rider',
        'assignment.vehicle.vehicle_type_relation',
        'assignment.vehicle.vehicle_model_relation',
        'assignment.vehicle.quality_check.customer_relation',
        'assignment.vehicle.quality_check.location_relation',
        'assignment.vehicle.quality_check.zone',
        'assignment.zone',
        'assignment.VehicleRequest',
        'assignment.VehicleRequest.customerLogin.customer_relation'
        ]);
        
        // -------------------------------
        // Core filters
        // -------------------------------
        if (!empty($this->accountability_type)) {
        $query->whereHas('assignment.VehicleRequest', function ($q) {
            $q->where('account_ability_type', $this->accountability_type);
        });
        }
        
        if (!empty($this->customer)) {
        $query->whereHas('assignment.VehicleRequest.customerLogin.customer_relation', function ($q) {
            $q->where('id', $this->customer);
        });
        }
        
        // -------------------------------
        // Vehicle type filter
        // -------------------------------
        if (!empty($this->vehicle_type)) {
        $query->whereHas('assignment.vehicle', function ($v) {
            $v->where('vehicle_type', $this->vehicle_type);
        });
        }
        
        // -------------------------------
        // City filter
        // -------------------------------
        if (!empty($this->city)) {
        $query->whereHas('assignment.vehicle.quality_check', function ($v) {
            $v->where('location', $this->city);
        });
        }
        
        // -------------------------------
        // Zone filter
        // -------------------------------
        if (!empty($this->zone)) {
        $query->whereHas('assignment.vehicle.quality_check', function ($v) {
            $v->where('zone_id', $this->zone);
        });
        }
        
        // -------------------------------
        // Vehicle number filter (multi-select)
        // -------------------------------
        if (!empty($this->vehicle_no)) {
        $vehicleNos = (array) $this->vehicle_no;
        $query->whereHas('assignment.vehicle', function ($v) use ($vehicleNos) {
            $v->whereIn('id', $vehicleNos);
        });
        }
        
        // -------------------------------
        // Date range filter
        // -------------------------------
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
            // Already passed via constructor
            break;
        default:
            $from = $to = now()->toDateString();
            break;
        }
        
        if (!empty($from) && !empty($to)) {
        $query->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
        }
        
        if (!empty($this->status)) {
        $query->where('status' , $this->status);
        }
        
        // -------------------------------
        // Final Query Result
        // -------------------------------
        return $query->orderByDesc('id')->get();
        }


    public function map($row): array
    {
        $this->sl++;

        // Current status badges (for export, we just keep text)
        $currentStatus = $row->current_status ?? '-';
        $ticketStatus  = $row->status ?? '-';

        return [
            $this->sl,
            $row->ticket_id ?? '-',
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
            $row->created_at ? $row->created_at->format('d M Y h:i A') : '-',
            ucfirst(str_replace('_', ' ', $currentStatus)),
            ucfirst(str_replace('_', ' ', $ticketStatus)),
        ];
    }
    
    public function headings(): array
    {
        return [
            'SL NO',
            'Ticket ID',
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
            'Created Date & Time',
            'Current Status',
            'Ticket Status',
        ];
    }
}
