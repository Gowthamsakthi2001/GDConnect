<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\B2B\Entities\B2BVehicleRequests;
use Modules\B2B\Entities\B2BVehicleAssignment;
use Illuminate\Support\Facades\Auth;
use Modules\MasterManagement\Entities\CustomerLogin;
use Carbon\Carbon;

class B2BVehicleListExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;
    protected $city;
    protected $zone;
    protected $status;
    protected $accountability_type;

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = null, $zone = null, $status = null , $accountability_type = null)
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = $city;
        $this->zone           = $zone;
        $this->status         = $status;
        $this->accountability_type = $accountability_type;
    }

public function collection()
{
    $guard = Auth::guard('master')->check() ? 'master' : 'zone';
    $user  = Auth::guard($guard)->user();
    $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');

    $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
        ->where('city_id', $user->city_id)
        ->pluck('id');
    
    // Base query using VehicleAssignment
    $query = B2BVehicleAssignment::whereNotIn('status', ['returned'])->with([
        'rider',
        'vehicle.vehicle_type_relation',
        'VehicleRequest',              // include the request relation
        'VehicleRequest.city', 
        'VehicleRequest.zone',// eager load city through request
        'VehicleRequest.rider.customerLogin.customer_relation',
        'recovery_Request',
    ]);

    if (!empty($this->selectedIds)) {
        $query->whereIn('id', $this->selectedIds);
    } else {
        // Filter through VehicleRequest relation
        $query->whereHas('VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds) {
            if ($customerLoginIds->isNotEmpty()) {
                $q->whereIn('created_by', $customerLoginIds);
            }

            if ($this->accountability_type) {
                $q->where('account_ability_type', $this->accountability_type);
            }
            
            
            if ($guard === 'master') {
                $q->where('city_id', $user->city_id);
            }

            if ($guard === 'zone') {
                $q->where('city_id', $user->city_id)
                  ->where('zone_id', $user->zone_id);
            }

            if ($this->city) {
                $q->where('city_id', $this->city);
            }

            if ($this->zone) {
                $q->where('zone_id', $this->zone);
            }

  
        });
        
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }
        
        if (!empty($this->from_date)) {
            $query->whereDate('created_at', '>=', $this->from_date);
        }
        
        if (!empty($this->to_date)) {
            $query->whereDate('created_at', '<=', $this->to_date);
        }

    }
    $data = $query->orderBy('id', 'desc')->get();
    
 
    return $query->orderBy('id', 'desc')->get();
    
}


    public function map($row): array
    {
        $mapped = [];

        foreach ($this->selectedFields as $key) {
            switch ($key) {
                case 'request_id':
                    $mapped[] = $row->req_id ?? '-';
                    break;

                case 'vehicle_id':
                    $mapped[] = $row->vehicle->vehicle_id ?? '-';
                    break;

                case 'chassis_number':
                    $mapped[] = $row->vehicle->chassis_number ?? '-';
                    break;

                case 'vehicle_type':
                    $mapped[] = $row->vehicle->vehicle_type_relation->name ?? '-';
                    break;


                case 'vehicle_number':
                    $mapped[] = $row->vehicle->permanent_reg_number ?? '-';
                    break;
                case 'vehicle_model':
                    $mapped[] = $row->vehicle->vehicle_model_relation->vehicle_model ?? '-';
                    break;
                case 'vehicle_make':
                    $mapped[] = $row->vehicle->vehicle_model_relation->make ?? '-';
                    break;
                    
                case 'customer_name':
                    $mapped[] = $row->VehicleRequest->customerLogin->customer_relation->trade_name ?? '-';
                    break;
                case 'handover_type':
                    $mapped[] = $row->handover_type ?? '-';
                    break;

                case 'handover_time':
                    $mapped[] = $row->created_at ? Carbon::parse($row->created_at)->format('d M Y, h:i A') : '-';
                    break;

                case 'city':
                    $mapped[] = $row->VehicleRequest->city->city_name ?? '-';
                    break;

                case 'zone':
                    $mapped[] = $row->VehicleRequest->zone->name ?? '-';
                    break;
                    
                case 'accountability_type':
                        $mapped[] = $row->VehicleRequest->accountAbilityRelation->name ?? '-';
                        break;

                case 'status':
                    if ($row->status === 'recovery_request') {
                        $creator = $row->recovery_Request->created_by_type ?? null;
                        $statusText = ($creator === 'b2b-admin-dashboard')
                            ? 'GDM Recovery Initiated'
                            : 'Client Recovery Initiated';
                    } else {
                        $statusText = ucwords(str_replace('_', ' ', $row->status ?? '-'));
                    }
                    $mapped[] = $statusText;
                    break;



                case 'name':
                    $mapped[] = optional($row->rider)->name ?? '-';
                    break;

                case 'mobile_no':
                    $mapped[] = optional($row->rider)->mobile_no ?? '-';
                    break;

                case 'email':
                    $mapped[] = optional($row->rider)->email ?? '-';
                    break;

                case 'dob':
                    $mapped[] = optional($row->rider)->dob ?? '-';
                    break;

                case 'adhar_front':
                    $mapped[] = optional($row->rider)->adhar_front 
                        ? asset('b2b/aadhar_images/' . $row->rider->adhar_front) : '-';
                    break;

                case 'adhar_back':
                    $mapped[] = optional($row->rider)->adhar_back 
                        ? asset('b2b/aadhar_images/' . $row->rider->adhar_back) : '-';
                    break;

                case 'adhar_number':
                    $mapped[] = optional($row->rider)->adhar_number ?? '-';
                    break;

                case 'pan_front':
                    $mapped[] = optional($row->rider)->pan_front 
                        ? asset('b2b/pan_images/' . $row->rider->pan_front) : '-';
                    break;

                case 'pan_back':
                    $mapped[] = optional($row->rider)->pan_back 
                        ? asset('b2b/pan_images/' . $row->rider->pan_back) : '-';
                    break;

                case 'pan_number':
                    $mapped[] = optional($row->rider)->pan_number ?? '-';
                    break;

                case 'driving_license_front':
                    $mapped[] = optional($row->rider)->driving_license_front 
                        ? asset('b2b/driving_license_images/' . $row->rider->driving_license_front) : '-';
                    break;

                case 'driving_license_back':
                    $mapped[] = optional($row->rider)->driving_license_back 
                        ? asset('b2b/driving_license_images/' . $row->rider->driving_license_back) : '-';
                    break;

                case 'driving_license_number':
                    $mapped[] = optional($row->rider)->driving_license_number ?? '-';
                    break;

                case 'llr_image':
                    $mapped[] = optional($row->rider)->llr_image 
                        ? asset('b2b/llr_images/' . $row->rider->llr_image) : '-';
                    break;

                case 'llr_number':
                    $mapped[] = optional($row->rider)->llr_number ?? '-';
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
            'request_id'             => 'Request ID',
            'vehicle_id'             => 'Vehicle ID',
            'chassis_number'         => 'Chassis Number',
            'vehicle_number'         => 'Vehicle Number',
            'vehicle_model'         => 'Vehicle Model',
            'vehicle_make'           => 'Vehicle Make',
            'vehicle_type'           => 'Vehicle Type',
            'handover_type'          => 'Handover Type',
            'handover_time'          => 'Handover Date & Time',
            'city'                   => 'City',
            'zone'                   => 'Zone',
            'status'                 => 'Status', 
            'customer_name'          => 'Customer Name' ,
            'name'                   => 'Rider Name',
            'mobile_no'              => 'Mobile Number',
            'email'                  => 'Email',
            'dob'                    => 'Date of Birth',
            'adhar_front'            => 'Aadhaar Front',
            'adhar_back'             => 'Aadhaar Back',
            'adhar_number'           => 'Aadhaar Number',
            'pan_front'              => 'PAN Front',
            'pan_back'               => 'PAN Back',
            'pan_number'             => 'PAN Number',
            'driving_license_front'  => 'Driving License Front',
            'driving_license_back'   => 'Driving License Back',
            'driving_license_number' => 'Driving License Number',
            'llr_image'              => 'LLR Image',
            'llr_number'             => 'LLR Number',
        ];

        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }
}
