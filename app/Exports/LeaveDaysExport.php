<?php

namespace App\Exports;

use Modules\LeaveManagement\Entities\LeaveRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class LeaveDaysExport implements FromCollection, WithHeadings, WithMapping
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
            ->whereNotNull('start_date')
            ->where('dm_id', $this->deliverymanId)
            ->where('approve_status', 1)
            ->get();
    }


    public function map($leaveRequest): array
    {
        return [
            $leaveRequest->leave->leave_name ?? 'N/A',
            $leaveRequest->start_date ? Carbon::parse($leaveRequest->start_date)->format('d-m-Y') : 'N/A',
            $leaveRequest->end_date ? Carbon::parse($leaveRequest->end_date)->format('d-m-Y') : 'N/A',
            $leaveRequest->apply_days ?? 0,
            $leaveRequest->remarks ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'Leave Name',
            'Start Date',
            'End Date',
            'Apply Days',
            'Description',
        ];
    }
}
