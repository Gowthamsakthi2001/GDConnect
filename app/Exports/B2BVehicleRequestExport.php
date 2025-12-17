<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\MasterManagement\Entities\CustomerLogin;
use Illuminate\Support\Facades\Auth;

class B2BVehicleRequestExport implements FromQuery, WithHeadings, WithMapping
{
    protected $status;
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;
    protected $accountability_type;
    protected $zone_id;
    protected $datefilter;

    public function __construct($status,$datefilter, $from_date, $to_date, $selectedIds = [], $selectedFields = [] , $accountability_type = [],$zone_id = [])
    {
        $this->status               = $status;
        $this->datefilter           = $datefilter;
        $this->from_date            = $from_date;
        $this->to_date              = $to_date;
        $this->selectedIds          = $selectedIds;
        $this->selectedFields       = $selectedFields;
        $this->accountability_type  = (array) $accountability_type;
        $this->zone_id              = (array) $zone_id;
    }


    # =====================================================
    #                     MAIN QUERY
    # =====================================================
    public function query()
    {   
         $guard = Auth::guard('master')->check() ? 'master' : 'zone';
        $user  = Auth::guard($guard)->user();

        $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');

        $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
            ->where('city_id', $user->city_id)
            ->pluck('id');
        
        
        $query = DB::table('b2b_tbl_vehicle_requests as vr')
            ->leftJoin('b2b_tbl_riders as r', 'r.id', '=', 'vr.rider_id')
            ->leftJoin('zones as zn', 'zn.id', '=', 'vr.zone_id')
            ->leftJoin('ev_tbl_city as cty', 'cty.id', '=', 'vr.city_id')
            ->leftJoin('ev_tbl_customer_logins as cl', 'r.created_by', '=', 'cl.id')
            ->leftJoin('ev_tbl_accountability_types as ac', 'ac.id', '=', 'vr.account_ability_type');
        
         if ($customerLoginIds->isNotEmpty()) {
                $query->whereIn('vr.created_by', $customerLoginIds);
            }

            if ($guard === 'master') {
                $query->where('vr.city_id', $user->city_id);
            }

            if ($guard === 'zone') {
                $query->where('vr.city_id', $user->city_id)
                      ->where('vr.zone_id', $user->zone_id);
            }

            if (!empty($this->zone)) {
                $query->whereIn('vr.zone_id', $this->zone);
            }
            
        # ===========================================
        # Selected IDs
        # ===========================================
        if (!empty($this->selectedIds)) {
            $query->whereIn('vr.id', $this->selectedIds);
        }

        # ===========================================
        # Status filter
        # ===========================================
        if (!empty($this->status) && in_array($this->status, ['pending','completed'])) {
            $query->where('vr.status', $this->status);
        }

        # ===========================================
        # Accountability filter
        # ===========================================
        if (!empty($this->accountability_type)) {
            $query->whereIn('vr.account_ability_type', $this->accountability_type);
        }

        # ===========================================
        # Zone filter
        # ===========================================
        if (!empty($this->zone_id)) {
            $query->whereIn('vr.zone_id', $this->zone_id);
        }

        # ===========================================
        # DATE FILTERS
        # ===========================================
        if (!empty($this->datefilter)) {
            switch ($this->datefilter) {
                case 'today':
                    $query->whereDate('vr.created_at', Carbon::today());
                    break;

                case 'week':
                    $query->whereBetween('vr.created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;

                case 'month':
                    $query->whereBetween('vr.created_at', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    ]);
                    break;
                case 'last_15_days':
                    $query->whereBetween('vr.created_at', [
                        Carbon::now()->subDays(15)->startOfDay(),
                        Carbon::now()->endOfDay()
                    ]);
                    break;
                case 'year':
                    $query->whereBetween('vr.created_at', [
                        Carbon::now()->startOfYear(),
                        Carbon::now()->endOfYear()
                    ]);
                    break;

                case 'custom':
                    if ($this->from_date && $this->to_date) {
                        $query->whereBetween('vr.created_at', [
                            $this->from_date . " 00:00:00",
                            $this->to_date . " 23:59:59"
                        ]);
                    }
                    break;
            }
        }

        return $query->orderBy('vr.id','desc')
        ->select([
            'vr.id',
            'vr.req_id',
            'vr.status',
            'vr.created_at',
            'vr.updated_at',
            'vr.account_ability_type',
            'vr.zone_id',
            'vr.completed_at',
            'zn.name as zone_name',
            'cty.city_name',
            'r.name as rider_name',
            'r.mobile_no',
            'r.email',
            'r.dob',
            'r.adhar_front',
            'r.adhar_back',
            'r.adhar_number',
            'r.pan_front',
            'r.pan_back',
            'r.pan_number',
            'r.driving_license_front',
            'r.driving_license_back',
            'r.driving_license_number',
            'r.llr_image',
            'r.llr_number',
            'ac.name as accountability_type',
            'cl.email as created_by_login'
        ]);
    }


