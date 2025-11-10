<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\B2B\Entities\B2BRecoveryRequest;

class B2BRecoveryManagerRequestExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;
    protected $city;
    protected $zone;
    protected $status;
    // protected $accountability_type; //updated by logesh
    // protected $customer_id; //updated by logesh

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = null, $zone = null,$status = null)
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = $city;
        $this->zone           = $zone;
        $this->status           = $status;
        // $this->accountability_type = $accountability_type; //updated by logesh
        // $this->customer_id = $customer_id; //updated by logesh
    }

    public function collection()
    {
        $query = B2BRecoveryRequest::with([
            'assignment.VehicleRequest.city',
            'assignment.VehicleRequest.zone',
            'assignment.vehicle',
            'rider.customerlogin.customer_relation'
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
            
            if ($this->status == 'pending') {
                $query->where('status', 'opened');
            } elseif ($this->status == 'all' || empty($this->status)) {
                // No status filter applied — show all
            }elseif ($this->status == 'agent-assigned' || empty($this->status)) {
                $query->where('status', 'agent_assigned');
            } 
            elseif ($this->status == 'not-recovered' || empty($this->status)) {
                $query->where('status', 'not_recovered');
            }else {
                $query->where('status', $this->status);
            }
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function map($row): array
    {
        $mapped = [];

        foreach ($this->selectedFields as $key) {
            switch ($key) {
                case 'req_id':
                    $mapped[] = $row->assignment->VehicleRequest->req_id ?? '-';
                    break;
                    
                case 'rider_name':
                    $mapped[] = $row->rider->name ?? '-';
                    
                    break;

                case 'vehicle_no':
                    $mapped[] = $row->assignment->vehicle->permanent_reg_number ?? '-';
                    break;

                case 'chassis_number':
                    $mapped[] = $row->assignment->vehicle->chassis_number ?? '-';
                    break;
                
                case 'vehicle_type':
                    $mapped[] = $row->assignment->vehicle->vehicle_type_relation->name ?? '-';
                    break;
                    
                case 'vehicle_model':
                    $mapped[] = $row->assignment->vehicle->vehicle_model_relation->vehicle_model ?? '-';
                    break;
                    
                case 'vehicle_make':
                    $mapped[] = $row->assignment->vehicle->vehicle_model_relation->make ?? '-';
                    break;
                    
                case 'vehicle_color':
                    $mapped[] = $row->assignment->vehicle->color_relation->name ?? '-';
                    break;
                    
                case 'vehicle_variant':
                    $mapped[] = $row->assignment->vehicle->vehicle_model_relation->variant ?? '-';
                    break;
                    
                case 'registration_certificate':
                    $mapped[] = $row->assignment->vehicle->reg_certificate_attachment ? asset('EV/asset_master/reg_certificate_attachments/' . $row->assignment->vehicle->reg_certificate_attachment) : '-';
                    break;
                    
                case 'insurance_attachment':
                    $mapped[] = $row->assignment->vehicle->insurance_attachment ? asset('EV/asset_master/insurance_attachments/' . $row->assignment->vehicle->insurance_attachment) : '-';
                    break;
                    
                case 'hsrp_attachment':
                    $mapped[] = $row->assignment->vehicle->hsrp_copy_attachment ? asset('EV/asset_master/hsrp_certificate_attachments/' . $row->assignment->vehicle->hsrp_copy_attachment) : '-';
                    break;
                    
                case 'fitness_certificate':
                    $mapped[] = $row->assignment->vehicle->fc_attachment ? asset('EV/asset_master/fc_attachments/' . $row->assignment->vehicle->fc_attachment) : '-';
                    break;
                    
                    
                case 'mobile_no':
                    $mapped[] = $row->rider->mobile_no ?? '-';
                    break;
                
                case 'rider_email':
                    $mapped[] = $row->rider->mobile_no ?? '-';
                    break;
                 
                 
                case 'poc_name':
                    $mapped[] = $row->rider->customerlogin->customer_relation->trade_name ?? '-';
                    break;
                
                case 'poc_number':
                    $mapped[] = $row->rider->customerlogin->customer_relation->phone ?? '-';
                    break;
                    
                case 'city':
                    $mapped[] = $row->assignment->VehicleRequest->city->city_name ?? '-';
                    break;

                case 'zone':
                    $mapped[] = $row->assignment->VehicleRequest->zone->name ?? '-';
                    break;

                case 'reason':
                    $reasons = [
                        1 => 'Breakdown',
                        2 => 'Battery Drain',
                        3 => 'Accident',
                        4 => 'Rider Unavailable',
                        5 => 'Other',
                    ];
                    $mapped[] = $reasons[(int)($row->reason ?? 0)] ?? '-';
                    break;

                case 'description':
                    $mapped[] = $row->description ?? '-';
                    break;
                
                case 'created_by':
                    $mapped[] = $row->rider->customerlogin->customer_relation->trade_name ?? '-';
                    break;

                case 'status':
                    $mapped[] = ucfirst($row->status ?? '-');
                    break;
                
                case 'agent_status':
                    $mapped[] = ucfirst($row->agent_status ?? '-');
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
                
                case 'adhar_front':
                    $mapped[] = $row->rider->adhar_front ? asset('b2b/aadhar_images/' . $row->rider->adhar_front) : '-';
                    break;
                    
                case 'adhar_back':
                    $mapped[] = $row->rider->adhar_back ? asset('b2b/aadhar_images/' . $row->rider->adhar_back) : '-';
                    break;
                
                case 'adhar_number':
                    $mapped[] = $row->rider->adhar_number ?? '-';
                    break;
                
                case 'pan_front':
                    $mapped[] = $row->rider->pan_front ? asset('b2b/pan_images/' . $row->rider->pan_front) : '-';
                    break;
                
                case 'pan_back':
                    $mapped[] = $row->rider->pan_back ? asset('b2b/pan_images/' . $row->rider->pan_back) : '-';
                    break;
                
                case 'pan_number':
                    $mapped[] = $row->rider->pan_number ?? '-';
                    break;
                
                case 'driving_license_front':
                    $mapped[] = $row->rider->driving_license_front ? asset('b2b/driving_license_images/' . $row->rider->driving_license_front) : '-';
                    break;
                
                case 'driving_license_back':
                    $mapped[] = $row->rider->driving_license_back ? asset('b2b/driving_license_images/' . $row->rider->driving_license_back) : '-';
                    break;
                
                case 'driving_license_number':
                    $mapped[] = $row->rider->driving_license_number  ?? '-';
                    break;
                
                case 'llr_image':
                    $mapped[] = $row->rider->llr_image ? asset('b2b/llr_images/' . $row->rider->llr_image) : '-';
                    break;
                
                case 'llr_number':
                    $mapped[] = $row->rider->llr_number ?? '-';
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
            'vehicle_no'    => 'Vehicle Number',
            'chassis_number'=> 'Chassis Number',
            'vehicle_type'  =>'Vehicle Type',
            'vehicle_model'  =>'Vehicle Model',
            'vehicle_make'  =>'Vehicle Make',
            'vehicle_color'  =>'Vehicle Color',
            'registration_certificate' =>'Registration Certificate',
            'insurance_attachment'  =>'Insurance Attachment',
            'hsrp_attachment'  =>'HSRP Attachment',
            'fitness_certificate'  =>'Fitness Certificate',
            'rider_name'    => 'Rider Name',
            'rider_email'  =>'Rider Email',
            'mobile_no'     => 'Mobile No',
            'adhar_front'  =>'Aadhar Front',
            'adhar_back'  =>'Aadhar Back',
            'adhar_number'  =>'Aadhar Number',
            'pan_front'  =>'Pan_Front',
            'pan_back'  =>'Pan Back',
            'pan_number' =>'Pan Number',
            'driving_license_front' =>'Driving License Front',
            'driving_license_back' =>'Driving License Back',
            'driving_license_number' =>'Driving License Number',
            'llr_image' =>'LLR Image',
            'llr_number' =>'LLR Number',
            'city'          => 'City',
            'zone'          => 'Zone',
            'poc_name'      => 'POC Name',
            'poc_number'   => 'POC Contact',
            'reason'        => 'Reason',
            'description'   => 'Description',
            'created_by'    => 'Created By',
            'status'        => 'Status',
            'agent_status'  => 'Agent Status',
            'recovery_images'=>'Recovery Images',
            'recovery_video' =>'Recovery Video',
            'created_at'    => 'Created Date & Time',
            'closed_at'    => 'Closed Date & Time',
        ];

        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }
}
