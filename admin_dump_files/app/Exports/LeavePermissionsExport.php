<?php

namespace App\Exports;

use Modules\LeaveManagement\Entities\LeaveRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class LeavePermissionsExport implements FromCollection, WithHeadings, WithMapping
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
            ->whereNotNull('permission_date')
            ->where('dm_id', $this->deliverymanId)
            ->where('approve_status', 1)
            ->get();
    }


    public function map($leaveRequest): array
    {
        return [
            $leaveRequest->leave->leave_name ?? 'N/A',
            $leaveRequest->permission_date ? Carbon::parse($leaveRequest->permission_date)->format('d-m-Y') : 'N/A',
            $leaveRequest->start_time ? Carbon::parse($leaveRequest->start_time)->format('H:i:s') : 'N/A',
            $leaveRequest->end_time ? Carbon::parse($leaveRequest->end_time)->format('H:i:s') : 'N/A',
            $leaveRequest->permission_hr ?? 0,
            $leaveRequest->remarks ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'Permission Name',
            'Permission Date',
            'Start Time',
            'End Time',
            'Total Time (Hours)',
            'Description',
        ];
    }
}
