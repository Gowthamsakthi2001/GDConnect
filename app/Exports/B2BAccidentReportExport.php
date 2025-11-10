<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\B2B\Entities\B2BReportAccident;
use Modules\MasterManagement\Entities\CustomerLogin;
use Illuminate\Support\Facades\Auth;

class B2BAccidentReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $selectedFields;
    protected $city;
    protected $zone;
    protected $status;
    protected $accountability_type;
    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = null, $zone = null,$status=null , $accountability_type=null)
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = $city;
        $this->status           = $status;
        $this->accountability_type           = $accountability_type;
    }

    public function collection()
    {
        $guard = Auth::guard('master')->check() ? 'master' : 'zone';
                    $user  = Auth::guard($guard)->user();
                    $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                    
        $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
                    ->where('city_id', $user->city_id)
                    ->pluck('id');
                    
        $query = B2BReportAccident::with([
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
                        
                        if ($this->accountability_type) {
                            $q->where('account_ability_type', $this->accountability_type);
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
        
        if ($row->status === 'claim_closed') {
            $aging = \Carbon\Carbon::parse($row->created_at)
                        ->diffForHumans(\Carbon\Carbon::parse($row->updated_at), true);
        } else {
            $aging = \Carbon\Carbon::parse($row->created_at)
                        ->diffForHumans(now(), true);
        }

        foreach ($this->selectedFields as $key) {
            switch ($key) {
                case 'req_id':
                    $mapped[] = $row->assignment->VehicleRequest->req_id ?? '-';
                    break;

                case 'rider_name':
                    $mapped[] = $row->assignment->rider->name ?? '-';
                    
                    break;

                case 'accountability_type':
                        $mapped[] = $row->assignment->VehicleRequest->accountAbilityRelation->name ?? '-';
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
                case 'aging':
                    $mapped[] = $aging ?? '-';
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

                case 'accident_type':
                    $mapped[] = $row->accident_type ?? '-';
                    break;
                
                case 'location':
                    $mapped[] = $row->location_of_accident ?? '-';
                    break;
                
                case 'rider_llr_number':
                    $mapped[] = $row->assignment->rider->llr_number ?? '-';
                    break;
                    
                case 'rider_license_number':
                    $mapped[] = $row->assignment->rider->driving_license_number ?? '-';
                    break;
                case 'vehicle_damage':
                    $mapped[] = $row->vehicle_damage ?? '-';
                    break;
                
                case 'rider_injury_description':
                    $mapped[] = $row->rider_injury_description ?? '-';
                    break;
                    
                case 'third_party_injury_description':
                    $mapped[] = $row->third_party_injury_description ?? '-';
                    break;
                
                case 'accident_attachments':
                    $attachments = json_decode($row->accident_attachments, true); // decode JSON
                    if (is_array($attachments) && !empty($attachments)) {
                        $urls = [];
                        foreach ($attachments as $file) {
                            $urls[] = asset('b2b/accident_reports/attachments/' . $file);
                        }
                        $mapped[] = implode(", ", $urls); // join multiple URLs
                    } else {
                        $mapped[] = '-';
                    }
                    break;
                
                case 'police_report':
                    $report = json_decode($row->police_report, true); // decode JSON
                    if (!empty($report['name'])) {
                        $mapped[] = asset('b2b/accident_reports/police_reports/' . $report['name']);
                    } else {
                        $mapped[] = '-';
                    }
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
                    
                case 'updated_at':
                    $mapped[] = $row->updated_at
                        ? $row->updated_at->format('d M Y h:i A')
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
            'accountability_type'     => 'Accountability Type',
            'zone'          => 'Zone',
            'accident_type' => 'Accident Type',
            'location'      => 'Location',
            'rider_license_number' => 'Rider License Number',
            'rider_llr_number' => 'Rider llr Number',
            'vehicle_damage' => 'Vehicle Damage',
            'rider_injury_description' => 'Rider Injury Description',
            'third_party_injury_description' => 'Third Party Injury Description',
            'accident_attachments' => 'Accident Attachments',
            'police_report' => 'Police Report',
            'description'   => 'Description',
            'created_by'    => 'Created By',
            'status'        => 'Status',
            'created_at'    => 'Created Date & Time',
            'updated_at'    => 'Updated Date & Time',
            'aging'         => 'aging'
        ];

        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }
}
