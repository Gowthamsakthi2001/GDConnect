<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\B2B\Entities\B2BServiceRequest;
use Modules\MasterManagement\Entities\CustomerLogin;
use Illuminate\Support\Facades\Auth;

class B2BServiceRequestExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $datefilter;
    protected $selectedIds;
    protected $selectedFields;
    protected $city = [];
    protected $zone = [];
    protected $status = [];
    protected $accountability_type = [];
    protected $status = [];
    protected $vehicle_model = [];
    protected $vehicle_make = [];
    protected $vehicle_type = [];
    
    public function __construct($from_date, $to_date,$datefilter, $selectedIds = [], $selectedFields = [], $city = [], $zone = [],$status=[] , $accountability_type = [],$vehicle_model =[],$vehicle_type=[],$vehicle_make =[])
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->datefilter        = $datefilter;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = $city;
        $this->zone           = $zone;
        $this->status           = $status;
        $this->accountability_type           = (array)$accountability_type;
        $this->vehicle_model           = (array)$vehicle_model;
        $this->vehicle_type           = (array)$vehicle_type;
        $this->vehicle_make           = (array)$vehicle_make;
    }

    public function collection()
    {
         $guard = Auth::guard('master')->check() ? 'master' : 'zone';
                    $user  = Auth::guard($guard)->user();
                    $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                    
        $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
                    ->where('city_id', $user->city_id)
                    ->pluck('id');
                    
        $query = B2BServiceRequest::with([
            'assignment.VehicleRequest.city',
            'assignment.VehicleRequest.zone',
            'assignment.vehicle',
            'assignment.VehicleRequest',
            'assignment.rider.customerlogin.customer_relation'
        ]);
        $query->whereHas('assignment.VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds) {
                    // Always filter by created_by if IDs exist
                        if ($customerLoginIds->isNotEmpty()) {
                            $q->whereIn('created_by', $customerLoginIds);
                        }
                    
                        // Apply guard-specific filters
                        if ($guard === 'master') {
                            $q->where('city_id', $user->city_id);
                        }
                    
                        if ($guard === 'zone') {
                            $q->where('city_id', $user->city_id)
                              ->where('zone_id', $user->zone_id);
                        }
                        
                        if (!empty(array_filter($this->accountability_type))) {
                            $q->where('account_ability_type', $this->accountability_type);
                        }
                        
                    });
                    
        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        } else {
             if (!empty(array_filter($this->city))) {
                $query->whereHas('assignment.VehicleRequest', function ($q) {
                    $q->where('city_id', [$this->city]);
                });
            }

            if (!empty(array_filter($this->zone))) {
                $query->whereHas('assignment.VehicleRequest', function ($q) {
                    $q->where('zone_id', $this->zone);
                });
            }
            
            if (!empty(array_filter($this->status))) {
                $query->whereIn('status', $this->status);
            }
            
            if ($this->from_date) {
                $query->whereDate('created_at', '>=', $this->from_date);
            }

            if ($this->to_date) {
                $query->whereDate('created_at', '<=', $this->to_date);
            }
            
            if (!empty(array_filter($this->vehicle_model))) {
                $query->whereHas('assignment.vehicle', function ($q) {
                    $q->whereIn('model', $this->vehicle_model);
                });
            }

            if (!empty(array_filter($this->vehicle_type))) {
                $query->whereHas('assignment.vehicle', function ($q) {
                    $q->whereIn('vehicle_type', $this->vehicle_type);
                });
            }

            if (!empty(array_filter($this->vehicle_make))) {
                $query->whereHas('assignment.vehicle.vehicle_model_relation', function ($q) {
                    $q->whereIn('make', $this->vehicle_make);
                });
            }
            
            if (!empty($this->datefilter)) {
                switch ($this->datefilter) {
                    case 'today':
                        $from = Carbon::today()->toDateString();
                        $to   = Carbon::today()->toDateString();
                        break;
                    
                    case 'week':
                        $from = Carbon::now()->startOfWeek()->toDateString();
                        $to   = Carbon::now()->endOfWeek()->toDateString();
                        break;
                            
                    case 'last_15_days':
                        $from = Carbon::now()->subDays(14)->startOfDay()->toDateString(); // 15 days including today
                        $to   = Carbon::now()->endOfDay()->toDateString();
                        break;
                    case 'month':
                        $from = Carbon::now()->startOfMonth()->toDateString();
                        $to   = Carbon::now()->endOfMonth()->toDateString();
                        break;
                    
                    case 'year':
                        $from = Carbon::now()->startOfYear()->toDateString();
                        $to   = Carbon::now()->endOfYear()->toDateString();
                        break;
                    
                    case 'custom':
                        default:
                        break;
                        }
                
                if(!empty($from) && !empty($to)){
                    $query->whereBetween('created_at', [$from, $to]);

                }
                      
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
                    
                case 'rider_name':
                    $mapped[] = $row->assignment->rider->name ?? '-';
                    break;
                    
                case 'accountability_type':
                        $mapped[] = $row->assignment->VehicleRequest->accountAbilityRelation->name ?? '-';
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
                    
                case 'mobile_no':
                    $mapped[] = $row->assignment->rider->mobile_no ?? '-';
                    break;

                // case 'client':
                //     $mapped[] = $row->assignment->rider->customerlogin->customer_relation->trade_name ?? '-';
                //     break;

                case 'city':
                    $mapped[] = $row->assignment->VehicleRequest->city->city_name ?? '-';
                    break;

                case 'zone':
                    $mapped[] = $row->assignment->VehicleRequest->zone->name ?? '-';
                    break;

                case 'aging':
                    $mapped[] = $aging ?? '-';
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
            'vehicle_no'    => 'Vehicle Number',
            'chassis_number'=> 'Chassis Number',
            'vehicle_model' => 'Vehicle Model',
            'vehicle_make'  => 'Vehicle Make',
            'vehicle_type'  => 'Vehicle Type',
            'rider_name'    => 'Rider Name',
            'mobile_no'     => 'Mobile No',
            'accountability_type'     => 'Accountability Type',
            // 'client'        => 'Client Name',
            'city'          => 'City',
            'zone'          => 'Zone',
            // 'poc_name'      => 'POC Name',
            // 'poc_number'    => 'POC Number',
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
