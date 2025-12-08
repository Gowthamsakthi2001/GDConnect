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

class B2BAdminDeployedAssetExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;
    protected $city = [];
    protected $zone= [];
    protected $status= [];
    protected $vehicle_model= [];
    protected $vehicle_type= [];
    protected $vehicle_make= [];
    protected $date_filter;
    protected $accountability_type= [];
    protected $customer_id= [];

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = [], $zone = [], $status = []  ,$accountability_type=[],$customer_id=[], $vehicle_type= [] , $vehicle_model= [],$vehicle_make= [], $date_filter= [])    {
        
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = $city;
        $this->zone           = $zone;
        $this->status         = $status;
        $this->vehicle_type         = $vehicle_type;
        $this->vehicle_model         = $vehicle_model;
        $this->vehicle_make        = $vehicle_make;
        $this->date_filter         = $date_filter;
        $this->accountability_type         = $accountability_type;
        $this->customer_id         = $customer_id;
        
    }

public function collection()
{
    // Base query using VehicleAssignment
    // $query = B2BVehicleAssignment::where('status','!=','returned')->with([
    $query = B2BVehicleAssignment::with([ // updated by Gowtham.S
        'rider',
        'vehicle.vehicle_type_relation',
        'vehicle.vehicle_model_relation',
        'VehicleRequest',       
        'vehicle.quality_check',
        'VehicleRequest.city', 
        'VehicleRequest.zone',// eager load city through request
        'VehicleRequest.rider.customerLogin.customer_relation',
        'agent_relation',
    ]);

    if (!empty($this->selectedIds)) {
        $query->whereIn('id', $this->selectedIds);
    } else {
        
        $query->whereHas('VehicleRequest', function ($q) {

            if (!empty($this->city) && !in_array('all',$this->city)) {
                $q->whereIn('city_id', $this->city);
            }

            if (!empty($this->zone) && !in_array('all',$this->zone)) {
                $q->whereIn('zone_id', $this->zone);
            }

            // if (!empty($this->city) && !in_array('all',$this->city)) {
            //     $q->where('status', $this->status);
            // }


        });
    }

    //  if ($this->from_date != "" && $this->to_date != "") {
    //     $query->whereDate('created_at', '>=', $this->from_date)
    //           ->whereDate('created_at', '<=', $this->to_date);
    // }
    
            if ($this->date_filter) {
        
            switch ($this->date_filter) {
                
                case 'today':
                    $query->whereDate('created_at', now()->toDateString());
                    break;
        
                case 'week':
                    $query->whereDate('created_at', '>=', now()->startOfWeek())
                          ->whereDate('created_at', '<=', now()->endOfWeek());
                    break;
                
                case 'last_15_days':
                        $query->whereMonth('created_at', now()->subDays(14)->startOfDay())
                              ->whereYear('created_at', now()->endOfDay());
                        break;
                case 'month':
                    $query->whereDate('created_at', '>=', now()->startOfMonth())
                          ->whereDate('created_at', '<=', now()->endOfMonth());
                    break;
        
                case 'year':
                    $query->whereDate('created_at', '>=', now()->startOfYear())
                          ->whereDate('created_at', '<=', now()->endOfYear());
                    break;
        
                case 'custom':

                    if ($this->from_date && $this->to_date) {
                        $query->whereDate('created_at', '>=', $this->from_date)
                              ->whereDate('created_at', '<=', $this->to_date);
                    }
                    break;
            }
        }

        if (!empty($this->vehicle_type) && !in_array('all',$this->vehicle_type)) {
            $query->whereHas('vehicle.quality_check', function ($q) {
                $q->where('vehicle_type', $this->vehicle_type);
            });
        }

        // Vehicle Model Filter
        if (!empty($this->vehicle_model) && !in_array('all',$this->vehicle_model)) {
            $query->whereHas('vehicle.quality_check', function ($q) {
                $q->where('vehicle_model', $this->vehicle_model);
            });
        }
        
        if (!empty($this->vehicle_make) && !in_array('all',$this->vehicle_make)) {
            $query->whereHas('vehicle.quality_check.vehicle_model_relation', function ($q) {
                $q->where('make', $this->vehicle_make);
            });
        }
        
        if (!empty($this->accountability_type) && !in_array('all',$this->accountability_type)) {
            $query->whereHas('vehicle.quality_check', function ($q) {
                $q->where('accountability_type', $this->accountability_type);
            });
        }
        
        if (!empty($this->customer_id) && !in_array('all',$this->customer_id)) {
            $customer_id = $this->customer_id;
        
            if (in_array(1,$this->accountability_type)) {
        
                // Accountability = 1 → Filter using vehicle.client
                $query->whereHas('vehicle', function ($q) use ($customer_id) {
                    $q->whereIn('client', $customer_id);
                });
        
            } else {
        
                // Accountability = 2 → Filter using quality_check.customer_id
                $query->whereHas('vehicle.quality_check', function ($q) use ($customer_id) {
                    $q->whereIn('customer_id', $customer_id);
                });
        
            }
        }
        if(!empty($this->status) && !in_array('all',$this->status)){
             $query->whereIn('status',$this->status);
        }
       
      $data = $query->orderBy('id', 'desc')
                      ->get()
                      ->unique('asset_vehicle_id')
                      ->values(); // reset array keys
    return $data;
    
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
                    $mapped[] = $row->asset_vehicle_id ?? '-';
                    break;

                case 'chassis_number':
                    $mapped[] = $row->vehicle->chassis_number ?? '-';
                    break;

                case 'vehicle_type':
                    $mapped[] = $row->vehicle->vehicle_type_relation->name ?? '-';
                    break;
                
                case 'vehicle_model':
                    $mapped[] = $row->vehicle->vehicle_model_relation->vehicle_model ?? '-';
                    break;
                
                case 'vehicle_make':
                    $mapped[] = $row->vehicle->vehicle_model_relation->make ?? '-';
                    break;
                    
                case 'contract_start_date':
                    
                    $contract_start_date = $row->VehicleRequest->rider->customerlogin->customer_relation->start_date ?? '';
                    $contract_start_date_format = 'N/A';
                    
                    if (!empty($contract_start_date)) {
                        $contract_start_date_format = \Carbon\Carbon::parse($contract_start_date)->format('d M Y');
                    }
                    
                    $mapped[] = $contract_start_date_format; //updated by Gowtham.S
                    break;
                
                case 'contract_expiry_date':
                    
                    $contract_end_date = $row->VehicleRequest->rider->customerlogin->customer_relation->end_date ?? '';
                    $contract_end_date_format = 'N/A';
                    
                    if (!empty($contract_end_date)) {
                        $contract_end_date_format = \Carbon\Carbon::parse($contract_end_date)->format('d M Y');
                    }
                    
                    $mapped[] = $contract_end_date_format;
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

                case 'agent_name':
                    $mapped[] = $row->agent_relation->name ?? '-';
                    break;
                    
                case 'agent_email':
                    $mapped[] = $row->agent_relation->email ?? '-';
                    break;
                    
                case 'agent_address':
                    $mapped[] = $row->agent_relation->address ?? '-';
                    break;    
                    
                    
                case 'zone':
                    $mapped[] = $row->VehicleRequest->zone->name ?? '-';
                    break;

                case 'status':
                    $mapped[] = ucwords(str_replace('_', ' ', $row->status ?? '-'));
                    break;
                
                case 'client_name':
                    $mapped[] = optional($row->VehicleRequest->rider->customerLogin->customer_relation)->trade_name ?? '-';
                    break;
                
                case 'client_contact':
                    $mapped[] = optional($row->VehicleRequest->rider->customerLogin->customer_relation)->phone ?? '-';
                    break;
                
                case 'client_email':
                    $mapped[] = optional($row->VehicleRequest->rider->customerLogin->customer_relation)->email ?? '-';
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
            'vehicle_type'           => 'Vehicle Type',
            'vehicle_model'           => 'Vehicle Model',
            'vehicle_make'           => 'Vehicle Make',
            'handover_type'          => 'Handover Type',
            'handover_time'          => 'Handover Time',
            'city'                   => 'City',
            'zone'                   => 'Zone',
            'status'                 => 'Status',
            'client_name'            => 'Client Name',
            'client_contact'         => 'Client Contact',
            'client_email'           => 'Client Email',
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
            'agent_name'             => 'Agent Name' ,
            'agent_email'            => 'Agent Email' ,
            'agent_address'          => 'Agent Address'
        ];

        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }
}
