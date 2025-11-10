<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Modules\Deliveryman\Entities\Deliveryman;
use Illuminate\Support\Facades\DB;
class DeliverymanOnboardlist implements FromCollection, WithHeadings, WithMapping
{
     protected $type;
     protected $city_id;
     protected $zone_id;
     protected $client_id;

    public function __construct($type,$city_id,$zone_id,$client_id)
    {
        $this->type = $type;
        $this->city_id = $city_id;
        $this->zone_id = $zone_id;
        $this->client_id = $client_id;
    }

public function collection()
{
    // DB::enableQueryLog();
    // dd($this->type,$this->city_id,$this->zone_id,$this->client_id);
    $query = Deliveryman::with('zone', 'get_approved_by')
        ->where('work_type', 'deliveryman')
        ->when($this->type !== 'all', function ($query) {
            return $query->where('approved_status', 
                $this->type === 'approve' ? 1 : 
                ($this->type === 'deny' ? 2 : 0)
            );
        })
        ->when($this->city_id, function ($query) {
            return $query->where('current_city_id', $this->city_id);
        })
        ->when($this->zone_id, function ($query) {
            return $query->where('zone_id', $this->zone_id);
        })
        ->when($this->client_id, function ($query) {
            return $query->where('client_id', $this->client_id);
        });

    $results = $query->orderBy('id', 'desc')->get();

    // dd(DB::getQueryLog()); // Shows full SQL with bindings applied

    return $results;
}

    public function map($deliveryman): array
    {
        $status = '';
        if($deliveryman->approved_status == 1){
            $status = 'Approved';
        }else if($deliveryman->approved_status == 2){
            $status = 'Rejected';
        }else{
            $status = 'Pending';
        }

        return [
            ($deliveryman->first_name ?? '-') . ' ' . ($deliveryman->last_name ?? '-'),
            $deliveryman->emp_id ?? '-',
            "'" . ($deliveryman->mobile_number ?? '-'),
            'Deliveryman',
            $deliveryman->current_city->city_name ?? '-',
            $deliveryman->zone->name ?? '-',
            $deliveryman->rider_status == 1 ? 'On' : 'Off',
            $deliveryman->get_last_login_date_for_all($deliveryman->id) ?? '-',
            $deliveryman->aadhar_verify == 1 ? 'Yes' : 'No',
            $deliveryman->pan_verify == 1 ? 'Yes' : 'No',
            $deliveryman->bank_verify == 1 ? 'Yes' : 'No',
            $deliveryman->lisence_verify == 1 ? 'Yes' : 'No',
            $status,
            ucfirst($deliveryman->approver_role ?? '-'),
            ucfirst($deliveryman->get_approved_by->name ?? '-'),
            $deliveryman->deny_remarks ?? '-',
            $deliveryman->job_status ? ucfirst($deliveryman->job_status) : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'ID',
            'Mobile Number',
            'Work Type',
            'City',
            'Zone',
            'Rider Status',
            'Last Login Date',
            'Aadhar Verified',
            'Pan Verified',
            'Bank Verified',
            'License Verified',
            'Approved Status',
            'Approved Role',
            'Approved By',
            'Remarks',
            'Job Status'
        ];
    }
}
