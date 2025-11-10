<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\B2B\Entities\B2BReportAccident;
use Modules\MasterManagement\Entities\CustomerLogin;
use Carbon\Carbon;

class B2BClientAccidentReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $date_range;
    protected $from_date;
    protected $to_date;
    protected $vehicle_type;
    protected $city;
    protected $zone;
    protected $vehicle_no;
    protected $status;
    protected $accountability_type;
    protected $sl = 0;

    public function __construct($date_range, $from_date, $to_date, $vehicle_type, $city, $zone, $vehicle_no = [] , $status , $accountability_type)
    {
        $this->date_range   = $date_range;
        $this->from_date    = $from_date;
        $this->to_date      = $to_date;
        $this->vehicle_type = $vehicle_type;
        $this->city         = $city;
        $this->zone         = $zone;
        $this->vehicle_no   = $vehicle_no;
        $this->status       = $status;
        $this->accountability_type       = $accountability_type;
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
            'assignment.rider',
            'assignment.vehicle.vehicle_type_relation',
            'assignment.vehicle.vehicle_model_relation',
            'assignment.vehicle.quality_check.customer_relation',
            'assignment.vehicle.quality_check.location_relation',
            'assignment.vehicle.quality_check.zone',
            'assignment.zone',
            'assignment.VehicleRequest',
            'assignment.VehicleRequest.accountAbilityRelation'
        ]);

        // Core filters
        $query->whereHas('assignment.VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds) {
            if ($customerLoginIds->isNotEmpty()) {
                $q->whereIn('created_by', $customerLoginIds);
            }

            if (!empty($this->accountability_type)) {
                    $q->where('account_ability_type', $this->accountability_type);
                }
                        
            if ($guard === 'master') {
                $q->where('city_id', $user->city_id);
                if (!empty($this->zone)) {
                    $q->where('zone_id', $this->zone);
                }
            } elseif ($guard === 'zone') {
                $zoneId = !empty($this->zone) ? $this->zone : $user->zone_id;
                $q->where('city_id', $user->city_id)
                  ->where('zone_id', $zoneId);
            }
        });

        // Vehicle type filter
        if (!empty($this->vehicle_type)) {
            $query->whereHas('assignment.vehicle', function ($v) {
                $v->where('vehicle_type', $this->vehicle_type);
            });
        }

        // Vehicle number filter (multi-select)
        if (!empty($this->vehicle_no)) {
            $vehicleNos = (array)$this->vehicle_no;
            $query->whereHas('assignment.vehicle', function ($v) use ($vehicleNos) {
                $v->whereIn('id', $vehicleNos);
            });
        }

        // Date range filter
        $from = $this->from_date;
        $to   = $this->to_date;

        switch ($this->date_range) {
            case 'yesterday':
                $from = $to = now()->subDay()->toDateString();
                break;
            case 'last7':
                $from = now()->subDays(6)->toDateString();
                $to   = now()->toDateString();
                break;
            case 'last30':
                $from = now()->subDays(29)->toDateString();
                $to   = now()->toDateString();
                break;
            case 'custom':
                // already passed via constructor
                break;
            default:
                $from = $to = now()->toDateString();
                break;
        }

        if ($from && $to) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
        }
        
        if(!empty($request->status)){
            $query->where('status' , $request->status);
        }

        return $query->orderByDesc('id')->get();
    }

    public function map($row): array
    {
        $this->sl++;

        $statusTextMap = [
            'claimed_initiated' => 'Claimed Initiated',
            'insurer_visit_confirmed' => 'Insurer Visit Confirmed',
            'inspection_completed' => 'Inspection Completed',
            'approval_pending' => 'Approval Pending',
            'repair_started' => 'Repair Started',
            'repair_completed' => 'Repair Completed',
            'invoice_submitted' => 'Invoice Submitted',
            'payment_approved' => 'Payment Approved',
            'claim_closed' => 'Claim Closed (Settled)',
        ];
        
        $status = '-';
        if (!empty($row->status)) {
            $status = $statusTextMap[$row->status] ?? ucfirst(str_replace('_', ' ', $row->status));
        }
        $createdAt = $row->created_at ? Carbon::parse($row->created_at)->format('d M Y h:i A') : '-';

        $accidentAttachments = '-';
        if (!empty($row->accident_attachments)) {
            $attachments = json_decode($row->accident_attachments, true);
            if (is_array($attachments) && count($attachments)) {
                $urls = array_map(fn($file) => asset('public/b2b/accident_reports/attachments/'.$file), $attachments);
                $accidentAttachments = implode(', ', $urls);
            }
        }
    
    
        $policeReport = '-';
        if (!empty($row->police_report)) {
            $report = json_decode($row->police_report, true);
            if (isset($report['name'])) {
                $policeReport = asset('public/b2b/accident_reports/police_reports/'.$report['name']);
            }
        }
    
    
        return [
            $this->sl,
            $row->assignment->VehicleRequest->req_id ?? '-',
            $row->assignment->VehicleRequest->accountAbilityRelation->name ?? '-',
            $row->assignment->vehicle->permanent_reg_number ?? '-',
            $row->assignment->vehicle->chassis_number ?? '-',
            $row->assignment->vehicle->vehicle_id ?? '-',
            $row->assignment->vehicle->vehicle_model_relation->make ?? '-',
            $row->assignment->vehicle->vehicle_type_relation->name ?? '-',
            $row->assignment->vehicle->quality_check->location_relation->city_name ?? '-',
            $row->assignment->vehicle->quality_check->zone->name ?? '-',
            $row->assignment->VehicleRequest->customerLogin->customer_relation->trade_name ?? '-',
            $row->assignment->rider->name ?? '-',
            $row->assignment->rider->mobile_no ?? '-',
            $row->location_of_accident ?? '-',
            $row->accident_type ?? '-',
            $row->description ?? '-',
            $row->vehicle_damage ?? '-',
            $row->rider_injury_description ?? '-',
            $row->third_party_injury_description ?? '-',
            $accidentAttachments,
            $policeReport,
            $createdAt ,
            $status
        ];
    }
    
    
    public function headings(): array
    {
        return [
            'SL NO',
            'Request ID',
            'Accountability Name',
            'Vehicle Number',
            'Chassis Number',
            'Vehicle ID',
            'Vehicle Make',
            'Vehicle Type',
            'City',
            'Zone',
            'Customer Name',
            'Rider Name',
            'Rider Number',
            'Location Of Accident',
            'Accident Type',
            'Description',
            'Vehicle Damage',
            'Rider Injury Description',
            'Third Party Injury Description',
            'Accident Attachments',
            'Police Report',
            'Created Date & Time',
            'Status',
        ];
    }
}
