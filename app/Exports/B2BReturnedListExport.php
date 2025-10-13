<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\B2B\Entities\B2BReturnRequest;
use Illuminate\Support\Facades\Auth;
use Modules\MasterManagement\Entities\CustomerLogin;

class B2BReturnedListExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;
    protected $city;
    protected $zone;

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = null, $zone = null)
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = $city;
        $this->zone           = $zone;
    }

    public function collection()
    {
        $guard = Auth::guard('master')->check() ? 'master' : 'zone';
        $user  = Auth::guard($guard)->user();
        $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                    
        $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
            ->where('city_id', $user->city_id)
            ->pluck('id');
            
        $query = B2BReturnRequest::with([
            'assignment.VehicleRequest.city',
            'assignment.VehicleRequest.zone',
            'assignment.vehicle',
            'assignment.VehicleRequest',
            'assignment.rider.customerlogin.customer_relation',
            'agent'
        ])->where('status','closed');
        
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
                    });
                    
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
                
                // case 'kilometer_value':
                //     $mapped[] = $row->kilometer_value ?? '-';
                //     break;
                    
                case 'odometer_value':
                    $mapped[] = $row->kilometer_value ?? '-';
                    break;
                    
                case 'odometer_image':
                    $mapped[] = $row->kilometer_image ?asset('b2b/kilometer_images/' . $row->kilometer_image): '-';
                    break;
                    
                // case 'odometer_image':
                //     $mapped[] = $row->odometer_image ?asset('b2b/odometer_images/' . $row->odometer_image): '-';
                //     break;
                    
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
        ];

        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }
}
