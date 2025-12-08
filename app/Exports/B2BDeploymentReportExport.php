<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Modules\B2B\Entities\B2BVehicleAssignment;
use Modules\MasterManagement\Entities\CustomerLogin;

class B2BDeploymentReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $date_range;
    protected $from_date;
    protected $to_date;
    protected $vehicle_type = [];
    protected $city = [];
    protected $zone = [];
    protected $vehicle_no = [];
    protected $vehicle_make = [];
    protected $vehicle_model = [];
    protected $status = [];
    protected $accountability_type = [];
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

        // ALWAYS convert to array safely
        $this->vehicle_type = (array) $vehicle_type;
        $this->vehicle_model = (array) $vehicle_model;
        $this->vehicle_make = (array) $vehicle_make;
        $this->city = (array) $city;
        $this->zone = (array) $zone;
        $this->vehicle_no = (array) $vehicle_no;
        $this->status = (array) $status;
        $this->accountability_type = (array) $accountability_type;
    }

    public function collection()
    {
        $guard = Auth::guard('master')->check() ? 'master' : 'zone';
        $user  = Auth::guard($guard)->user();

        /** --------------------------------------------------
         * FIX: convert CSV string → array
         * -------------------------------------------------- */
        foreach (['status','vehicle_type','vehicle_model','vehicle_make','city','zone','accountability_type'] as $key) {
            if (!empty($this->$key) && is_string($this->$key[0])) {
                $this->$key = explode(',', $this->$key[0]);
            }
        }

        /** Remove empty values */
        $this->status               = array_filter($this->status);
        $this->vehicle_type         = array_filter($this->vehicle_type);
        $this->vehicle_model        = array_filter($this->vehicle_model);
        $this->vehicle_make         = array_filter($this->vehicle_make);
        $this->city                 = array_filter($this->city);
        $this->zone                 = array_filter($this->zone);
        $this->accountability_type  = array_filter($this->accountability_type);

        $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
        $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
            ->where('city_id', $user->city_id)
            ->pluck('id');

        $query = B2BVehicleAssignment::with([
            'vehicle.quality_check.customer_relation',
            'vehicle.vehicle_type_relation',
            'vehicle.vehicle_model_relation',
            'rider',
            'agent_relation',
            'zone',
            'VehicleRequest',
            'VehicleRequest.accountAbilityRelation',
            'recovery_Request',
        ]);

        /** --------------------------------------------------
         * VEHICLE REQUEST FILTERS
         * -------------------------------------------------- */
        $query->whereHas('VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds) {

            if ($customerLoginIds->isNotEmpty()) {
                $q->whereIn('created_by', $customerLoginIds);
            }

            if (!empty($this->accountability_type)) {
                $q->whereIn('account_ability_type', $this->accountability_type);
            }

            if (!empty($this->city)) {
                $q->whereIn('city_id', $this->city);
            }

            if (!empty($this->zone)) {
                $q->whereIn('zone_id', $this->zone);
            }

            if ($guard === 'master') {
                $q->where('city_id', $user->city_id);
            }

            if ($guard === 'zone') {
                $zoneId = !empty($this->zone) ? $this->zone : [$user->zone_id];
                $q->where('city_id', $user->city_id)
                    ->whereIn('zone_id', $zoneId);
            }
        });

        /** --------------------------------------------------
         * OUTSIDE VEHICLE FILTERS
         * -------------------------------------------------- */
        if (!empty($this->status)) {
            $query->whereIn('status', $this->status);
        }

        if (!empty($this->vehicle_model)) {
            $query->whereHas('vehicle', fn($q) =>
                $q->whereIn('model', $this->vehicle_model)
            );
        }

        if (!empty($this->vehicle_type)) {
            $query->whereHas('vehicle', fn($q) =>
                $q->whereIn('vehicle_type', $this->vehicle_type)
            );
        }

        if (!empty($this->vehicle_make)) {
            $query->whereHas('vehicle.vehicle_model_relation', fn($q) =>
                $q->whereIn('make', $this->vehicle_make)
            );
        }

        if (!empty($this->vehicle_no)) {
            $vehicleNos = (array) $this->vehicle_no;

            $query->whereHas('vehicle', fn($v) =>
                $v->whereIn('id', $vehicleNos)
            );
        }

        /** --------------------------------------------------
         * DATE RANGE
         * -------------------------------------------------- */
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
            default:
                $from = $to = now()->toDateString();
                break;
        }

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function map($row): array
    {
        $this->sl++;

        /** FIX: Battery type */
        $battery_type = '-';
        if ($row->battery_type == 1) $battery_type = 'Self-Charging';
        if ($row->battery_type == 2) $battery_type = 'Portable';

        /** FIX: Status mapping */
        $status = match ($row->status) {
            'running' => 'Running',
            'accident' => 'Accident',
            'under_maintenance' => 'Under Maintenance',
            'recovery_request' => ($row->recovery_Request->created_by_type ?? null) === 'b2b-admin-dashboard'
                ? 'GDM Recovery Initiated'
                : 'Client Recovery Initiated',
            'recovered' => 'Recovered',
            'return_request' => 'Return Request',
            'returned' => 'Returned',
            default => 'Unknown',
        };

        return [
            $this->sl,
            $row->VehicleRequest->req_id ?? '-',
            $row->VehicleRequest->accountAbilityRelation->name ?? '-',
            $row->vehicle->permanent_reg_number ?? '-',
            $row->vehicle->chassis_number ?? '-',
            $row->vehicle->vehicle_id ?? '-',
            $row->vehicle->vehicle_model_relation->make ?? '-',
            $row->vehicle->vehicle_type_relation->name ?? '-',
            $battery_type,
            $row->vehicle->quality_check->location_relation->city_name ?? '-',
            $row->vehicle->quality_check->zone->name ?? '-',
            $row->VehicleRequest->customerLogin->customer_relation->trade_name ?? '-',
            $row->rider->name ?? '-',
            $row->rider->mobile_no ?? '-',
            $row->agent_relation->name ?? '-',
            $row->odometer_value ?? '-',
            $row->odometer_image ? asset('public/b2b/odometer_images/' . $row->odometer_image) : '-',
            $row->vehicle_front ? asset('public/b2b/vehicle_front/' . $row->vehicle_front) : '-',
            $row->vehicle_back ? asset('public/b2b/vehicle_back/' . $row->vehicle_back) : '-',
            $row->vehicle_top ? asset('public/b2b/vehicle_top/' . $row->vehicle_top) : '-',
            $row->vehicle_left ? asset('public/b2b/vehicle_left/' . $row->vehicle_left) : '-',
            $row->vehicle_right ? asset('public/b2b/vehicle_right/' . $row->vehicle_right) : '-',
            $row->vehicle_battery ? asset('public/b2b/vehicle_battery/' . $row->vehicle_battery) : '-',
            $row->vehicle_charger ? asset('public/b2b/vehicle_charger/' . $row->vehicle_charger) : '-',
            optional($row->VehicleRequest->created_at)->format('d M Y h:i A') ?? '-',
            optional($row->created_at)->format('d M Y h:i A') ?? '-',
            $status
        ];
    }

    public function headings(): array
    {
        return [
            'SL NO','Request ID','Accountability name','Vehicle Number','Chassis Number','Vehicle ID',
            'Vehicle Make','Vehicle Type','Battery Type','City','Zone','Customer Name',
            'Rider Name','Rider Number','Agent Name','Odometer value','Odometer Image',
            'Vehicle Front Image','Vehicle Back Image','Vehicle Top Image','Vehicle Left Image',
            'Vehicle Right Image','Vehicle Battery Image','Vehicle Charger Image','Requested Date',
            'Assignment Date','Status',
        ];
    }
}
