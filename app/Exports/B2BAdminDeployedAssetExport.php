<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\B2B\Entities\B2BVehicleRequests;
use Modules\B2B\Entities\B2BVehicleAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\MasterManagement\Entities\CustomerLogin;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class B2BAdminDeployedAssetExport implements FromQuery,WithMapping,WithHeadings, WithChunkReading
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

    public function query()
    {
        $query = \DB::table('b2b_tbl_vehicle_assignments as ass')
            ->join('ev_tbl_asset_master_vehicles as vh', 'vh.id', '=', 'ass.asset_vehicle_id')
            ->leftJoin('b2b_tbl_vehicle_requests as vhr', 'vhr.req_id', '=', 'ass.req_id')
            ->leftJoin('ev_tbl_accountability_types as actype', 'actype.id', '=', 'vhr.account_ability_type')
            ->leftJoin('vehicle_qc_check_lists as qc', 'qc.id', '=', 'vh.qc_id')
            ->leftJoin('ev_tbl_vehicle_models as vm', 'vm.id', '=', 'vh.model')
            ->leftJoin('vehicle_types as vt', 'vt.id', '=', 'vh.vehicle_type')
            ->leftJoin('ev_tbl_city as cty', 'cty.id', '=', 'qc.location')
            ->leftJoin('zones as zn', 'zn.id', '=', 'qc.zone_id')
            ->leftJoin('b2b_tbl_riders as rider', 'rider.id', '=', 'ass.rider_id')
            ->leftJoin('users as agent', 'agent.id', '=', 'ass.assigned_agent_id')
            ->leftJoin('ev_tbl_customer_logins as cml', 'cml.id', '=', 'vhr.created_by')
            ->leftJoin('ev_tbl_customer_master as cm', 'cm.id', '=', 'cml.customer_id')
            ->where('vh.is_status', 'accepted');
    
        if (!empty($this->selectedIds)) {
            $query->whereIn('ass.id', $this->selectedIds);
        }
    
        if (!empty($this->city) && !in_array('all', $this->city)) {
            $query->whereIn('cty.id', $this->city);
        }
    
        if (!empty($this->zone) && !in_array('all', $this->zone)) {
            $query->whereIn('zn.id', $this->zone);
        }
    
        if (!empty($this->status) && !in_array('all', $this->status)) {
            $query->whereIn('ass.status', $this->status);
        }
    
        if (!empty($this->vehicle_type) && !in_array('all', $this->vehicle_type)) {
            $query->whereIn('qc.vehicle_type', $this->vehicle_type);
        }
    
        if (!empty($this->vehicle_model) && !in_array('all', $this->vehicle_model)) {
            $query->whereIn('qc.vehicle_model', $this->vehicle_model);
        }
    
        if (!empty($this->vehicle_make) && !in_array('all', $this->vehicle_make)) {
            $query->whereIn('vm.make', $this->vehicle_make);
        }
    
        if (!empty($this->accountability_type) && !in_array('all', $this->accountability_type)) {
            $query->whereIn('qc.accountability_type', $this->accountability_type);
        }
    
        if (!empty($this->customer_id) && !in_array('all', $this->customer_id)) {
            if (in_array(1, $this->accountability_type)) {
                $query->whereIn('vh.client', $this->customer_id);
            } else {
                $query->whereIn('qc.customer_id', $this->customer_id);
            }
        }
    
        if ($this->date_filter && $this->date_filter !== 'all') {
            switch ($this->date_filter) {
                case 'today':
                    $query->whereDate('ass.created_at', now());
                    break;
    
                case 'week':
                    $query->whereBetween('ass.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                    
                case 'last_15_days':
                    $query->whereBetween('ass.created_at', [now()->subDays(14)->startOfDay(),now()->endOfDay()]);
                    break;
    
                case 'month':
                    $query->whereBetween('ass.created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
    
                case 'year':
                    $query->whereBetween('ass.created_at', [now()->startOfYear(), now()->endOfYear()]);
                    break;
            }
            if ($this->from_date) {
                $query->whereDate('ass.created_at', '>=', $this->from_date);
            }

            if ($this->to_date) {
                $query->whereDate('ass.created_at', '<=', $this->to_date);
            }
        }
    
        return $query
            ->whereIn('ass.id', function ($q) {
                $q->selectRaw('MAX(id)')
                  ->from('b2b_tbl_vehicle_assignments')
                  ->groupBy('asset_vehicle_id');
            })
            ->orderBy('ass.id', 'desc')
            ->select([
                'ass.id',
                'ass.req_id',
                'ass.asset_vehicle_id',
                'ass.handover_type',
                'ass.created_at',
                'ass.status',
                'actype.name as accountability_type',
                'vh.chassis_number',
    
                'vt.name as vehicle_type_name',
                'vm.vehicle_model',
                'vm.make',
    
                'cty.city_name',
                'zn.name as zone_name',
    
                'agent.name as agent_name',
                'agent.email as agent_email',
                'agent.address as agent_address',
    
                'rider.name as rider_name',
                'rider.mobile_no',
                'rider.email',
                'rider.dob',
                'rider.adhar_front',
                'rider.adhar_back',
                'rider.adhar_number',
                'rider.pan_front',
                'rider.pan_back',
                'rider.pan_number',
                'rider.driving_license_front',
                'rider.driving_license_back',
                'rider.driving_license_number',
                'rider.llr_image',
                'rider.llr_number',
    
                'cm.trade_name as client_name',
                'cm.phone as client_contact',
                'cm.email as client_email',
                'cm.start_date',
                'cm.end_date',
            ]);
    }

    public function map($row): array
    {
        $mapped = [];
    
        foreach ($this->selectedFields as $key) {
    
            switch ($key) {
    
                case 'request_id':
                    $mapped[] = $row->req_id ?? '-';
                    break;
                 case 'ac_type':
                    $mapped[] = $row->accountability_type ?? '-';
                    break;
    
                case 'vehicle_id':
                    $mapped[] = $row->asset_vehicle_id ?? '-';
                    break;
    
                case 'chassis_number':
                    $mapped[] = $row->chassis_number ?? '-';
                    break;
    
                case 'vehicle_type':
                    $mapped[] = $row->vehicle_type_name ?? '-';
                    break;
    
                case 'vehicle_model':
                    $mapped[] = $row->vehicle_model ?? '-';
                    break;
    
                case 'vehicle_make':
                    $mapped[] = $row->make ?? '-';
                    break;
    
                case 'contract_start_date':
                    $mapped[] = $row->start_date 
                        ? \Carbon\Carbon::parse($row->start_date)->format('d M Y') 
                        : 'N/A';
                    break;
    
                case 'contract_expiry_date':
                    $mapped[] = $row->end_date 
                        ? \Carbon\Carbon::parse($row->end_date)->format('d M Y') 
                        : 'N/A';
                    break;
    
                case 'handover_type':
                    $mapped[] = $row->handover_type ?? '-';
                    break;
    
                case 'handover_time':
                    $mapped[] = $row->created_at 
                        ? \Carbon\Carbon::parse($row->created_at)->format('d M Y, h:i A') 
                        : '-';
                    break;
    
                case 'city':
                    $mapped[] = $row->city_name ?? '-';
                    break;
    
                case 'zone':
                    $mapped[] = $row->zone_name ?? '-';
                    break;
    
                case 'status':
                    $mapped[] = ucfirst(str_replace('_', ' ', $row->status ?? '-'));
                    break;
    
                case 'client_name':
                    $mapped[] = $row->client_name ?? '-';
                    break;
    
                case 'client_contact':
                    $mapped[] = $row->client_contact ?? '-';
                    break;
    
                case 'client_email':
                    $mapped[] = $row->client_email ?? '-';
                    break;
    
                case 'agent_name':
                    $mapped[] = $row->agent_name ?? '-';
                    break;
    
                case 'agent_email':
                    $mapped[] = $row->agent_email ?? '-';
                    break;
    
                case 'agent_address':
                    $mapped[] = $row->agent_address ?? '-';
                    break;
    
                case 'name':
                    $mapped[] = $row->rider_name ?? '-';
                    break;
    
                case 'mobile_no':
                    $mapped[] = $row->mobile_no ?? '-';
                    break;
    
                case 'email':
                    $mapped[] = $row->email ?? '-';
                    break;
    
                case 'dob':
                    $mapped[] = $row->dob ?? '-';
                    break;
    
                case 'adhar_front':
                $mapped[] = $row->adhar_front
                    ? asset('b2b/aadhar_images/' . $row->adhar_front) : '-';
                break;

            case 'adhar_back':
                $mapped[] = $row->adhar_back
                    ? asset('b2b/aadhar_images/' . $row->adhar_back) : '-';
                break;

            case 'adhar_number':
                $mapped[] = $row->adhar_number ?? '-';
                break;

            case 'pan_front':
                $mapped[] = $row->pan_front
                    ? asset('b2b/pan_images/' . $row->pan_front) : '-';
                break;

            case 'pan_back':
                $mapped[] = $row->pan_back
                    ? asset('b2b/pan_images/' . $row->pan_back) : '-';
                break;

            case 'pan_number':
                $mapped[] = $row->pan_number ?? '-';
                break;

            case 'driving_license_front':
                $mapped[] = $row->driving_license_front
                    ? asset('b2b/driving_license_images/' . $row->driving_license_front) : '-';
                break;

            case 'driving_license_back':
                $mapped[] = $row->driving_license_back
                    ? asset('b2b/driving_license_images/' . $row->driving_license_back) : '-';
                break;

            case 'driving_license_number':
                $mapped[] = $row->driving_license_number ?? '-';
                break;

            case 'llr_image':
                $mapped[] = $row->llr_image
                    ? asset('b2b/llr_images/' . $row->llr_image) : '-';
                break;

            case 'llr_number':
                $mapped[] = $row->llr_number ?? '-';
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
            'ac_type'                => 'Accountablity Type',
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
    
    public function chunkSize(): int
    {
        return 1000; 
    }

}
