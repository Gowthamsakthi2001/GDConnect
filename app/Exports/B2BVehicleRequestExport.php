<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\B2B\Entities\B2BVehicleRequests;
use Illuminate\Support\Facades\DB;
use Modules\MasterManagement\Entities\CustomerLogin;
use Illuminate\Support\Facades\Auth;
use Modules\B2B\Entities\B2BRider;

class B2BVehicleRequestExport implements FromCollection, WithHeadings, WithMapping
{
    protected $status;
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;

    public function __construct($status, $from_date, $to_date, $selectedIds = [], $selectedFields = [])
    {
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->selectedIds = $selectedIds;
        $this->selectedFields = $selectedFields;
    }

    public function collection()
    {
        
        $guard = Auth::guard('master')->check() ? 'master' : 'zone';
        $user  = Auth::guard($guard)->user();
        $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
    
        $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
            ->where('city_id', $user->city_id)
            ->pluck('id');
        
        
      $query = B2BVehicleRequests::with('rider');
       
        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        } else {
            
             if ($customerLoginIds->isNotEmpty()) {
                $query->whereIn('created_by', $customerLoginIds);
            }

            if ($guard === 'master') {
                $query->where('city_id', $user->city_id);
            }

            if ($guard === 'zone') {
                $query->where('city_id', $user->city_id)
                  ->where('zone_id', $user->zone_id);
            }

            
            if (in_array($this->status, ['pending', 'completed'])) {
                $query->where('status', $this->status);
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
        
            foreach ($this->selectedFields as $field) {
                $key = $field['name'];
        
                switch ($key) {
                    // Aadhaar
                    case 'adhar_front':
                        $mapped[] = $row->rider->adhar_front ? asset('b2b/aadhar_images/' . $row->rider->adhar_front) : '-';
                        break;
                    case 'adhar_back':
                        $mapped[] = $row->rider->adhar_back ? asset('b2b/aadhar_images/' . $row->rider->adhar_back) : '-';
                        break;
        
                    // PAN
                    case 'pan_front':
                        $mapped[] = $row->rider->pan_front ? asset('b2b/pan_images/' . $row->rider->pan_front) : '-';
                        break;
                    case 'pan_back':
                        $mapped[] = $row->rider->pan_back ? asset('b2b/pan_images/' . $row->rider->pan_back) : '-';
                        break;
        
                    // Driving License
                    case 'driving_license_front':
                        $mapped[] = $row->rider->driving_license_front ? asset('b2b/driving_license_images/' . $row->rider->driving_license_front) : '-';
                        break;
                    case 'driving_license_back':
                        $mapped[] = $row->rider->driving_license_back ? asset('b2b/driving_license_images/' . $row->rider->driving_license_back) : '-';
                        break;
        
                    // LLR
                    case 'llr_image':
                        $mapped[] = $row->rider->llr_image ? asset('b2b/llr_images/' . $row->rider->llr_image) : '-';
                        break;
        
                    case 'name':
                        $mapped[] = $row->rider->name ?? '-';
                        break;
                        
                     case 'mobile_no':
                        $mapped[] = $row->rider->mobile_no ?? '-';
                        break;
                    case 'email':
                        $mapped[] = $row->rider->email ?? '-';
                        break;
                        
                     case 'dob':
                        $mapped[] = $row->rider->dob ?? '-';
                        break;
                        
                        
                    case 'adhar_number':
                        $mapped[] = $row->rider->adhar_number ?? '-';
                        break;
                        
                    case 'pan_number':
                        $mapped[] = $row->rider->pan_number ?? '-';
                        break;
                        
                    case 'driving_license_number':
                        $mapped[] = $row->rider->driving_license_number ?? '-';
                        break;
                        
                    case 'llr_number':
                        $mapped[] = $row->rider->llr_number ?? '-';
                        break;
                        
                    case 'created_at':
                        $mapped[] = $row->$key ? \Carbon\Carbon::parse($row->rider->$key)->format('d M Y h:i A') : '-';
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
            'req_id'                => 'Request ID',
            'name'                  => 'Rider Name',
            'mobile_no'             => 'Mobile Number',
            'email'                 => 'Email',
            'dob'                   => 'Date of Birth',
            // 'vehicle_duration_type' => 'Vehicle Duration',
            'start_date'            => 'Start Date',
            'end_date'              => 'End Date',
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
            'status'                => 'Status',
            'created_at'            => 'Created At',
        ];

        foreach ($this->selectedFields as $field) {
            $headers[] = $customHeadings[$field['name']] ?? ucfirst(str_replace('_', ' ', $field['name']));
        }

        return $headers;
    }


}
