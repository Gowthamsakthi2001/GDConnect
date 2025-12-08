<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Modules\B2B\Entities\B2BRecoveryRequest;
use Modules\MasterManagement\Entities\CustomerLogin;
use Carbon\Carbon;

class B2BRecoveryReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $date_range;
    protected $from_date;
    protected $to_date;
    protected $vehicle_type = [];
    protected $city = [];
    protected $zone = [];
    protected $vehicle_no = [];
    protected $accountability_type = [];
    protected $vehicle_make = [];
    protected $vehicle_model = [];
    protected $status = [];
    protected $sl = 0;

    public function __construct(
        $date_range,
        $from_date,
        $to_date,
        $vehicle_type = [],
        $city = [],
        $zone = [],
        $vehicle_no = [],
        $status = [],
        $accountability_type = [],
        $vehicle_model = [],
        $vehicle_make = []
    ) {
        $this->date_range   = $date_range;
        $this->from_date    = $from_date;
        $this->to_date      = $to_date;

        // Safe casting
        $this->vehicle_type      = (array) $vehicle_type;
        $this->city              = (array) $city;
        $this->zone              = (array) $zone;
        $this->vehicle_no        = (array) $vehicle_no;
        $this->accountability_type = (array) $accountability_type;
        $this->vehicle_model     = (array) $vehicle_model;
        $this->vehicle_make      = (array) $vehicle_make;

        // Status sometimes arrives as ["running,returned"]
        if (!empty($status) && isset($status[0]) && is_string($status[0]) && str_contains($status[0], ',')) {
            $this->status = explode(',', $status[0]);
        } else {
            $this->status = (array) $status;
        }
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
            'assignment.rider',
            'assignment.vehicle.vehicle_type_relation',
            'assignment.vehicle.vehicle_model_relation',
            'assignment.vehicle.quality_check.customer_relation',
            'assignment.vehicle.quality_check.location_relation',
            'assignment.vehicle.quality_check.zone',
            'assignment.zone',
            'assignment.VehicleRequest',
            'recovery_agent',
            'assignment.VehicleRequest.accountAbilityRelation'
        ]);

        // ---------------- FILTERS ---------------- //

        $query->whereHas('assignment.VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds) {

            if ($customerLoginIds->isNotEmpty()) {
                $q->whereIn('created_by', $customerLoginIds);
            }

            if (!empty(array_filter($this->accountability_type))) {
                $q->whereIn('account_ability_type', $this->accountability_type);
            }

            if ($guard === 'master') {
                $q->where('city_id', $user->city_id);

                if (!empty(array_filter($this->zone))) {
                    $q->whereIn('zone_id', $this->zone);
                }
            }

            if ($guard === 'zone') {
                $zoneId = !empty($this->zone) ? $this->zone : [$user->zone_id];
                $q->where('city_id', $user->city_id)
                  ->whereIn('zone_id', $zoneId);
            }
        });

        // Vehicle Model
        if (!empty(array_filter($this->vehicle_model))) {
            $query->whereHas('assignment.vehicle', fn($q) =>
                $q->whereIn('model', $this->vehicle_model)
            );
        }

        // Vehicle Type
        if (!empty(array_filter($this->vehicle_type))) {
            $query->whereHas('assignment.vehicle', fn($q) =>
                $q->whereIn('vehicle_type', $this->vehicle_type)
            );
        }

        // Vehicle Make
        if (!empty(array_filter($this->vehicle_make))) {
            $query->whereHas('assignment.vehicle.vehicle_model_relation', fn($q) =>
                $q->whereIn('make', $this->vehicle_make)
            );
        }

        // Vehicle Number
        if (!empty($this->vehicle_no)) {
            $query->whereHas('assignment.vehicle', fn($q) =>
                $q->whereIn('id', $this->vehicle_no)
            );
        }

        // Date Range
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
        }

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        // Status
        if (!empty(array_filter($this->status))) {
            $query->whereIn('status', $this->status);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function map($row): array
    {
        $this->sl++;

        $created_by = match ($row->created_by_type) {
            'b2b-web-dashboard'  => 'Customer',
            'b2b-admin-dashboard' => 'GDM',
            default => 'Unknown'
        };

        $terms = $row->terms_condition ? 'Accepted' : 'Not Accepted';

        $statusText = match ($row->status) {
            'opened'          => 'Opened',
            'closed'          => 'Closed',
            'agent_assigned'  => 'Agent Assigned',
            'not_recovered'   => 'Not Recovered',
            default           => '-',
        };

        $agentStatusText = match ($row->agent_status) {
            'opened'            => 'Opened',
            'in_progress'       => 'In Progress',
            'reached_location'  => 'Location Reached',
            'revisit_location'  => 'Location Revisited',
            'recovered'         => 'Recovered',
            'not_recovered'     => 'Not Recovered',
            'rider_contacted'   => 'Rider Contacted',
            'hold'              => 'Hold',
            'closed'            => 'Closed',
            default             => '-',
        };

        $agentName = $row->recovery_agent
            ? trim(($row->recovery_agent->first_name ?? '') . ' ' . ($row->recovery_agent->last_name ?? ''))
            : '-';

        $createdAt = $row->created_at
            ? Carbon::parse($row->created_at)->format('d M Y h:i A')
            : '-';

        $closed_by = match ($row->closed_by_type) {
            'recovery-agent' => trim(($row->user->first_name ?? '') . ' ' . ($row->user->last_name ?? '')),
            'recovery-manager-dashboard' => $row->user->name ?? '-',
            default => '-'
        };

        $videoPath = $row->video
            ? asset('public/b2b/recovery_comments/' . $row->video)
            : '-';

        $ImagesPath = '-';
        if (!empty($row->images)) {
            $imgs = json_decode($row->images, true);
            if (is_array($imgs)) {
                $ImagesPath = implode(', ', array_map(fn($img) =>
                    asset("public/b2b/recovery_comments/$img")
                , $imgs));
            }
        }

        return [
            $this->sl,
            $row->assignment->VehicleRequest->req_id ?? '-',
            $row->assignment->VehicleRequest->accountAbilityRelation->name ?? '-',
            $row->assignment->vehicle->permanent_reg_number ?? '-',
            $row->assignment->vehicle->chassis_number ?? '-',
            $row->assignment->vehicle->vehicle_id ?? '-',
            $row->assignment->vehicle->vehicle_model_relation->vehicle_model ?? '-',
            $row->assignment->vehicle->vehicle_model_relation->make ?? '-',
            $row->assignment->vehicle->vehicle_type_relation->name ?? '-',
            $row->assignment->vehicle->quality_check->location_relation->city_name ?? '-',
            $row->assignment->vehicle->quality_check->zone->name ?? '-',
            $row->description ?? '-',
            $terms,
            $row->assignment->VehicleRequest->customerLogin->customer_relation->trade_name ?? '-',
            $row->assignment->rider->name ?? '-',
            $row->assignment->rider->mobile_no ?? '-',
            $agentName,
            $closed_by,
            $videoPath,
            $ImagesPath,
            $created_by,
            $createdAt,
            $statusText,
            $agentStatusText,
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
            'Vehicle Model',
            'Vehicle Make',
            'Vehicle Type',
            'City',
            'Zone',
            'Description',
            'Terms & Condition',
            'Customer Name',
            'Rider Name',
            'Rider Number',
            'Agent Name',
            'Closed By',
            'Video',
            'Images',
            'Created By',
            'Created Date & Time',
            'Status',
            'Agent Status'
        ];
    }
}