    # =====================================================
    #                      MAP
    # =====================================================
    public function map($row): array
    {
        $mapped = [];

        foreach ($this->selectedFields as $field) {

            $key = $field['name'];

            switch ($key) {

                case 'name':
                    $mapped[] = $row->rider_name ?? '-';
                    break;
                
                case 'accountability_type':
                    $mapped[] = $row->accountability_type ?? '-';
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
                    $mapped[] = $row->adhar_front ? asset("b2b/aadhar_images/$row->adhar_front") : '-';
                    break;

                case 'adhar_back':
                    $mapped[] = $row->adhar_back ? asset("b2b/aadhar_images/$row->adhar_back") : '-';
                    break;

                case 'pan_front':
                    $mapped[] = $row->pan_front ? asset("b2b/pan_images/$row->pan_front") : '-';
                    break;

                case 'pan_back':
                    $mapped[] = $row->pan_back ? asset("b2b/pan_images/$row->pan_back") : '-';
                    break;

                case 'driving_license_front':
                    $mapped[] = $row->driving_license_front ? asset("b2b/driving_license_images/$row->driving_license_front") : '-';
                    break;

                case 'driving_license_back':
                    $mapped[] = $row->driving_license_back ? asset("b2b/driving_license_images/$row->driving_license_back") : '-';
                    break;

                case 'zone_id':
                    $mapped[] = $row->zone_name ?? '-';
                    break;

                case 'city_id':
                    $mapped[] = $row->city_name ?? '-';
                    break;

                case 'created_at':
                    $mapped[] = $row->created_at ? Carbon::parse($row->created_at)->format('d M Y h:i A') : '-';
                    break;
                
                case 'created_by':
                    $mapped[] = $row->created_by_login ?? '-';
                    break;
                    
                case 'updated_at':
                    $mapped[] = $row->updated_at ? Carbon::parse($row->updated_at)->format('d M Y h:i A') : '-';
                    break;
                
                case 'aging':
                        if ($row->status === 'completed' && $row->completed_at) {
                            $created   = \Carbon\Carbon::parse($row->created_at);
                            $completed = \Carbon\Carbon::parse($row->completed_at);
                            $diffInDays = $created->diffInDays($completed);
                            $diffInHours = $created->diffInHours($completed);
                            $diffInMinutes = $created->diffInMinutes($completed);
                        } else {
                            $created   = \Carbon\Carbon::parse($row->created_at);
                            $now       = now();
                            $diffInDays = $created->diffInDays($now);
                            $diffInHours = $created->diffInHours($now);
                            $diffInMinutes = $created->diffInMinutes($now);
                        }
        
                        if ($diffInDays > 0) {
                            $aging = $diffInDays . ' days';
                        } elseif ($diffInHours > 0) {
                            $aging = $diffInHours . ' hours';
                        } else {
                            $aging = $diffInMinutes . ' mins';
                        }
        
                        $mapped[] = $aging;
                        break;
                default:
                    $mapped[] = $row->{$key} ?? '-';
            }
        }

        return $mapped;
    }


    # =====================================================
    #                HEADINGS
    # =====================================================
    public function headings(): array
    {
        $headers = [];

        $custom = [
            'req_id'    => 'Request ID',
            'name'      => 'Rider Name',
            'mobile_no' => 'Mobile Number',
            'email'     => 'Email',
            'dob'       => 'Date of Birth',
            'adhar_front' => 'Aadhaar Front',
            'adhar_back'  => 'Aadhaar Back',
            'pan_front'   => 'PAN Front',
            'pan_back'    => 'PAN Back',
            'city_id'     => 'City',
            'zone_id'     => 'Zone',
            'created_at'  => 'Created Date',
            'updated_at'  => 'Updated Date',
            'created_by'  => 'Created By',
        ];

        foreach ($this->selectedFields as $field) {
            $key = $field['name'];
            $headers[] = $custom[$key] ?? ucfirst(str_replace('_',' ', $key));
        }

        return $headers;
    }
    
    public function chunkSize(): int
    {
        return 500;
    }
}
