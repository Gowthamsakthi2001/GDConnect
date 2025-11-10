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
    protected $city;
    protected $zone;
    protected $status;
    protected $accountability_type; //updated by logesh
    protected $customer_id; //updated by logesh

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = null, $zone = null,$status=null,$accountability_type = null,$customer_id=null)
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = $city;
        $this->zone           = $zone;
        $this->status           = $status;
        $this->accountability_type = $accountability_type; //updated by logesh
        $this->customer_id = $customer_id; //updated by logesh
    }

    public function collection()
    {
        $query = B2BRecoveryRequest::with([
            'assignment.VehicleRequest.city',
            'assignment.VehicleRequest.zone',
            'assignment.vehicle',
            'assignment.rider.customerlogin.customer_relation'
        ]);

        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        } else {
            if ($this->city) {
                $query->whereHas('assignment.VehicleRequest', function ($q) {
                    $q->where('city_id', $this->city);
                });
            }

            if ($this->zone) {
                $query->whereHas('assignment.VehicleRequest', function ($q) {
                    $q->where('zone_id', $this->zone);
                });
            }

            if ($this->from_date) {
                $query->whereDate('created_at', '>=', $this->from_date);
            }

            if ($this->to_date) {
                $query->whereDate('created_at', '<=', $this->to_date);
            }
            
            if ($this->status) {
                $query->where('status', $this->status);
            }
            //updated by logesh
            if ($this->accountability_type) {
                $query->wherehas('assignment.VehicleRequest', function ($p) {
                     $p->where('account_ability_type', $this->accountability_type);
                });
            }
            //updated by logesh
            if ($this->customer_id) {
                $query->wherehas('assignment.VehicleRequest.rider.customerLogin.customer_relation', function ($p) {
                     $p->where('id', $this->customer_id);
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
