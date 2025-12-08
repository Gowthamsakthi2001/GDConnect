<?php

namespace App\Exports;

use Modules\B2B\Entities\B2BAgent;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class B2BAgentExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $city;
    protected $zone;
    protected $selectedIds;
    protected $selectedFields;
     protected $datefilter;

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = [], $zone = [] , $datefilter)
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = (array)$city;
        $this->zone           = (array)$zone;
        $this->datefilter           = $datefilter;
    }

    public function collection()
    {
        $query = B2BAgent::where('role',17)->with(['city', 'zone']); // if relations exist

        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        } else {
            if (!empty($this->city)) {
                $query->whereIn('city_id', $this->city);
            }

            if (!empty($this->zone)) {
                $query->whereIn('zone_id', $this->zone);
            }
            
            if (!empty($this->datefilter )) {
                switch ($this->datefilter ) {
            
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
            
                    case 'week':
                        $query->whereBetween('created_at', [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]);
                        break;
                        
                    case 'last_15_days':
                        $query->whereMonth('created_at', now()->subDays(14)->startOfDay())
                              ->whereYear('created_at', now()->endOfDay());
                        break;
                        
                    case 'month':
                        $query->whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year);
                        break;
            
                    case 'year':
                        $query->whereYear('created_at', now()->year);
                        break;
            
                }
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

        foreach ($this->selectedFields as $key) {
            switch ($key) {
                case 'profile_photo_path':
                    $mapped[] = $row->profile_photo_path
                        ? asset('uploads/users/' . $row->profile_photo_path)
                        : '-';
                    break;

                case 'created_at':
                    $mapped[] = $row->$key
                        ? Carbon::parse($row->$key)->format('d M Y h:i A')
                        : '-';
                    break;

                case 'city_id':
                    $mapped[] = $row->city->city_name ?? '-';
                    break;

                case 'zone_id':
                    $mapped[] = $row->zone->name ?? '-';
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
            'emp_id'                => 'Employee ID',
            'name'                  => 'Name',
            'email'                 => 'Email',
            'phone'                 => 'Phone',
            'gender'                => 'Gender',
            'age'                   => 'Age',
            'address'               => 'Address',
            'status'                => 'Status',
            'profile_photo_path'    => 'Profile Photo',
            'city_id'               => 'City',
            'zone_id'               => 'Zone',
            'created_at'            => 'Created At',
            
        ];

        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }
}
