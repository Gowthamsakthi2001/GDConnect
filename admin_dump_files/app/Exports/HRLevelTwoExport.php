<?php

namespace App\Exports;

use Modules\Deliveryman\Entities\Deliveryman;
use Modules\HRStatus\Entities\HRleveltwoDeliverymanAssignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class HRLevelTwoExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $fromDate;
    protected $toDate;
    protected $timeline;
    protected $selectedIds;
    protected $selectedFields;
    protected $deliveryMen;
    protected $assignments;
    protected $roleType;
    protected $status;

    public function __construct($status , $fromDate, $toDate, $timeline, $selectedIds, $selectedFields, $roleType = null)
{
    $this->fromDate = $fromDate;
    $this->toDate = $toDate;
    $this->timeline = $timeline;
    $this->selectedIds = array_filter((array)$selectedIds, function($id) {
        return !empty($id) && $id !== '';
    });
    
    // Ensure selectedFields is always an array of arrays
    $this->selectedFields = array_map(function($field) {
        return is_array($field) ? $field : ['name' => $field];
    }, (array)$selectedFields);
    
    $this->roleType = $roleType;
    $this->status = $status;
    
    $this->prepareData();
}

    protected function prepareData()
    {
        
   
        
        try {
            $assignmentsQuery = HRleveltwoDeliverymanAssignment::query()
                ->whereHas('delivery_man', function($query) {
                    // Apply role type filter if specified and not 'all'
                    if ($this->roleType && $this->roleType !== 'all') {
                        $query->where('work_type', $this->roleType);
                    }
                });


        if (!empty($this->selectedIds)) {
            $assignmentsQuery->whereIn('id', $this->selectedIds);
        } else {
            
            if ($this->status != 'total_application') {
                if ($this->status == 'pending') {
                    $assignmentsQuery->where('current_status', $this->status);
                } elseif (in_array($this->status, ['sent_to_bgv', 'sent_to_hr1'])) {
                    $assignmentsQuery->where('current_status', $this->status);
                } elseif ($this->status == 'approved_employee') {
                    $assignmentsQuery->where('current_status', 'approved')
                        ->whereHas('delivery_man', function($q) {
                            $q->where('work_type', 'in-house');
                        });
                } elseif ($this->status == 'approved_rider') {
                    $assignmentsQuery->where('current_status', 'approved')
                        ->whereHas('delivery_man', function($q) {
                            $q->where('work_type', 'deliveryman');
                        });
                } elseif ($this->status == 'reject_by_hr2') {
                    $assignmentsQuery->where('current_status', 'rejected');
                }
            }
    
         
            // Timeline filter (only if no date range specified)
            if ($this->timeline) {
                $now = now();
                switch ($this->timeline) {
                    case 'today':
                        $assignmentsQuery->whereDate('created_at', $now->toDateString());
                        break;
                    case 'this_week':
                        $assignmentsQuery->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                        break;
                    case 'this_month':
                        $assignmentsQuery->whereMonth('created_at', $now->month)
                                      ->whereYear('created_at', $now->year);
                        break;
                    case 'this_year':
                        $assignmentsQuery->whereYear('created_at', $now->year);
                        break;
                }
                
                $this->from_date = null;
                 $this->to_date = null;
            }
            else {
            if ($this->fromDate && $this->toDate) {
                $assignmentsQuery->whereBetween('created_at', [$this->fromDate, $this->toDate]);
            }
        }
            
    }

        

            // Eager load relationships
            $this->assignments = $assignmentsQuery->with([
                'delivery_man' => function($query) {
                    $query->with([
                        'current_city',
                        'vehicle_type',
                        'interest_city',
                        'RiderType'
                    ]);
                }
            ])->get();

            $this->deliveryMen = $this->assignments->pluck('delivery_man')->filter();
            
            

        } catch (\Exception $e) {
            Log::error('Export Preparation Failed: '.$e->getMessage());
            $this->assignments = collect();
            $this->deliveryMen = collect();
        }
    }
    
    public function collection()
    {
        return $this->deliveryMen;
    }
    
    public function headings(): array
    {
        return Arr::map($this->selectedFields, function($field) {
            return $this->getFieldLabel($field['name']);
        });
    }
    
    public function map($deliveryMan): array
    {
        

        return Arr::map($this->selectedFields, function($field) use ($deliveryMan) {
            return $this->getFieldValue($deliveryMan, $field['name']);
        });
    }
    
    protected function getFieldLabel($fieldName)
    {
        $labels = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email ID',
            'gender' => 'Gender',
            'mobile_number' => 'Contact No',
            'house_no' => 'House No',
            'street_name' => 'Street Name',
            'interested_city_id' => 'Area',
            'current_city_id' => 'City',
            'pincode' => 'Pincode',
            'alternative_number' => 'Alternative No',
            'work_type' => 'Role',
            'account_holder_name' => 'Account Holder Name',
            'bank_name' => 'Bank Name',
            'bank_passbook'=>'Bank Passbook',
            'ifsc_code' => 'IFSC Code',
            'account_number' => 'Bank Account No',
            'date_of_birth' => 'DOB',
            'present_address' => 'Present Address',
            'permanent_address' => 'Permanent Address',
            'emp_prev_company_id' => 'Rider ID',
            'emp_prev_experience' => 'Past Experience',
            'father_name' => 'Father/ Mother/ Guardian',
            'father_mobile_number' => 'Father/ Mother/ Guardian Contact No',
            'referal_person_name' => 'Reference Name',
            'referal_person_number' => 'Reference Contact No',
            'referal_person_relationship' => 'Reference Relationship',
            'spouse_name' => 'Spouse Name',
            'spouse_mobile_number' => 'Spouse Contact No',
            'blood_group' => 'Blood Group',
            'social_links' => 'Social Media Link',
            'rider_type' => 'Rider Type',
            'vehicle_type_id' => 'Vehicle Type',
            'aadhar_number' => 'Aadhaar No',
            'pan_number' => 'Pan No',
            'license_number' => 'Driving license No',
            'reg_application_id' => 'Application ID',
            'register_date_time' => 'Application Date',
            'current_status' => 'Current Status',
            'profile_image' => 'Profile Image',
            'aadhar_front' => 'Aadhar Front',
            'aadhar_back' => 'Aadhar Back',
            'pan_image' => 'PAN Image',
            'license_front' => 'License Front',
            'license_back' => 'License Back',
        ];
        
        return $labels[$fieldName] ?? ucfirst(str_replace('_', ' ', $fieldName));
    }
    
    protected function getFieldValue($deliveryMan, $fieldName)
    {
       
        
        if (!$deliveryMan) return '-';
        
        try {
            switch ($fieldName) {
                case 'current_city_id':
                    return optional($deliveryMan->current_city)->city_name ?? '-';
                case 'vehicle_type_id':
                    return optional($deliveryMan->vehicle_type)->name ?? '-';
                case 'work_type':
                    return $this->getWorkTypeLabel($deliveryMan->work_type);
                case 'interested_city_id':
                    return optional($deliveryMan->interest_city)->Area_name ?? '-';
                case 'rider_type':
                    return optional($deliveryMan->RiderType)->type ?? '-';
                case 'bank_passbook':
                        return !empty($deliveryMan->bank_passbook) 
                        ? asset('public/EV/images/bank_passbook/' . $deliveryMan->bank_passbook)
                        : '';
                case 'current_status':
                    if (!isset($this->assignments) || $this->assignments->isEmpty()) {
                        return '-';
                    }
                    
                    foreach ($this->assignments as $assignment) {
                        if ($assignment->dm_id == $deliveryMan->id) {
                            return $this->getStatusLabel($assignment->current_status);
                        }
                    }
                    return '-';
                case 'register_date_time':
                    return $deliveryMan->register_date_time 
                        ? date('d M Y, h:i A', strtotime($deliveryMan->register_date_time))
                        : '-';
                // Image fields - check if image exists, then return URL, else empty string
                case 'photo':
                    return !empty($deliveryMan->photo) 
                        ? asset('public/EV/images/photos/' . $deliveryMan->photo)
                        : '';
        
                case 'aadhar_card_front':
                    return !empty($deliveryMan->aadhar_card_front) 
                        ? asset('public/EV/images/aadhar/' . $deliveryMan->aadhar_card_front)
                        : '';
        
                case 'aadhar_card_back':
                    return !empty($deliveryMan->aadhar_card_back) 
                        ? asset('public/EV/images/aadhar/' . $deliveryMan->aadhar_card_back)
                        : '';
        
                case 'pan_card_front':
                    return !empty($deliveryMan->pan_image) 
                        ? asset('public/EV/images/pan/' . $deliveryMan->pan_image)
                        : '';
        
                case 'driving_license_front':
                    return !empty($deliveryMan->driving_license_front) 
                        ? asset('public/EV/images/driving_license/' . $deliveryMan->driving_license_front)
                        : '';
        
                case 'driving_license_back':
                    return !empty($deliveryMan->driving_license_back) 
                        ? asset('public/EV/images/driving_license/' . $deliveryMan->driving_license_back)
                        : '';
        
                case 'llr_image':
                    return !empty($deliveryMan->llr_image) 
                        ? asset('public/EV/images/llr_images/' . $deliveryMan->llr_image)
                        : '';
                default:
                    return $deliveryMan->{$fieldName} ?? '-';
            }
        } catch (\Exception $e) {
            Log::error("Error getting field value for {$fieldName}: " . $e->getMessage());
            return '-';
        }
    }
    
    protected function getImageUrl($path)
    {
        if (empty($path)) {
            return 'No Image';
        }
        
        // Check if it's already a full URL
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        
        // Handle storage paths (if using Laravel's storage)
        if (strpos($path, 'storage/') === 0) {
            return asset($path);
        }
        
        // Handle direct public paths
        if (file_exists(public_path($path))) {
            return asset($path);
        }
        
        // Handle files in storage
        if (Storage::exists($path)) {
            return Storage::url($path);
        }
        
        return 'Image Not Found';
    }
    
    protected function getWorkTypeLabel($workType)
    {
        $types = [
            'deliveryman' => 'Rider',
            'in-house' => 'Employee',
            'adhoc' => 'Adhoc',
            'helper' => 'Helper'
        ];
        
        return $types[$workType] ?? $workType;
    }
    
    protected function getStatusLabel($status)
    {
        $statusLabels = [
            'pending' => 'Pending',
            'sent_to_bgv' => 'Sent to BGV',
            'sent_to_hr1' => 'Sent to HR Level 1',
            'approved_employee' => 'Approved (Employee)',
            'approved_rider' => 'Approved (Rider)',
            'reject_by_hr2' => 'Rejected by HR Level 2',
            'total_application' => 'Total Application'
        ];

        return $statusLabels[strtolower($status)] ?? ucfirst(str_replace('_', ' ', $status));
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}