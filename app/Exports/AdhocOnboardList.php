<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Modules\Deliveryman\Entities\Deliveryman;

class AdhocOnboardList implements FromCollection, WithHeadings, WithMapping
{
    protected $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

   public function collection()
    {
        return Deliveryman::with('zone', 'get_approved_by')
            ->where('work_type', 'adhoc')
            ->when($this->type !== 'all', function ($query) {
                return $query->where('approved_status', 
                    $this->type === 'approve' ? 1 : 
                    ($this->type === 'deny' ? 2 : 0)
                );
            })
            ->orderBy('id', 'desc')
            ->get();
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
            'Adhoc',
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
