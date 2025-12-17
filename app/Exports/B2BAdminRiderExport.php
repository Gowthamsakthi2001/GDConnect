<?php
namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Modules\B2B\Entities\B2BRider;
use Illuminate\Support\Facades\Auth;
use Modules\MasterManagement\Entities\CustomerLogin;
class B2BAdminRiderExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;
    protected $datefilter;
    protected $customer;

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = [], $zone = [] , $datefilter , $customer=[])
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = (array) $city;
        $this->zone           = (array) $zone;
        $this->datefilter           = $datefilter;
        $this->customer           = (array) $customer;
    }
    
     public function query()
    {
        $query = DB::table('b2b_tbl_riders as rider')
            ->leftJoin('ev_tbl_customer_logins as cml', 'cml.id', '=', 'rider.created_by')
            ->leftJoin('ev_tbl_customer_master as cm', 'cm.id', '=', 'cml.customer_id')
            ->select('rider.*');

        if (!empty($this->selectedIds)) {
            $query->whereIn('rider.id', $this->selectedIds);
        } else {

            if (!empty($this->city)) {
                $query->whereIn('rider.createdby_city', $this->city);
            }

            if (!empty($this->zone)) {
                $query->whereIn('rider.assign_zone_id', $this->zone);
            }

            if (!empty($this->customer)) {
                $query->whereIn('cm.id', $this->customer);
            }

            if (!empty($this->datefilter)) {
                switch ($this->datefilter) {

                    case 'today':
                        $query->whereDate('rider.created_at', today());
                        break;

                    case 'week':
                        $query->whereBetween('rider.created_at', [
                            now()->startOfWeek(),
                            now()->endOfWeek()
                        ]);
                        break;
                     case 'last_15_days':
                        $query->whereBetween('rider.created_at', [
                            now()->subDays(14)->startOfDay(),
                            now()->endOfDay()
                        ]);
                        break;
                    case 'month':
                        $query->whereMonth('rider.created_at', now()->month)
                              ->whereYear('rider.created_at', now()->year);
                        break;

                    case 'year':
                        $query->whereYear('rider.created_at', now()->year);
                        break;

                    case 'custom':
                        if ($this->from_date) {
                            $query->whereDate('rider.created_at', '>=', $this->from_date);
                        }
                        if ($this->to_date) {
                            $query->whereDate('rider.created_at', '<=', $this->to_date);
                        }
                        break;
                }
            }
        }

        return $query->orderBy('rider.id', 'desc');
    }

        public function map($row): array
    {
        $mapped = [];
    
        foreach ($this->selectedFields as $key) {  
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
    
    public function chunkSize(): int
    {
        return 1000;
    }

    
}
