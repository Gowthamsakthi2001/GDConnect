<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\B2B\Entities\B2BServiceRequest;

class B2BAdminServiceRequestExport implements FromCollection, WithHeadings, WithMapping
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

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = null, $zone = null, $status =null ,$accountability_type = null,$customer_id=null)
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
        $query = B2BServiceRequest::with([
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
            
            if ($this->status) {
                $query->where('status', $this->status);
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
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function map($row): array
    {
        $mapped = [];


        if ($row->status === 'closed') {
            $created   = \Carbon\Carbon::parse($row->created_at);
            $completed = \Carbon\Carbon::parse($row->updated_at);
            $diffInDays = $created->diffInDays($completed);
            $diffInHours = $created->diffInHours($completed);
            $diffInMinutes = $created->diffInMinutes($completed);
        
            if ($diffInDays > 0) {
                $aging = $diffInDays . ' days';
            } elseif ($diffInHours > 0) {
                $aging = $diffInHours . ' hours';
            } else {
                $aging = $diffInMinutes . ' mins';
            }
        } else {
            $created   = \Carbon\Carbon::parse($row->created_at);
            $now       = now();
            $diffInDays = $created->diffInDays($now);
            $diffInHours = $created->diffInHours($now);
            $diffInMinutes = $created->diffInMinutes($now);
        
            if ($diffInDays > 0) {
                $aging = $diffInDays . ' days';
            } elseif ($diffInHours > 0) {
                $aging = $diffInHours . ' hours';
            } else {
                $aging = $diffInMinutes . ' mins';
            }
        }

        foreach ($this->selectedFields as $key) {
            switch ($key) {
                case 'req_id':
                    $mapped[] = $row->assignment->VehicleRequest->req_id ?? '-';
                    break;
                
                case 'ticket_id':
                    $mapped[] = $row->ticket_id ?? '-';
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

                case 'client':
                    $mapped[] = $row->assignment->rider->customerlogin->customer_relation->trade_name ?? '-';
                    break;

                case 'city':
                    $mapped[] = $row->assignment->VehicleRequest->city->city_name ?? '-';
                    break;

                case 'zone':
                    $mapped[] = $row->assignment->VehicleRequest->zone->name ?? '-';
                    break;

                case 'repair_type':
                    $mapped[] = $row->repair_type == 1
                        ? 'Breakdown Repair'
                        : ($row->repair_type == 2 ? 'Running Repair' : '-');
                    break;

                case 'location':
                    $mapped[] = $row->gps_pin_address ?? '-';
                    break;

                case 'created_by':
                    $mapped[] = ucfirst($row->type ?? '-');
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
            'ticket_id'     => 'Ticket ID',
            'accountability_type'    => 'Accountablity Type', //updated by logesh
            'vehicle_no'    => 'Vehicle Number',
            'chassis_number'=> 'Chassis Number',
            'rider_name'    => 'Rider Name',
            'mobile_no'     => 'Mobile No',
            'client'        => 'Client Name',
            'city'          => 'City',
            'zone'          => 'Zone',
            'poc_name'      => 'POC Name',
            'poc_number'    => 'POC Number',
            'description'   => 'Description',
            'repair_type'   => 'Repair Type',
            'location'      => 'Location',
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
