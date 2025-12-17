<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Modules\MasterManagement\Entities\CustomerLogin;

class B2BVehicleListExport implements FromQuery, WithMapping, WithHeadings, WithCustomChunkSize
{
    protected $from_date;
    protected $to_date;
    protected $date_filter;
    protected $selectedIds = [];
    protected $selectedFields = [];
    protected $city = [];
    protected $zone = [];
    protected $status = [];
    protected $vehicle_model = [];
    protected $vehicle_type = [];
    protected $vehicle_make = [];
    protected $accountability_type = [];
    protected $customer_id = [];

    /**
     * Keep parameter order similar to your previous usage.
     *
     * @param string|null $date_filter
     * @param string|null $from_date
     * @param string|null $to_date
     * @param array $selectedIds
     * @param array $selectedFields   // can be array of strings or array of ['name'=>..]
     * @param array $city
     * @param array $zone
     * @param array $status
     * @param array $vehicle_model
     * @param array $vehicle_type
     * @param array $vehicle_make
     * @param array $accountability_type
     * @param array $customer_id
     */
    public function __construct(
        $date_filter = null,
        $from_date = null,
        $to_date = null,
        $selectedIds = [],
        $selectedFields = [],
        $city = [],
        $zone = [],
        $status = [],
        $vehicle_model = [],
        $vehicle_type = [],
        $vehicle_make = [],
        $accountability_type = [],
        $customer_id = []
    ) {
        $this->date_filter = $date_filter;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->selectedIds = (array) $selectedIds;
        // Normalize selectedFields: accept ['a','b'] or [ ['name'=>'a'], ... ]
        $this->selectedFields = array_map(function ($f) {
            if (is_array($f) && isset($f['name'])) return $f['name'];
            if (is_object($f) && isset($f->name)) return $f->name;
            return $f;
        }, (array) $selectedFields);

        $this->city = (array) $city;
        $this->zone = (array) $zone;
        $this->status = (array) $status;
        $this->vehicle_model = (array) $vehicle_model;
        $this->vehicle_type = (array) $vehicle_type;
        $this->vehicle_make = (array) $vehicle_make;
        $this->accountability_type = (array) $accountability_type;
        $this->customer_id = (array) $customer_id;
    }

