<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\B2B\Entities\B2BVehicleRequests; //updated by Mugesh.B
use Carbon\Carbon;

class B2BAdminDeploymentRequestExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;
    protected $city;
    protected $zone;
    protected $status;

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = null, $zone = null, $status = null)
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = $city;
        $this->zone           = $zone;
        $this->status         = $status;
    }

    public function collection()
    {
        $query = B2BVehicleRequests::with('rider', 'zone', 'city', 'vehicle_type_relation');

        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        } else {
            if ($this->city) {
                $query->where('city_id', $this->city);
            }

            if ($this->status) {
                $query->where('status', $this->status);
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
                case 'req_id':
                    $mapped[] = $row->req_id ?? '-';
                    break;

                case 'rider_name':
                    $mapped[] = $row->rider->name ?? '-';
                    break;

                case 'mobile_no':
                    $mapped[] = $row->rider->mobile_no ?? '-';
                    break;

                case 'client':
                    $mapped[] = $row->rider->customerlogin->customer_relation->trade_name ?? '-';
                    break;

                case 'city':
                    $mapped[] = $row->city->city_name ?? '-';
                    break;

                case 'zone':
                    $mapped[] = $row->zone->name ?? '-';
                    break;

                case 'from_date':
                    $mapped[] = $row->start_date ?? '-';
                    break;

                case 'end_date':
                    $mapped[] = $row->end_date ?? '-';
                    break;

                case 'vehicle_type':
                    $mapped[] = $row->vehicle_type_relation->name ?? '-';
                    break;

                case 'battery_type':
                    if ($row->battery_type == 1) {
                        $mapped[] = 'Removable';
                    } elseif ($row->battery_type == 2) {
                        $mapped[] = 'Non Removable';
                    } else {
                        $mapped[] = '-';
                    }
                    break;

                case 'status':
                    if ($row->status == 'pending') {
                        $mapped[] = 'Opened';
                    } elseif ($row->status == 'completed') {
                        $mapped[] = 'Closed';
                    } else {
                        $mapped[] = ucfirst($row->status) ?? '-';
                    }
                    break;

                case 'aging':
                    if ($row->status === 'completed' && $row->completed_at) {
                        $aging = Carbon::parse($row->created_at)->diffForHumans(Carbon::parse($row->completed_at), true);
                    } else {
                        $aging = Carbon::parse($row->created_at)->diffForHumans(now(), true);
                    }
                    $mapped[] = $aging ?? '-';
                    break;

                case 'created_at':
                    $mapped[] = $row->created_at ? Carbon::parse($row->created_at)->format('d M Y, h:i A') : '-';
                    break;

                case 'updated_at':
                    $mapped[] = $row->updated_at ? Carbon::parse($row->updated_at)->format('d M Y, h:i A') : '-';
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
            'req_id'        => 'Request ID',
            'rider_name'    => 'Rider Name',
            'mobile_no'     => 'Contact Details',
            'client'        => 'Client Name',
            'city'          => 'City',
            'zone'          => 'Zone',
            'from_date'     => 'Vehicle Duration From Date',
            'end_date'      => 'Vehicle Duration End Date',
            'vehicle_type'  => 'Vehicle Type',
            'battery_type'  => 'Battery Type',
            'status'        => 'Status',
            'aging'         => 'Aging',
            'created_at'    => 'Created Date & Time',
            'updated_at'    => 'Updated Date & Time',
        ];

        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }
}
