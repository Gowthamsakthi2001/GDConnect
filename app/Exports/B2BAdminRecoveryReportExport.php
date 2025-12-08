<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Modules\B2B\Entities\B2BRecoveryRequest;
use Modules\MasterManagement\Entities\CustomerLogin;
use Carbon\Carbon;

class B2BAdminRecoveryReportExport implements FromCollection, WithHeadings, WithMapping
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

    public function __construct($date_range, $from_date, $to_date, $vehicle_type=[],$vehicle_model=[],$vehicle_make=[], $city=[], $zone=[], $vehicle_no =[], $status=[] , $customer_id=[] ,$accountability_type=[])
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


      $query = B2BRecoveryRequest::with([
                        'assignment.rider',
                        'assignment.vehicle.vehicle_type_relation',
                        'assignment.vehicle.vehicle_model_relation',
                        'assignment.vehicle.quality_check.customer_relation',
                        'assignment.vehicle.quality_check.location_relation',
                        'assignment.vehicle.quality_check.zone',
                        'assignment.zone',
                        'assignment.VehicleRequest',
                        'recovery_agent' ,
                        'assignment.VehicleRequest.customerLogin.customer_relation'
                    ]);


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


        if ($this->vehicle_no) {
            $vehicleNos = (array) $this->vehicle_no; // Ensure it's an array
        
            $query->whereHas('assignment.vehicle', function ($v) use ($vehicleNos) {
                $v->whereIn('id', $vehicleNos);
            });
        }

        // Date range handling
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
                // already handled via from_date and to_date
                break;
            default:
                $from = $to = now()->toDateString();
                break;
        }

        if ($from && $to) {
            $query->whereDate('created_at', '>=', $from)
                  ->whereDate('created_at', '<=', $to);
        }
        
        if (!empty($this->status) && !in_array('all',$this->status)) {
        $query->whereIn('status' , $this->status);
        }
        


        return $query->orderBy('id', 'desc')->get();
    }

    public function map($row): array
    {
        $this->sl++;
    

        $termsCondition = $row->terms_condition
            ? 'Accepted'
            : 'Not Accepted';
    
       
        switch ($row->status) {
            case 'opened':
                $statusText = 'Opened';
                break;
            case 'closed':
                $statusText = 'Closed';
                break;
            case 'agent_assigned':
                $statusText = 'Agent Assigned';
                break;
            case 'not_recovered':
                $statusText = 'Not Recovered';
                break;
            default:
                $statusText = '-';
        }
    
 
        switch ($row->agent_status) {
            case 'opened':
                $agentStatusText = 'Opened';
                break;
            case 'in_progress':
                $agentStatusText = 'In Progress';
                break;
            case 'location_reached':
                $agentStatusText = 'Location Reached';
                break;
            case 'location_revisited':
                $agentStatusText = 'Location Revisited';
                break;
            case 'recovered':
                $agentStatusText = 'Recovered';
                break;
            case 'not_recovered':
                $agentStatusText = 'Not Recovered';
                break;
            case 'closed':
                $agentStatusText = 'Closed';
                break;
            default:
                $agentStatusText = '-';
        }
    
        
        $agentName = $row->recovery_agent
            ? trim(($row->recovery_agent->first_name ?? '') . ' ' . ($row->recovery_agent->last_name ?? ''))
            : '-';
    
        
        
        $createdAt = $row->created_at
            ? Carbon::parse($row->created_at)->format('d M Y h:i A')
            : '-';
            
            
        $closed_by = '-';
        if ($row->closed_by_type == 'recovery-agent') {
            $closed_by = trim(($row->user->first_name ?? '') . ' ' . ($row->user->last_name ?? ''));
        } elseif ($row->closed_by_type == 'recovery-manager-dashboard') {
            $closed_by = $row->user->name ?? '-';
        }
        
        $videoPath = '-';
        if (!empty($row->video)) {
            $videoPath = asset('public/b2b/recovery_comments/' . $row->video);
        }
        $ImagesPath = '-';

        if (!empty($row->images)) {
            // Decode JSON string into array
            $images = json_decode($row->images, true);
        
            if (is_array($images) && count($images) > 0) {
                // Map each image name to full URL
                $imageUrls = array_map(function ($img) {
                    return asset('public/b2b/recovery_comments/' . $img);
                }, $images);
        
                // Join URLs with commas
                $ImagesPath = implode(', ', $imageUrls);
            }
        }
        
        $created_by = "Unknown";
        if($row->created_by_type == 'b2b-web-dashboard'){
            $created_by = 'Customer';
        }elseif($row->created_by_type == 'b2b-admin-dashboard'){
            $created_by = 'GDM';
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
            $row->description ?? '-',
            $termsCondition,
            $row->VehicleRequest->customerLogin->customer_relation->trade_name ?? '-',
            $row->rider->name ?? '-',
            $row->rider->mobile_no ?? '-',
            $agentName,
            $closed_by,
            $videoPath,
            $ImagesPath,
            $created_by,
            $createdAt,
            $statusText,
            $agentStatusText,
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
            'Description',
            'Terms & Condition',
            'Customer Name',
            'Rider Name',
            'Rider Number',
            'Agent Name',
            'Closed By',
            'Video',
            'Images',
            'Created By',
            'Created Date & Time',
            'Status',
            'Agent Status'
        ];
    }
}
