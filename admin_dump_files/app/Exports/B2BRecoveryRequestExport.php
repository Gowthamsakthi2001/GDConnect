<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\B2B\Entities\B2BRecoveryRequest;
use Modules\MasterManagement\Entities\CustomerLogin;
use Illuminate\Support\Facades\Auth;

class B2BRecoveryRequestExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;
    protected $city;
    protected $zone;
    protected $status;

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = null, $zone = null ,$status = null)
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = $city;
        $this->zone           = $zone;
        $this->status           = $status;
    }

    public function collection()
    {
        $guard = Auth::guard('master')->check() ? 'master' : 'zone';
                    $user  = Auth::guard($guard)->user();
                    $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                    
        $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
                    ->where('city_id', $user->city_id)
                    ->pluck('id');
                    
        $query = B2BRecoveryRequest::with([
            'assignment.VehicleRequest.city',
            'assignment.VehicleRequest.zone',
            'assignment.vehicle',
            'assignment.VehicleRequest',
            'assignment.rider.customerlogin.customer_relation'
        ]);
        
        $query->whereHas('assignment.VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds) {
                    // Always filter by created_by if IDs exist
                        if ($customerLoginIds->isNotEmpty()) {
                            $q->whereIn('created_by', $customerLoginIds);
                        }
                    
                        // Apply guard-specific filters
                        if ($guard === 'master') {
                            $q->where('city_id', $user->city_id);
                        }
                    
                        if ($guard === 'zone') {
                            $q->where('city_id', $user->city_id)
                              ->where('zone_id', $user->zone_id);
                        }
                    });
                    
        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        } else {
            if ($this->city) {
                $query->whereHas('assignment.VehicleRequest', function ($q) {
                    $q->where('city_id', $this->city);
                });
            }

            if ($this->zone) {
                $query->whereHas('assignment.VehicleRequest', function ($q) {
                    $q->where('zone_id', $this->zone);
                });
            }
            
            if ($this->status) {
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

        foreach ($this->selectedFields as $key) {
            switch ($key) {
                case 'req_id':
                    $mapped[] = $row->assignment->VehicleRequest->req_id ?? '-';
                    break;

                case 'rider_name':
                    $mapped[] = $row->assignment->rider->name ?? '-';
                    
                    break;

                case 'vehicle_no':
                    $mapped[] = $row->assignment->vehicle->permanent_reg_number ?? '-';
                    break;

                case 'chassis_number':
                    $mapped[] = $row->assignment->vehicle->chassis_number ?? '-';
                    break;

                case 'mobile_no':
                    $mapped[] = $row->assignment->rider->mobile_no ?? '-';
                    break;

                // case 'poc_name':
                //     $mapped[] = $row->assignment->rider->customerlogin->customer_relation->trade_name ?? '-';
                //     break;
                
                // case 'poc_number':
                //     $mapped[] = $row->assignment->rider->customerlogin->customer_relation->phone ?? '-';
                //     break;
                    
                case 'city':
                    $mapped[] = $row->assignment->VehicleRequest->city->city_name ?? '-';
                    break;

                case 'zone':
                    $mapped[] = $row->assignment->VehicleRequest->zone->name ?? '-';
                    break;

                case 'reason':
                    $reasons = [
                        1 => 'Breakdown',
                        2 => 'Battery Drain',
                        3 => 'Accident',
                        4 => 'Rider Unavailable',
                        5 => 'Other',
                    ];
                    $mapped[] = $reasons[(int)($row->reason ?? 0)] ?? '-';
                    break;

                case 'description':
                    $mapped[] = $row->description ?? '-';
                    break;
                
                case 'created_by':
                    $mapped[] = $row->assignment->rider->customerlogin->customer_relation->trade_name ?? '-';
                    break;

                case 'status':
                    $mapped[] = ucfirst($row->status ?? '-');
                    break;

                case 'created_at':
                    $mapped[] = $row->created_at
                        ? $row->created_at->format('d M Y h:i A')
                        : '-';
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
            'vehicle_no'    => 'Vehicle Number',
            'chassis_number'=> 'Chassis Number',
            'rider_name'    => 'Rider Name',
            'mobile_no'     => 'Mobile No',
            'city'          => 'City',
            // 'poc_name'      => 'POC Name',
            // 'poc_number'   => 'POC Contact',
            'zone'          => 'Zone',
            'reason'        => 'Reason',
            'description'   => 'Description',
            'created_by'    => 'Created By',
            'status'        => 'Status',
            'created_at'    => 'Created Date & Time',
        ];

        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }
}
