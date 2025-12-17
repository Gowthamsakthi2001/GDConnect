<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\B2B\Entities\B2BReportAccident;
use Modules\MasterManagement\Entities\CustomerLogin;
use Carbon\Carbon;

class B2BAdminAccidentSummaryExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
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

    // public function collection()
    // {
    //     $query = B2BReportAccident::with([
    //         'assignment.rider',
    //         'assignment.vehicle.vehicle_type_relation',
    //         'assignment.vehicle.vehicle_model_relation',
    //         'assignment.vehicle.quality_check.customer_relation',
    //         'assignment.vehicle.quality_check.location_relation',
    //         'assignment.vehicle.quality_check.zone',
    //         'assignment.zone',
    //         'assignment.VehicleRequest',
    //         'assignment.VehicleRequest.customerLogin.customer_relation',
    //     ]);

    //     // -------------------------------
    //     // Core Filters
    //     // -------------------------------
    //     if (!empty($this->accountability_type) && !in_array('all',$this->accountability_type)) {
    //     $query->whereHas('assignment.VehicleRequest', function ($q) {
    //         $q->whereIn('account_ability_type', $this->accountability_type);
    //     });
    //     }
        
    //     if (!empty($this->customer_id) && !in_array('all',$this->customer_id)) {
    //     $query->whereHas('assignment.VehicleRequest.customerLogin.customer_relation', function ($q) {
    //         $q->whereIn('id', $this->customer_id);
    //     });
    //     }
        
    //     // -------------------------------
    //     // Vehicle type filter
    //     // -------------------------------
    //     if (!empty($this->vehicle_type) && !in_array('all',$this->vehicle_type)) {
    //     $query->whereHas('assignment.vehicle', function ($v) {
    //         $v->whereIn('vehicle_type', $this->vehicle_type);
    //     });
    //     }
        
    //     if (!empty($this->vehicle_model) && !in_array('all',$this->vehicle_model)) {
    //         $query->whereHas('assignment.vehicle.quality_check', fn($q) => $q->whereIn('vehicle_model', $this->vehicle_model));
    //     }
        
    //     if (!empty($this->vehicle_make) && !in_array('all',$this->vehicle_make)) {
    //         $query->whereHas('assignment.vehicle.quality_check.vehicle_model_relation', fn($q) => $q->whereIn('make', $this->vehicle_make));
    //     }
        
    //     // -------------------------------
    //     // City filter
    //     // -------------------------------
    //     if (!empty($this->city) && !in_array('all',$this->city)) {
    //     $query->whereHas('assignment.vehicle.quality_check', function ($v) {
    //         $v->whereIn('location', $this->city);
    //     });
    //     }
        
    //     // -------------------------------
    //     // Zone filter
    //     // -------------------------------
    //     if (!empty($this->zone) && !in_array('all',$this->zone)) {
    //     $query->whereHas('assignment.vehicle.quality_check', function ($v) {
    //         $v->whereIn('zone_id', $this->zone);
    //     });
    //     }

    //     // Vehicle number filter (multi-select)
    //     if (!empty($this->vehicle_no)) {
    //         $vehicleNos = (array) $this->vehicle_no;
    //         $query->whereHas('assignment.vehicle', function ($v) use ($vehicleNos) {
    //             $v->whereIn('id', $vehicleNos);
    //         });
    //     }

    //     // -------------------------------
    //     // Date range filter
    //     // -------------------------------
    //     $from = $this->from_date;
    //     $to   = $this->to_date;

    //     switch ($this->date_range) {
    //         case 'yesterday':
    //             $from = $to = now()->subDay()->toDateString();
    //             break;
    //         case 'last7':
    //             $from = now()->subDays(6)->toDateString();
    //             $to   = now()->toDateString();
    //             break;
    //         case 'last30':
    //             $from = now()->subDays(29)->toDateString();
    //             $to   = now()->toDateString();
    //             break;
    //         case 'custom':
    //             // already provided
    //             break;
    //         default:
    //             $from = $to = now()->toDateString();
    //             break;
    //     }

    //     if ($from && $to) {
    //         $query->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
    //     }

    //     // Status filter
    //     if (!empty($this->status) && !in_array('all',$this->status)) {
    //     $query->whereIn('status' , $this->status);
    //     }

    //     return $query->orderByDesc('id')->get();
    // }
    
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
                'vh.id as vehicle_id',
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
                $query->whereBetween(DB::raw('DATE(ar.created_at)'), [$from, $to]);
            }
        
    
        return $query->orderBy('ar.id', 'desc');
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
            $row->req_id ?? '-',
            $row->accountability_name ?? '-',
            $row->permanent_reg_number ?? '-',
            $row->chassis_number ?? '-',
            $row->vehicle_id ?? '-',
            $row->vehicle_make ?? '-',
            $row->vehicle_model ?? '-',
            $row->vehicle_type_name ?? '-',
            $row->city_name ?? '-',
            $row->zone_name ?? '-',
            $row->client_name ?? '-',
            $row->rider_name ?? '-',
            $row->rider_mobile_no ?? '-',
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
    
     public function chunkSize(): int
    {
        return 1000; 
    }
}
