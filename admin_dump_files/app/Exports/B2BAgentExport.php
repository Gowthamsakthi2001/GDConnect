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
        $query = B2BAgent::where('role',17)->with(['city', 'zone']); // if relations exist

        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        } else {
            if ($this->city) {
                $query->where('city_id', $this->city);
            }

            if ($this->zone) {
                $query->where('zone_id', $this->zone);
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
