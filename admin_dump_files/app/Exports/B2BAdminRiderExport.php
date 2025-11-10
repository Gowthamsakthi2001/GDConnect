<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\B2B\Entities\B2BRider;
use Illuminate\Support\Facades\Auth;
use Modules\MasterManagement\Entities\CustomerLogin;
class B2BAdminRiderExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;

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
    //     $guard = Auth::guard('master')->check() ? 'master' : 'zone';
    // $user  = Auth::guard($guard)->user();
    // $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');

    // $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
    //     ->where('city_id', $user->city_id)
    //     ->pluck('id');
        $query = B2BRider::with('customerLogin','vehicleRequest');

        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        } else {
            
            // if ($customerLoginIds->isNotEmpty()) {
            //         $query->whereIn('created_by', $customerLoginIds);
            //     }
            
            // if ($guard === 'master') {
            //     $query->where('createdby_city', $user->city_id);
            // }

            // if ($guard === 'zone') {
            //     $query->where('createdby_city', $user->city_id)
            //       ->where('assign_zone_id', $user->zone_id);
            // }
            
            
            if ($this->city) {
                // Zone: filter by city + zone
            $query->where('createdby_city', $this->city);
            }
                    
            if ($this->zone) {
                // Zone: filter by city + zone
                $query->where('assign_zone_id', $this->zone);
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
    
        foreach ($this->selectedFields as $key) {  // now $key is just "email", "dob", etc.
            switch ($key) {
                case 'adhar_front':
                    $mapped[] = $row->adhar_front ? asset('b2b/aadhar_images/' . $row->adhar_front) : '-';
                    break;
                case 'adhar_back':
                    $mapped[] = $row->adhar_back ? asset('b2b/aadhar_images/' . $row->adhar_back) : '-';
                    break;
    
                case 'pan_front':
                    $mapped[] = $row->pan_front ? asset('b2b/pan_images/' . $row->pan_front) : '-';
                    break;
                case 'pan_back':
                    $mapped[] = $row->pan_back ? asset('b2b/pan_images/' . $row->pan_back) : '-';
                    break;
    
                case 'driving_license_front':
                    $mapped[] = $row->driving_license_front ? asset('b2b/driving_license_images/' . $row->driving_license_front) : '-';
                    break;
                case 'driving_license_back':
                    $mapped[] = $row->driving_license_back ? asset('b2b/driving_license_images/' . $row->driving_license_back) : '-';
                    break;
    
                case 'llr_image':
                    $mapped[] = $row->llr_image ? asset('b2b/llr_images/' . $row->llr_image) : '-';
                    break;
    
                case 'created_at':
                    $mapped[] = $row->$key ? \Carbon\Carbon::parse($row->$key)->format('d M Y h:i A') : '-';
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
            'created_at'            => 'Created At',
        ];
    
        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }
    
        return $headers;
    }

    
}
