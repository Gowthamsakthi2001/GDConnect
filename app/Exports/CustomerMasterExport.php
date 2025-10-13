<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\MasterManagement\Entities\CustomerMaster;

class CustomerMasterExport implements FromCollection, WithHeadings, WithMapping
{
    protected $status;
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $timeline;
    protected $selectedFields;

    protected $fieldMap = [
        'customer_id' => 'Customer ID' ,
        'customer_type' => 'Customer Type',
        'bussiness_type' => 'Business Type',
        'business_constitution_type' => 'Business Constitution Type',
        'company_name' => 'Company Name',
        'email' => 'Email ID',
        'phone' => 'Contact No',
        'address' => 'Address',
        'trade_name' => 'Trade Name',
        'City' => 'City',
        'state' => 'State',
        'gst_no' => 'GST No',
        'pan_no' => 'PAN No',
        'adhaar_front' => 'Adhaar Front Image',
        'adhaar_back' => 'Adhaar Back Image',
        'pan' => 'PAN Image',
        'gst_image' => 'GST Image',
        'other_bussiness_proof' => 'Other Business Proof',
        'status' => 'Status',
    ];

    public function __construct($status, $from_date, $to_date, $timeline, $selectedIds = [], $selectedFields = [])
    {
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->selectedIds = $selectedIds;
        $this->timeline = $timeline;
        $this->selectedFields = collect($selectedFields)
            ->filter(fn($item) => $item['value'] === 'on')
            ->pluck('name')
            ->filter(fn($name) => $name !== 'customer_hubs' && $name !== 'poc_details')
            ->toArray();
            

            
    }

    public function collection()
    {
        $query = CustomerMaster::with('constitution_type', 'cities', 'states');

        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        } else {
            if (in_array($this->status, ['0', '1'])) {
                $query->where('status', $this->status);
            }

            if ($this->timeline) {
                switch ($this->timeline) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'this_week':
                        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'this_month':
                        $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                        break;
                    case 'this_year':
                        $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
                        break;
                }
            } else {
                if ($this->from_date) {
                    $query->whereDate('created_at', '>=', $this->from_date);
                }
                if ($this->to_date) {
                    $query->whereDate('created_at', '<=', $this->to_date);
                }
            }
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function map($row): array
    {
        $mapData = [];
        foreach ($this->selectedFields as $field) {
            $mapData[] = $this->getFieldValue($row, $field);
        }
        return $mapData;
    }

    protected function getFieldValue($row, $field)
    {
        switch ($field) {
            case 'customer_id':
                return $row->id ?? '-';
            case 'customer_type':
                return $row->customer_type == 1 ? 'Individual' : ($row->customer_type == 2 ? 'Company' : '-');
            case 'bussiness_type':
                return $row->business_type == 1 ? 'Registered' : ($row->business_type == 2 ? 'Unregistered' : '-');
            case 'business_constitution_type':
                return $row->constitution_type->name ?? '-';
            case 'company_name':
                return $row->name ?? '-';
            case 'trade_name':
                return $row->trade_name ?? '-';
            case 'email':
                return $row->email ?? '-';
            case 'phone':
                return $row->phone ?? '-';
            case 'address':
                return $row->address ?? '-';
            case 'City':
                return $row->cities->city_name ?? '-';
            case 'state':
                return $row->states->state_name ?? '-';
            case 'gst_no':
                return $row->gst_no ?? '-';
            case 'pan_no':
                return $row->pan_no ?? '-';
            case 'adhaar_front':
                return $row->adhaar_front_img ? asset("EV/vehicle_transfer/adhaar_front_images/{$row->adhaar_front_img}") : '-';
            case 'adhaar_back':
                return $row->adhaar_back_img ? asset("EV/vehicle_transfer/adhaar_back_images/{$row->adhaar_back_img}") : '-';
            case 'pan':
                return $row->pan_img ? asset("EV/vehicle_transfer/pan_card_images/{$row->pan_img}") : '-';
            case 'gst_image':
                return $row->gst_img ? asset("EV/vehicle_transfer/gst_images/{$row->gst_img}") : '-';
            case 'other_bussiness_proof':
                return $row->business_proof_img ? asset("EV/vehicle_transfer/other_business_proof_images/{$row->business_proof_img}") : '-';
            case 'status':
                return $row->status == 1 ? 'Active' : 'Inactive';
            default:
                return '-';
        }
    }

    public function headings(): array
    {
        return array_map(fn($field) => $this->fieldMap[$field] ?? $field, $this->selectedFields);
    }
}
