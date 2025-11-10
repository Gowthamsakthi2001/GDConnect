<?php

namespace App\Exports;

use Modules\LeaveManagement\Entities\LeaveRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class exportLeaveRejectListExport implements FromCollection, WithHeadings, WithMapping
{
    protected $year;
    protected $deliverymanId;

    public function __construct($year, $deliverymanId)
    {
        $this->year = $year;
        $this->deliverymanId = $deliverymanId;
    }

    public function collection()
    {
        return LeaveRequest::with('leave')
            ->where('dm_id', $this->deliverymanId)
            ->where('reject_status', 1)
            ->get();
    }


    public function map($leaveRequest): array
    {
        return [
            $leaveRequest->leave->leave_name ?? 'N/A',
            $leaveRequest->start_date ? Carbon::parse($leaveRequest->start_date)->format('d-m-Y') : 'N/A',
            $leaveRequest->end_date ? Carbon::parse($leaveRequest->end_date)->format('d-m-Y') : 'N/A',
            ($leaveRequest->apply_days ?? 0) . ' Days' ?? 0,
            $leaveRequest->permission_date ? Carbon::parse($leaveRequest->permission_date)->format('d-m-Y') : 'N/A',
            $leaveRequest->start_time ? Carbon::parse($leaveRequest->start_time)->format('H:i:s') : 'N/A',
            $leaveRequest->end_time ? Carbon::parse($leaveRequest->end_time)->format('H:i:s') : 'N/A',
            ($leaveRequest->permission_hr ?? 0) . ' Hours',
            $leaveRequest->rejection_reason ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'Leave Name',
            'Start Date',
            'End Date',
            'Apply Days',
            'Permission Date',
            'Start Time',
            'End Time',
            'Permission Time',
            'Reject Reason',
        ];
    }
}
