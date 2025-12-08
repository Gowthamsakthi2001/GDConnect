<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\B2B\Entities\B2BServiceRequest;
use App\Models\User;
use Modules\MasterManagement\Entities\CustomerLogin;

class B2BAdminZonesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $selectedFields;
    protected $city;    
    protected $customer;
    protected $status;
    
    public function __construct($selectedFields = [], $city = [],$status = [] , $customer = [])
    {
        $this->selectedFields = $selectedFields;
        $this->city           = (array) $city;
        $this->customer       =(array) $customer;
        $this->status       =(array) $status;
    }

    public function collection()
     {
        $query = CustomerLogin::with('customer_relation', 'city', 'zone')
        ->whereNotNull('zone_id')       
        ->where('zone_id', '!=', '');   

        if (!empty($this->city)) {
            $query->whereIn('city_id', $this->city);
        }
        if (!empty($this->customer)) {
            $query->whereIn('customer_id', $this->customer);
        }
        
        $query->whereHas('zone', function ($q) {
            $q->whereIn('status',$this->status);
        });
        return $query->orderBy('id', 'desc')->get();
    }

    public function map($row): array
    {
        $mapped = [];

        foreach ($this->selectedFields as $key) {
            switch ($key) {
                case 'client':
                    $mapped[] = $row->customer_relation->trade_name ?? '-';
                    break;

                case 'city':
                    $mapped[] = $row->city->city_name ?? '-';
                    break;

                case 'zone':
                    $mapped[] = $row->zone->name ?? '-';
                    break;

                case 'zone_status':
                    $mapped[] = $row->zone
                        ? ($row->zone->status == 1 ? 'Active' : ($row->zone->status == 2 ? 'Inactive' : '-'))
                        : '-';
                    break;

                case 'client_status':
                    $mapped[] = $row->customer_relation
                        ? ($row->customer_relation->status == 1 ? 'Active' : ($row->customer_relation->status == 2 ? 'Inactive' : '-'))
                        : '-';
                    break;

                case 'agent_name':
                    $zone_id = $row->zone_id ?? null;
                    $agent_names = $zone_id
                        ? User::where('login_type', 2)
                            ->where('zone_id', $zone_id)
                            ->pluck('name')
                            ->implode(', ')
                        : null;
                    $mapped[] = $agent_names ?: '-';
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
            'client'        => 'Client Name',
            'city'          => 'City Name',
            'zone'          => 'Zone Name',
            'zone_status'   => 'Zone Status',
            'client_status' => 'Client Status',
            'agent_name'    => 'Agent Name'
        ];

        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }
}