    /**
     * Build and return the query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {   
        $guard = Auth::guard('master')->check() ? 'master' : 'zone';
        $user  = Auth::guard($guard)->user();

        $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');

        $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
            ->where('city_id', $user->city_id)
            ->pluck('id');
            
        $query = DB::table('b2b_tbl_vehicle_assignments as ass')
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
            ->leftJoin('b2b_tbl_recovery_request as rec', 'rec.assign_id', '=', 'ass.id') // guess FK; if different adjust
            ->where('vh.is_status', 'accepted');
        
         // Restrict to customer login scope
            if ($customerLoginIds->isNotEmpty()) {
                $query->whereIn('vhr.created_by', $customerLoginIds);
            }

            if ($guard === 'master') {
                $query->where('vhr.city_id', $user->city_id);
            }

            if ($guard === 'zone') {
                $query->where('vhr.city_id', $user->city_id)
                      ->where('vhr.zone_id', $user->zone_id);
            }

            if (!empty($this->zone)) {
                $query->whereIn('vhr.zone_id', $this->zone);
            }
            
        // selected ids
        if (!empty($this->selectedIds)) {
            $query->whereIn('ass.id', $this->selectedIds);
        }

        // city filter
        if (!empty($this->city) && !in_array('all', $this->city)) {
            $query->whereIn('cty.id', $this->city);
        }

        // zone filter
        if (!empty($this->zone) && !in_array('all', $this->zone)) {
            $query->whereIn('zn.id', $this->zone);
        }

        // status
        if (!empty($this->status) && !in_array('all', $this->status)) {
            $query->whereIn('ass.status', $this->status);
        }

        // vehicle_type (from qc table)
        if (!empty($this->vehicle_type) && !in_array('all', $this->vehicle_type)) {
            $query->whereIn('qc.vehicle_type', $this->vehicle_type);
        }

        // vehicle_model (from qc)
        if (!empty($this->vehicle_model) && !in_array('all', $this->vehicle_model)) {
            $query->whereIn('qc.vehicle_model', $this->vehicle_model);
        }

        // vehicle_make (from vm)
        if (!empty($this->vehicle_make) && !in_array('all', $this->vehicle_make)) {
            $query->whereIn('vm.make', $this->vehicle_make);
        }

        // accountability_type (from qc)
        if (!empty($this->accountability_type) && !in_array('all', $this->accountability_type)) {
            $query->whereIn('qc.accountability_type', $this->accountability_type);
        }

        // customer_id filter
        if (!empty($this->customer_id) && !in_array('all', $this->customer_id)) {
            if (in_array(1, $this->accountability_type)) {
                $query->whereIn('vh.client', $this->customer_id);
            } else {
                $query->whereIn('qc.customer_id', $this->customer_id);
            }
        }

        // date filters
        if (!empty($this->date_filter) && $this->date_filter !== 'all') {
            switch ($this->date_filter) {
                case 'today':
                    $query->whereDate('ass.created_at', Carbon::today());
                    break;

                case 'week':
                    $query->whereBetween('ass.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;

                case 'last_15_days':
                    $query->whereBetween('ass.created_at', [Carbon::now()->subDays(14)->startOfDay(), Carbon::now()->endOfDay()]);
                    break;

                case 'month':
                    $query->whereBetween('ass.created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    break;

                case 'year':
                    $query->whereBetween('ass.created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]);
                    break;

                case 'custom':
                    if ($this->from_date && $this->to_date) {
                        // include full day range
                        $query->whereBetween('ass.created_at', [
                            $this->from_date . ' 00:00:00',
                            $this->to_date . ' 23:59:59'
                        ]);
                    }
                    break;
            }
        }

        // Return only latest assignment per asset_vehicle_id (same as your reference)
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
                'vh.permanent_reg_number',
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
                'cm.trade_name as customer_name',
                'cm.phone as customer_contact',
                'cm.email as customer_email',
                'cm.start_date',
                'cm.end_date',
                // include recovery created_by_type for status logic if needed
                'rec.created_by_type as recovery_created_by_type'
            ]);
    }

    /**
     * Map a single row (stdClass) into array for the Excel row
     *
     * @param  \stdClass $row
     * @return array
     */
    public function map($row): array
    {
        $mapped = [];

        foreach ($this->selectedFields as $key) {
            switch ($key) {
                case 'request_id':
                    $mapped[] = $row->req_id ?? '-';
                    break;

                case 'ac_type':
                case 'accountability_type':
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
                case 'vehicle_number':
                    $mapped[] = $row->permanent_reg_number ?? '-';
                    break;
                    
                case 'vehicle_model':
                    $mapped[] = $row->vehicle_model ?? '-';
                    break;

                case 'vehicle_make':
                    $mapped[] = $row->make ?? '-';
                    break;

                case 'contract_start_date':
                    $mapped[] = $row->start_date ? Carbon::parse($row->start_date)->format('d M Y') : 'N/A';
                    break;

                case 'contract_expiry_date':
                    $mapped[] = $row->end_date ? Carbon::parse($row->end_date)->format('d M Y') : 'N/A';
                    break;

                case 'handover_type':
                    $mapped[] = $row->handover_type ?? '-';
                    break;

                case 'handover_time':
                    $mapped[] = $row->created_at ? Carbon::parse($row->created_at)->format('d M Y, h:i A') : '-';
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

                case 'customer_name':
                    $mapped[] = $row->customer_name ?? '-';
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
                case 'rider_name':
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
                    $mapped[] = $row->adhar_front ? asset('b2b/aadhar_images/' . $row->adhar_front) : '-';
                    break;

                case 'adhar_back':
                    $mapped[] = $row->adhar_back ? asset('b2b/aadhar_images/' . $row->adhar_back) : '-';
                    break;

                case 'adhar_number':
                    $mapped[] = $row->adhar_number ?? '-';
                    break;

                case 'pan_front':
                    $mapped[] = $row->pan_front ? asset('b2b/pan_images/' . $row->pan_front) : '-';
                    break;

                case 'pan_back':
                    $mapped[] = $row->pan_back ? asset('b2b/pan_images/' . $row->pan_back) : '-';
                    break;

                case 'pan_number':
                    $mapped[] = $row->pan_number ?? '-';
                    break;

                case 'driving_license_front':
                    $mapped[] = $row->driving_license_front ? asset('b2b/driving_license_images/' . $row->driving_license_front) : '-';
                    break;

                case 'driving_license_back':
                    $mapped[] = $row->driving_license_back ? asset('b2b/driving_license_images/' . $row->driving_license_back) : '-';
                    break;

                case 'driving_license_number':
                    $mapped[] = $row->driving_license_number ?? '-';
                    break;

                case 'llr_image':
                    $mapped[] = $row->llr_image ? asset('b2b/llr_images/' . $row->llr_image) : '-';
                    break;

                case 'llr_number':
                    $mapped[] = $row->llr_number ?? '-';
                    break;

                default:
                    // fallback: try to return selected key from row
                    $mapped[] = $row->{$key} ?? '-';
                    break;
            }
        }

        return $mapped;
    }

    /**
     * Headings for exported columns (order matches selectedFields)
     *
     * @return array
     */
    public function headings(): array
    {
        $headers = [];

        $customHeadings = [
            'request_id'             => 'Request ID',
            'ac_type'                => 'Accountability Type',
            'accountability_type'    => 'Accountability Type',
            'vehicle_id'             => 'Vehicle ID',
            'chassis_number'         => 'Chassis Number',
            'vehicle_type'           => 'Vehicle Type',
            'vehicle_model'          => 'Vehicle Model',
            'vehicle_make'           => 'Vehicle Make',
            'contract_start_date'    => 'Contract Start Date',
            'contract_expiry_date'   => 'Contract Expiry Date',
            'handover_type'          => 'Handover Type',
            'handover_time'          => 'Handover Date & Time',
            'city'                   => 'City',
            'zone'                   => 'Zone',
            'status'                 => 'Status',
            'customer_name'            => 'Customer Name',
            'client_contact'         => 'Client Contact',
            'client_email'           => 'Client Email',
            'agent_name'             => 'Agent Name',
            'agent_email'            => 'Agent Email',
            'agent_address'          => 'Agent Address',
            'name'                   => 'Rider Name',
            'rider_name'             => 'Rider Name',
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

    /**
     * Chunk size for query export
     *
     * @return int
     */
    public function chunkSize(): int
    {
        return 500;
    }
}
