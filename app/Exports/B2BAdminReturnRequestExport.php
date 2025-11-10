<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\B2B\Entities\B2BReturnRequest;

class B2BAdminReturnRequestExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;
    protected $city;
    protected $zone;
    protected $accountability_type; //updated by logesh
    protected $customer_id; //updated by logesh
    protected $status; //updated by logesh

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = null, $zone = null,$status = null,$accountability_type = null,$customer_id=null)
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = $city;
        $this->zone           = $zone;
        $this->accountability_type = $accountability_type; //updated by logesh
        $this->customer_id = $customer_id; //updated by logesh
        $this->status = $status; //updated by logesh
    }

    public function collection()
    {
        $query = B2BReturnRequest::with([
            'assignment.VehicleRequest.city',
            'assignment.VehicleRequest.zone',
            'assignment.vehicle',
            'assignment.rider.customerlogin.customer_relation',
            'agent'
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
            
            if ($this->status) {
                $query->where('status', $this->status);
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
                
                //updated by logesh
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
                    $mapped[] =$row->return_reason ?? '-';
                    break;

                case 'description':
                    $mapped[] = $row->description ?? '-';
                    break;
                
                case 'kilometer_value':
                    $mapped[] = $row->kilometer_value ?? '-';
                    break;
                    
                case 'odometer_value':
                    $mapped[] = $row->odometer_value ?? '-';
                    break;
                    
                case 'kilometer_image':
                    $mapped[] = $row->kilometer_image ?asset('b2b/kilometer_images/' . $row->kilometer_image): '-';
                    break;
                    
                case 'odometer_image':
                    $mapped[] = $row->odometer_image ?asset('b2b/odometer_images/' . $row->odometer_image): '-';
                    break;
                    
                case 'vehicle_front':
                    $mapped[] = $row->vehicle_front ?asset('b2b/vehicle_front/' . $row->vehicle_front): '-';
                    break;
                    
                case 'vehicle_back':
                    $mapped[] = $row->vehicle_back ?asset('b2b/vehicle_back/' . $row->vehicle_back): '-';
                    break;
                    
                case 'vehicle_top':
                    $mapped[] = $row->vehicle_top ?asset('b2b/vehicle_top/' . $row->vehicle_top): '-';
                    break;
                    
                case 'vehicle_bottom':
                    $mapped[] = $row->vehicle_bottom ?asset('b2b/vehicle_bottom' . $row->vehicle_bottom): '-';
                    break;
                    
                case 'vehicle_left':
                    $mapped[] = $row->vehicle_left ?asset('b2b/vehicle_left/' . $row->vehicle_left): '-';
                    break;
                    
                case 'vehicle_right':
                    $mapped[] = $row->vehicle_right ?asset('b2b/vehicle_right/' . $row->vehicle_right): '-';
                    break;
                    
                case 'vehicle_battery':
                    $mapped[] = $row->vehicle_battery ?asset('b2b/vehicle_battery/' . $row->vehicle_battery): '-';
                    break;
                    
                case 'vehicle_charger':
                    $mapped[] = $row->vehicle_charger ?asset('b2b/vehicle_charger/' . $row->vehicle_charger): '-';
                    break;

                case 'closed_by':
                    $mapped[] = $row->agent->name ?? '-';
                    break;
                    
                case 'created_by':
                    $mapped[] = $row->assignment->rider->customerlogin->customer_relation->trade_name ?? '-';
                    break;

                case 'status':
                    $mapped[] = ucfirst($row->status ?? '-');
                    break;

                case 'created_at':
                    $mapped[] = $row->created_at
                        ? $row->created_at->format('d M Y h:i A')
                        : '-';
                    break;
                case 'updated_at':
                    $mapped[] = $row->updated_at
                        ? $row->updated_at->format('d M Y h:i A')
                        : '-';
                    break;
                    
                case 'aging':
                    $mapped[] = $aging ?? '-';
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
            'kilometer_value'=> 'Kilometer Value',
            'odometer_value'=> 'Odometer Value',
            'kilometer_image'=> 'Kilometer Image',
            'odometer_image'=> 'Odometer Image',
            'vehicle_front'=> 'Vehicle Front',
            'vehicle_back'=> 'Vehicle Back',
            'vehicle_top'=> 'Vehicle Top',
            'vehicle_bottom'=> 'Vehicle Bottom',
            'vehicle_left'=> 'Vehicle Left',
            'vehicle_right'=> 'Vehicle Right',
            'vehicle_battery'=> 'Vehicle Battery',
            'vehicle_charger'=> 'Vehicle Charger',
            'closed_by'=> 'Closed By',
            'created_by'    => 'Created By',
            'status'        => 'Status',
            'created_at'    => 'Created Date & Time',
            'updated_at'    => 'Updated Date & Time',
            'aging'         => 'Aging'
        ];

        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }
}
