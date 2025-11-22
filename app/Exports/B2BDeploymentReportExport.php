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
    protected $vehicle_type;
    protected $city;
    protected $zone;
    protected $vehicle_no;
    protected $status;
    protected $accountability_type;
    protected $sl = 0;

    public function __construct($date_range, $from_date, $to_date, $vehicle_type, $city, $zone, $vehicle_no =[], $status , $accountability_type)
    {
        $this->date_range   = $date_range;
        $this->from_date    = $from_date;
        $this->to_date      = $to_date;
        $this->vehicle_type = $vehicle_type;
        $this->city         = $city;
        $this->zone         = $zone;
        $this->vehicle_no   = $vehicle_no;
        $this->status   = $status;
        $this->accountability_type   = $accountability_type;
        
    }

    public function collection()
    {
        $guard = Auth::guard('master')->check() ? 'master' : 'zone';
        $user  = Auth::guard($guard)->user();

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

        // Core filters
        $query->whereHas('VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds) {
            if ($customerLoginIds->isNotEmpty()) {
                $q->whereIn('created_by', $customerLoginIds);
            }
            
           if (!empty($this->accountability_type)) {
                    $q->where('account_ability_type', $this->accountability_type);
            }

            if ($guard === 'master') {
                $q->where('city_id', $user->city_id);
            }

            if ($guard === 'zone') {
                $zoneId = $this->zone ?? $user->zone_id; 
                $q->where('city_id', $user->city_id)
                  ->where('zone_id', $zoneId);
            }
        });
        

        // Apply filters from constructor
        if ($this->city) {
            $query->whereHas('VehicleRequest', fn($q) => $q->where('city_id', $this->city));
        }

        if ($this->zone) {
            $query->whereHas('VehicleRequest', fn($q) => $q->where('zone_id', $this->zone));
        }

        if ($this->vehicle_type) {
            $query->whereHas('vehicle', fn($q) => $q->where('vehicle_type', $this->vehicle_type));
        }


        if ($this->vehicle_no) {
            $vehicleNos = (array) $this->vehicle_no; // Ensure it's an array
        
            $query->whereHas('vehicle', function ($v) use ($vehicleNos) {
                $v->whereIn('id', $vehicleNos);
            });
        }

        // Date range handling
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
                // already handled via from_date and to_date
                break;
            default:
                $from = $to = now()->toDateString();
                break;
        }

        if ($from && $to) {
            $query->whereDate('created_at', '>=', $from)
                  ->whereDate('created_at', '<=', $to);
        }
        
        if ($this->status) {
            $query->where('status', $this->status);
        }

        // $query->whereNotIn('status', ['returned']);

        return $query->orderBy('id', 'desc')->get();
    }

    public function map($row): array
    {
        $this->sl++;
        $status = '';
        
     if ($row->status === 'running') {
                $status = ' Running';
        } elseif ($row->status === 'accident') {
                $status = 'Accident';
        }   elseif ($row->status === 'under_maintenance') { 
            $status = 'Under Maintenance';   
            
        }
        elseif ($row->status === 'recovery_request') { 
            
           $recoveryStatus = $row->recovery_Request->created_by_type ?? null;


            $status = 'Client Recovery Initiated';
        
            if ($recoveryStatus === 'b2b-admin-dashboard') {
                $status = 'GDM Recovery Initiated';
            }
            
        }
        elseif ($row->status === 'recovered') { 
            $status = "Recovered";
        }
        
        elseif ($row->status === 'return_request') { 
            $status = 'Return Request';
        }
        elseif ($row->status === 'returned') { 
            $status = 'Returned';
        }
         else {
            $status = 'Unknown';
        }
                    
                    
        $battery_type = '';
        if($row->battery_type == 1){
            $battery_type = 'Self-Charging';
        }
        else if($row->battery_type == 1){
            $battery_type = 'Portable';
        }else{
            $battery_type = '-';
        }
        
        return [
            $this->sl,
            $row->VehicleRequest->req_id ?? '-',
            $row->VehicleRequest->accountAbilityRelation->name ?? '-',
            $row->vehicle->permanent_reg_number ?? '-',
            $row->vehicle->chassis_number ?? '-',
            $row->vehicle->vehicle_id ?? '-',
            $row->vehicle->vehicle_model_relation->make ?? '-',
            $row->vehicle->vehicle_type_relation->name ?? '-',
            $battery_type ,
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
            // $row->vehicle_bottom ? asset('public/b2b/vehicle_bottom/' . $row->vehicle_bottom) : '-',
            $row->vehicle_left ? asset('public/b2b/vehicle_left/' . $row->vehicle_left) : '-',
            $row->vehicle_right ? asset('public/b2b/vehicle_right/' . $row->vehicle_right) : '-',
            $row->vehicle_battery ? asset('public/b2b/vehicle_battery/' . $row->vehicle_battery) : '-',
            $row->vehicle_charger ? asset('public/b2b/vehicle_charger/' . $row->vehicle_charger) : '-',
            $row->VehicleRequest->created_at ? $row->VehicleRequest->created_at->format('d M Y h:i A') : '-',
            $row->created_at ? $row->created_at->format('d M Y h:i A') : '-',
            $status
            
        ];
    }

    public function headings(): array
    {
        
        return [
            'SL NO',
            'Request ID',
            'Accountability name',
            'Vehicle Number',
            'Chassis Number',
            'Vehicle ID' ,
            'Vehicle Make' ,
            'Vehicle Type' ,
            'Battery Type' ,
            'City' ,
            'Zone' ,
            'Customer Name',
            'Rider Name',
            'Rider Number',
            'Agent Name',
            'Odometer value',
            'Odometer Image',
            'Vehicle Front Image',
            'Vehicle Back Image',
            'Vehicle Top Image',
            // 'Vehicle Bottom Image',
            'Vehicle Left Image',
            'Vehicle Right Image',
            'Vehicle Battery Image',
            'Vehicle Charger Image',
            'Requested Date',
            'Assignment Date',
            'Status',
        ];
    }
}
