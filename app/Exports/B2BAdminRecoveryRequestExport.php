<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\B2B\Entities\B2BRecoveryRequest;
use Modules\MasterManagement\Entities\RecoveryReasonMaster;//updated by Gowtham.S

class B2BAdminRecoveryRequestExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;
    protected $city=[];
    protected $zone=[];
    protected $accountability_type=[]; //updated by logesh
    protected $customer_id=[]; //updated by logesh
    protected $status=[]; //updated by logesh
    protected $vehicle_type=[]; //updated by logesh
    protected $vehicle_model=[]; //updated by logesh
    protected $vehicle_make=[]; //updated by logesh
    protected $date_filter; //updated by Mugesh

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = [], $zone = [],$status=[],$accountability_type = [],$customer_id=[] , $vehicle_type=[] , $vehicle_model=[],$vehicle_make=[], $date_filter)
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = (array)$city;
        $this->zone           = (array)$zone;
        $this->accountability_type = (array)$accountability_type; //updated by logesh
        $this->customer_id =(array) $customer_id; //updated by logesh
        $this->status = (array)$status; //updated by logesh
        $this->vehicle_type =(array) $vehicle_type; //updated by logesh
        $this->vehicle_model = (array)$vehicle_model; //updated by logesh
        $this->vehicle_make =(array) $vehicle_make; //updated by logesh
        $this->date_filter = $date_filter; //updated by Mugesh
    }

    public function collection()
    {
        $query = B2BRecoveryRequest::with([
            'assignment.VehicleRequest.city',
            'assignment.VehicleRequest.zone',
            'assignment.vehicle',
            'assignment.vehicle.quality_check',
            'assignment.rider.customerlogin.customer_relation'
        ]);

        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        } else {
            if (!empty($this->city) && !in_array('all',$this->city)) {
                $query->whereHas('assignment.VehicleRequest', function ($q) {
                    $q->whereIn('city_id', $this->city);
                });
            }

            if (!empty($this->zone) && !in_array('all',$this->zone)) {
                $query->whereHas('assignment.VehicleRequest', function ($q) {
                    $q->whereIn('zone_id', $this->zone);
                });
            }
            
            if (!empty($this->vehicle_model) && !in_array('all',$this->vehicle_model)) {
                $query->whereHas('assignment.vehicle.quality_check', function ($q) {
                    $q->whereIn('vehicle_model', $this->vehicle_model);
                });
            }
            
            if (!empty($this->vehicle_type) && !in_array('all',$this->vehicle_type)) {
                $query->whereHas('assignment.vehicle.quality_check', function ($q) {
                    $q->whereIn('vehicle_type', $this->vehicle_type);
                });
            }
            
            if (!empty($this->vehicle_make) && !in_array('all',$this->vehicle_make)) {
                $query->whereHas('assignment.vehicle.quality_check.vehicle_model_relation', function ($q) {
                    $q->whereIn('make', $this->vehicle_make);
                });
            }
            
            
            if (!empty($this->date_filter)) {
                switch ($this->date_filter) {
            
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
            
                    case 'week':
                        $query->whereBetween('created_at', [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]);
                        break;
                    case 'last_15_days':
                        $query->whereMonth('created_at', now()->subDays(14)->startOfDay())
                              ->whereYear('created_at', now()->endOfDay());
                        break;
                    case 'month':
                        $query->whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year);
                        break;
            
                    case 'year':
                        $query->whereYear('created_at', now()->year);
                        break;
            
                    // custom handled outside
                }
            }
            
            if ($this->from_date) {
                $query->whereDate('created_at', '>=', $this->from_date);
            }

            if ($this->to_date) {
                $query->whereDate('created_at', '<=', $this->to_date);
            }
            
            if (!empty($this->status) && !in_array('all',$this->status)) {
                $query->whereIn('status', $this->status);
            }
            //updated by logesh
            if (!empty($this->accountability_type) && !in_array('all',$this->accountability_type)) {
                $query->wherehas('assignment.VehicleRequest', function ($p) {
                     $p->whereIn('account_ability_type', $this->accountability_type);
                });
            }
            //updated by logesh
            if (!empty($this->customer_id) && !in_array('all',$this->customer_id)) {
                $query->wherehas('assignment.VehicleRequest.rider.customerLogin.customer_relation', function ($p) {
                     $p->whereIn('id', $this->customer_id);
                });
            }
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function map($row): array
    {
        $mapped = [];
        
        if ($row->status === 'closed' && $row->closed_at) {
            $aging = \Carbon\Carbon::parse($row->created_at)
                        ->diffForHumans(\Carbon\Carbon::parse($row->closed_at), true);
        } else {
            $aging = \Carbon\Carbon::parse($row->created_at)
                        ->diffForHumans(now(), true);
        }
                        
                        

        foreach ($this->selectedFields as $key) {
            switch ($key) {
                case 'req_id':
                    $mapped[] = $row->assignment->VehicleRequest->req_id ?? '-';
                    break;
                    
                case 'accountability_type':
                    $mapped[] = $row->assignment->VehicleRequest->accountAbilityRelation->name ?? '-';
                    break;
                    
                case 'rider_name':
                    $mapped[] = $row->assignment->rider->name ?? '-';
                    
                    break;

                case 'vehicle_no':
                    $mapped[] = $row->assignment->vehicle->permanent_reg_number ?? '-';
                    break;

                case 'chassis_number':
                    $mapped[] = $row->assignment->vehicle->chassis_number ?? '-';
                    break;
                
                case 'vehicle_type':
                    $mapped[] = $row->assignment->vehicle->vehicle_type_relation->name?? '-';
                    break;
                    
                case 'vehicle_model':
                    $mapped[] = $row->assignment->vehicle->vehicle_model_relation->vehicle_model?? '-';
                    break;
                    
                case 'vehicle_make':
                    $mapped[] = $row->assignment->vehicle->vehicle_model_relation->make?? '-';
                    break;
                    
                case 'mobile_no':
                    $mapped[] = $row->assignment->rider->mobile_no ?? '-';
                    break;

                case 'poc_name':
                    $mapped[] = $row->assignment->rider->customerlogin->customer_relation->trade_name ?? '-';
                    break;
                
                case 'poc_number':
                    $mapped[] = $row->assignment->rider->customerlogin->customer_relation->phone ?? '-';
                    break;
                    
                case 'city':
                    $mapped[] = $row->assignment->VehicleRequest->city->city_name ?? '-';
                    break;

                case 'zone':
                    $mapped[] = $row->assignment->VehicleRequest->zone->name ?? '-';
                    break;

                case 'reason':
                    $reasonData = RecoveryReasonMaster::where('id',$row->reason)->first();
                    $reason = $reasonData->label_name ?? 'Unknown';
                    $mapped[] = $reason ?? '-';
                    break;

                case 'description':
                    $mapped[] = $row->description ?? '-';
                    break;

                case 'created_by':
                    $created_by = $row->created_by_type == 'b2b-web-dashboard' 
                        ? 'Customer' 
                        : ($row->created_by_type == 'b2b-admin-dashboard' ? 'Admin' : '-');
                    $mapped[] = $created_by;
                    break;



                case 'status':
                    $mapped[] = ucfirst($row->status ?? '-');
                    break;
                
                case 'agent_status':
                    $status = $row->agent_status ?? '';
                    $mapped[] = !empty($status) ? ucfirst($status) : 'Not Assigned';
                    break;
                    
                 case 'created_at':
                    $mapped[] = $row->created_at
                        ? \Carbon\Carbon::parse($row->created_at)->format('d M Y h:i A')
                        : '-';
                    break;
                
                case 'closed_at':
                    $mapped[] = $row->closed_at
                        ? \Carbon\Carbon::parse($row->closed_at)->format('d M Y h:i A')
                        : '-';
                    break;
                case 'aging':
                    $mapped[] = $aging ?? '-';
                    break;
                    
                case 'recovery_images':
                    $attachments = $row->images ?? [];
                
                    // Decode JSON if it's a string
                    if (is_string($attachments)) {
                        $attachments = json_decode($attachments, true);
                    }
                
                    if (is_array($attachments) && !empty($attachments)) {
                        $urls = [];
                        foreach ($attachments as $file) {
                            $urls[] = asset('b2b/recovery_comments/' . $file);
                        }
                        $mapped[] = implode(", ", $urls);
                    } else {
                        $mapped[] = '-';
                    }
                    break;
                
                case 'recovery_video':
                    $report = $row->video ?? '';
                    if (!empty($report)) {
                        $mapped[] = asset('b2b/recovery_comments/' . $report);
                    } else {
                        $mapped[] = '-';
                    }
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
            'accountability_type'    => 'Accountablity Type', //updated by logesh
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
            'reason'        => 'Reason',
            'description'   => 'Description',
            'recovery_images'=>'Recovery Images',
            'recovery_video' =>'Recovery Video',
            'created_by'    => 'Created By',
            'agent_status'  => 'Agent Status',
            'status'        => 'Status',
            'created_at'    => 'Created Date & Time',
            'closed_at'    => 'Closed Date & Time',
            'aging'        => 'Aging'
        ];

        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }
}
