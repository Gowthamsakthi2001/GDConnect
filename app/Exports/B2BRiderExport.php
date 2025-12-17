<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Modules\MasterManagement\Entities\CustomerLogin;
use Carbon\Carbon;

class B2BRiderExport implements FromQuery, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;
    protected $zone;
    protected $datefilter;

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $datefilter, $zone = [])
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->zone           = (array) $zone;
        $this->datefilter     = $datefilter;
    }

    /**
     * Build the Query Using Query Builder (No Eloquent)
     */
    public function query()
    {
        $guard = Auth::guard('master')->check() ? 'master' : 'zone';
        $user  = Auth::guard($guard)->user();

        $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');

        $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
            ->where('city_id', $user->city_id)
            ->pluck('id');

        // Base Query
        $query = DB::table('b2b_tbl_riders as r')
            ->leftJoin('b2b_tbl_vehicle_requests as v', 'v.rider_id', '=', 'r.id')
            ->leftJoin('ev_tbl_city as c', 'c.id', '=', 'v.city_id')
            ->leftJoin('zones as z', 'z.id', '=', 'v.zone_id')
            ->leftJoin('ev_tbl_customer_logins as cl', 'r.created_by', '=', 'cl.id')
            ->select(
                'r.*',
                DB::raw('c.city_name as city'),
                DB::raw('cl.email as created_by_login'),
                DB::raw('z.name as zone')
            );

        // If selected IDs exist
        if (!empty($this->selectedIds)) {
            $query->whereIn('r.id', $this->selectedIds);
        } else {

            // Restrict to customer login scope
            if ($customerLoginIds->isNotEmpty()) {
                $query->whereIn('r.created_by', $customerLoginIds);
            }

            if ($guard === 'master') {
                $query->where('r.createdby_city', $user->city_id);
            }

            if ($guard === 'zone') {
                $query->where('r.createdby_city', $user->city_id)
                      ->where('r.assign_zone_id', $user->zone_id);
            }

            if (!empty($this->zone)) {
                $query->whereIn('r.assign_zone_id', $this->zone);
            }

            // Date Filters
            if (!empty($this->datefilter)) {
                switch ($this->datefilter) {
                    case 'today':
                        $this->from_date = Carbon::today()->toDateString();
                        $this->to_date   = Carbon::today()->toDateString();
                        break;
                    case 'week':
                        $this->from_date = Carbon::now()->startOfWeek()->toDateString();
                        $this->to_date   = Carbon::now()->endOfWeek()->toDateString();
                        break;
                    case 'last_15_days':
                        $this->from_date = Carbon::now()->subDays(14)->startOfDay();
                        $this->to_date = Carbon::now()->endOfDay();
                        break;
                    case 'month':
                        $this->from_date = Carbon::now()->startOfMonth()->toDateString();
                        $this->to_date   = Carbon::now()->endOfMonth()->toDateString();
                        break;
                    case 'year':
                        $this->from_date = Carbon::now()->startOfYear()->toDateString();
                        $this->to_date   = Carbon::now()->endOfYear()->toDateString();
                        break;
                }
            }

            if ($this->from_date) {
                $query->whereDate('r.created_at', '>=', $this->from_date);
            }

            if ($this->to_date) {
                $query->whereDate('r.created_at', '<=', $this->to_date);
            }
        }

        return $query->orderBy('r.id', 'desc');
    }

    /**
     * Mapping the fields selected
     */
    public function map($row): array
    {
        $mapped = [];

        foreach ($this->selectedFields as $key) {

            switch ($key) {

                case 'adhar_front':
                    $mapped[] = $row->adhar_front ? asset('b2b/aadhar_images/'.$row->adhar_front) : '-';
                    break;

                case 'adhar_back':
                    $mapped[] = $row->adhar_back ? asset('b2b/aadhar_images/'.$row->adhar_back) : '-';
                    break;

                case 'pan_front':
                    $mapped[] = $row->pan_front ? asset('b2b/pan_images/'.$row->pan_front) : '-';
                    break;

                case 'pan_back':
                    $mapped[] = $row->pan_back ? asset('b2b/pan_images/'.$row->pan_back) : '-';
                    break;

                case 'driving_license_front':
                    $mapped[] = $row->driving_license_front ? asset('b2b/driving_license_images/'.$row->driving_license_front) : '-';
                    break;

                case 'driving_license_back':
                    $mapped[] = $row->driving_license_back ? asset('b2b/driving_license_images/'.$row->driving_license_back) : '-';
                    break;

                case 'llr_image':
                    $mapped[] = $row->llr_image ? asset('b2b/llr_images/'.$row->llr_image) : '-';
                    break;

                case 'city':
                    $mapped[] = $row->city ?? '-';
                    break;
                
                case 'created_by':
                    $mapped[] = $row->created_by_login ?? '-';
                    break;
                    
                case 'zone':
                    $mapped[] = $row->zone ?? '-';
                    break;

                case 'created_at':
                    $mapped[] = $row->created_at ? Carbon::parse($row->created_at)->format('d M Y h:i A') : '-';
                    break;

                default:
                    $mapped[] = $row->{$key} ?? '-';
                    break;
            }
        }

        return $mapped;
    }

    public function headings(): array
    {
        $headers = [];

        $customHeadings = [ 
            'name'                  => 'Rider Name',
            'mobile_no'             => 'Mobile Number',
            'email'                 => 'Email',
            'dob'                   => 'Date of Birth',
            'adhar_front'           => 'Aadhaar Front',
            'adhar_back'            => 'Aadhaar Back',
            'adhar_number'          => 'Aadhaar Number',
            'pan_front'             => 'PAN Front',
            'pan_back'              => 'PAN Back',
            'pan_number'            => 'PAN Number',
            'driving_license_front' => 'Driving License Front',
            'driving_license_back'  => 'Driving License Back',
            'driving_license_number'=> 'Driving License Number',
            'llr_image'             => 'LLR Image',
            'llr_number'            => 'LLR Number',
            'city'                  => 'City',
            'zone'                  => 'Zone',
            'created_by'            => 'Created By',
            'created_at'            => 'Created At',
        ];

        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] 
                ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }
    
    public function chunkSize(): int
    {
        return 500;
    }
}
