<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\B2B\Entities\B2BReportAccident;
use Modules\MasterManagement\Entities\CustomerLogin;
use Carbon\Carbon;

class B2BAdminAccidentSummaryExport implements FromCollection, WithHeadings, WithMapping
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

    public function __construct($date_range, $from_date, $to_date, $vehicle_type=[],$vehicle_model=[],$vehicle_make=[], $city=[], $zone=[], $vehicle_no = [] , $status=[] ,$accountability_type=[],$customer_id=[])
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

    public function collection()
    {
        $query = B2BReportAccident::with([
            'assignment.rider',
            'assignment.vehicle.vehicle_type_relation',
            'assignment.vehicle.vehicle_model_relation',
            'assignment.vehicle.quality_check.customer_relation',
            'assignment.vehicle.quality_check.location_relation',
            'assignment.vehicle.quality_check.zone',
            'assignment.zone',
            'assignment.VehicleRequest',
            'assignment.VehicleRequest.customerLogin.customer_relation',
        ]);

        // -------------------------------
        // Core Filters
        // -------------------------------
        if (!empty($this->accountability_type) && !in_array('all',$this->accountability_type)) {
        $query->whereHas('assignment.VehicleRequest', function ($q) {
            $q->whereIn('account_ability_type', $this->accountability_type);
        });
        }
        
        if (!empty($this->customer_id) && !in_array('all',$this->customer_id)) {
        $query->whereHas('assignment.VehicleRequest.customerLogin.customer_relation', function ($q) {
            $q->whereIn('id', $this->customer_id);
        });
        }
        
        // -------------------------------
        // Vehicle type filter
        // -------------------------------
        if (!empty($this->vehicle_type) && !in_array('all',$this->vehicle_type)) {
        $query->whereHas('assignment.vehicle', function ($v) {
            $v->whereIn('vehicle_type', $this->vehicle_type);
        });
        }
        
        if (!empty($this->vehicle_model) && !in_array('all',$this->vehicle_model)) {
            $query->whereHas('assignment.vehicle.quality_check', fn($q) => $q->whereIn('vehicle_model', $this->vehicle_model));
        }
        
        if (!empty($this->vehicle_make) && !in_array('all',$this->vehicle_make)) {
            $query->whereHas('assignment.vehicle.quality_check.vehicle_model_relation', fn($q) => $q->whereIn('make', $this->vehicle_make));
        }
        
        // -------------------------------
        // City filter
        // -------------------------------
        if (!empty($this->city) && !in_array('all',$this->city)) {
        $query->whereHas('assignment.vehicle.quality_check', function ($v) {
            $v->whereIn('location', $this->city);
        });
        }
        
        // -------------------------------
        // Zone filter
        // -------------------------------
        if (!empty($this->zone) && !in_array('all',$this->zone)) {
        $query->whereHas('assignment.vehicle.quality_check', function ($v) {
            $v->whereIn('zone_id', $this->zone);
        });
        }

        // Vehicle number filter (multi-select)
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
                // already provided
                break;
            default:
                $from = $to = now()->toDateString();
                break;
        }

        if ($from && $to) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
        }

        // Status filter
        if (!empty($this->status) && !in_array('all',$this->status)) {
        $query->whereIn('status' , $this->status);
        }

        return $query->orderByDesc('id')->get();
    }

    public function map($row): array
    {
        $this->sl++;

        $statusTextMap = [
            'claimed_initiated' => 'Claimed Initiated',
            'insurer_visit_confirmed' => 'Insurer Visit Confirmed',
            'inspection_completed' => 'Inspection Completed',
            'approval_pending' => 'Approval Pending',
            'repair_started' => 'Repair Started',
            'repair_completed' => 'Repair Completed',
            'invoice_submitted' => 'Invoice Submitted',
            'payment_approved' => 'Payment Approved',
            'claim_closed' => 'Claim Closed (Settled)',
        ];
        
        $status = '-';
        if (!empty($row->status)) {
            $status = $statusTextMap[$row->status] ?? ucfirst(str_replace('_', ' ', $row->status));
        }
        $createdAt = $row->created_at ? Carbon::parse($row->created_at)->format('d M Y h:i A') : '-';

        $accidentAttachments = '-';
        if (!empty($row->accident_attachments)) {
            $attachments = json_decode($row->accident_attachments, true);
            if (is_array($attachments) && count($attachments)) {
                $urls = array_map(fn($file) => asset('public/b2b/accident_reports/attachments/'.$file), $attachments);
                $accidentAttachments = implode(', ', $urls);
            }
        }
    
    
        $policeReport = '-';
        if (!empty($row->police_report)) {
            $report = json_decode($row->police_report, true);
            if (isset($report['name'])) {
                $policeReport = asset('public/b2b/accident_reports/police_reports/'.$report['name']);
            }
        }
    
    
        return [
            $this->sl,
            $row->assignment->VehicleRequest->req_id ?? '-',
            $row->assignment->vehicle->permanent_reg_number ?? '-',
            $row->assignment->vehicle->chassis_number ?? '-',
            $row->assignment->vehicle->vehicle_id ?? '-',
            $row->assignment->vehicle->vehicle_model_relation->make ?? '-',
            $row->assignment->vehicle->vehicle_model_relation->vehicle_model ?? '-',
            $row->assignment->vehicle->vehicle_type_relation->name ?? '-',
            $row->assignment->vehicle->quality_check->location_relation->city_name ?? '-',
            $row->assignment->vehicle->quality_check->zone->name ?? '-',
            $row->assignment->VehicleRequest->customerLogin->customer_relation->trade_name ?? '-',
            $row->assignment->rider->name ?? '-',
            $row->assignment->rider->mobile_no ?? '-',
            $row->location_of_accident ?? '-',
            $row->accident_type ?? '-',
            $row->description ?? '-',
            $row->vehicle_damage ?? '-',
            $row->rider_injury_description ?? '-',
            $row->third_party_injury_description ?? '-',
            $accidentAttachments,
            $policeReport,
            $createdAt ,
            $status
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
            'Vehicle Model',
            'Vehicle Type',
            'City',
            'Zone',
            'Customer Name',
            'Rider Name',
            'Rider Number',
            'Location Of Accident',
            'Accident Type',
            'Description',
            'Vehicle Damage',
            'Rider Injury Description',
            'Third Party Injury Description',
            'Accident Attachments',
            'Police Report',
            'Created Date & Time',
            'Status',
        ];
    }
}
