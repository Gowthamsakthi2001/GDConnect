<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\B2B\Entities\B2BReturnRequest;
use Modules\MasterManagement\Entities\CustomerLogin;
use Carbon\Carbon;

class B2BReturnReportExport implements FromCollection, WithHeadings, WithMapping
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
        $accountability_type = [],
        $status = [],
        $vehicle_model = [],
        $vehicle_make = []
    ) {
        $this->date_range  = $date_range;
        $this->from_date   = $from_date;
        $this->to_date     = $to_date;

        // Always convert to array safely
        $this->vehicle_type = (array) $vehicle_type;
        $this->vehicle_model = (array) $vehicle_model;
        $this->vehicle_make  = (array) $vehicle_make;
        $this->city          = (array) $city;
        $this->zone          = (array) $zone;
        $this->vehicle_no    = (array) $vehicle_no;

        $this->accountability_type = (array) $accountability_type;

        // Status can arrive as ["running,returned"]
        if (!empty($status) && is_string($status)) {
            $this->status = explode(',', $status);
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

        $query = B2BReturnRequest::with([
            'assignment.rider',
            'assignment.vehicle.vehicle_type_relation',
            'assignment.vehicle.vehicle_model_relation',
            'assignment.vehicle.quality_check.customer_relation',
            'assignment.vehicle.quality_check.location_relation',
            'assignment.vehicle.quality_check.zone',
            'assignment.zone',
            'assignment.VehicleRequest',
            'agent',
            'assignment.VehicleRequest.accountAbilityRelation'
        ]);

        // Core Filters
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
            } else { // zone guard
                $zoneId = !empty($this->zone) ? $this->zone : [$user->zone_id];

                $q->where('city_id', $user->city_id)
                    ->whereIn('zone_id', $zoneId);
            }
        });

        // Vehicle Model Filter
        if (!empty(array_filter($this->vehicle_model))) {
            $query->whereHas('assignment.vehicle', function ($q) {
                $q->whereIn('model', $this->vehicle_model);
            });
        }

        // Vehicle Type Filter
        if (!empty(array_filter($this->vehicle_type))) {
            $query->whereHas('assignment.vehicle', function ($q) {
                $q->whereIn('vehicle_type', $this->vehicle_type);
            });
        }

        // Vehicle Make Filter
        if (!empty(array_filter($this->vehicle_make))) {
            $query->whereHas('assignment.vehicle.vehicle_model_relation', function ($q) {
                $q->whereIn('make', $this->vehicle_make);
            });
        }

        // Vehicle Number filter
        if (!empty(array_filter($this->vehicle_no))) {
            $vehicleNos = (array) $this->vehicle_no;

            $query->whereHas('assignment.vehicle', function ($v) use ($vehicleNos) {
                $v->whereIn('id', $vehicleNos);
            });
        }

        // Date Range Filter
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
                break;

            default: // today
                $from = $to = now()->toDateString();
                break;
        }

        if ($from && $to) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
        }

        // Status Filter
        if (!empty(array_filter($this->status))) {
            $query->whereIn('status', $this->status);
        }

        return $query->orderByDesc('id')->get();
    }

    public function map($row): array
    {
        $this->sl++;

        $createdAt = $row->created_at
            ? Carbon::parse($row->created_at)->format('d M Y h:i A')
            : '-';

        $closedAt = $row->closed_at
            ? Carbon::parse($row->closed_at)->format('d M Y h:i A')
            : '-';

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
            $row->assignment->VehicleRequest->customerLogin->customer_relation->trade_name ?? '-',
            $row->assignment->rider->name ?? '-',
            $row->assignment->rider->mobile_no ?? '-',
            $row->agent->name ?? '-',
            $row->odometer_value ?? '-',
            $row->odometer_image ? asset('public/b2b/odometer_images/' . $row->odometer_image) : '-',
            $row->vehicle_front ? asset('public/b2b/vehicle_front/' . $row->vehicle_front) : '-',
            $row->vehicle_back ? asset('public/b2b/vehicle_back/' . $row->vehicle_back) : '-',
            $row->vehicle_top ? asset('public/b2b/vehicle_top/' . $row->vehicle_top) : '-',
            $row->vehicle_left ? asset('public/b2b/vehicle_left/' . $row->vehicle_left) : '-',
            $row->vehicle_right ? asset('public/b2b/vehicle_right/' . $row->vehicle_right) : '-',
            $row->vehicle_battery ? asset('public/b2b/vehicle_battery/' . $row->vehicle_battery) : '-',
            $row->vehicle_charger ? asset('public/b2b/vehicle_charger/' . $row->vehicle_charger) : '-',
            $createdAt,
            $closedAt,
        ];
    }

    public function headings(): array
    {
        return [
            'SL NO',
            'Request ID',
            'Accountability Type',
            'Vehicle Number',
            'Chassis Number',
            'Vehicle ID',
            'Vehicle Model',
            'Vehicle Make',
            'Vehicle Type',
            'City',
            'Zone',
            'Customer Name',
            'Rider Name',
            'Rider Number',
            'Agent Name',
            'Odometer Value',
            'Odometer Image',
            'Vehicle Front Image',
            'Vehicle Back Image',
            'Vehicle Top Image',
            'Vehicle Left Image',
            'Vehicle Right Image',
            'Vehicle Battery Image',
            'Vehicle Charger Image',
            'Created Date & Time',
            'Completed Date & Time',
        ];
    }
}
