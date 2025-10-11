<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\HRStatus\Entities\RiderOnboardingLog;
use Modules\MasterManagement\Entities\CustomerOperationalHub;
use Carbon\Carbon;

class RiderOnboardLogExport implements FromCollection, WithHeadings, WithMapping
{
    protected $status;
    protected $from_date;
    protected $to_date;
    protected $timeline;
    protected $selectedIds;
    protected $selectedFields;
    protected $dm_id;
    protected $c_id;

    public function __construct($status, $from_date, $to_date, $timeline, $selectedIds = [], $selectedFields = [],$dm_id,$c_id)
    {
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->timeline = $timeline;
        $this->selectedIds = array_filter($selectedIds) ?? [];
        $this->dm_id = $dm_id;
        $this->c_id = $c_id;
        $this->selectedFields = array_map(function ($field) {
            return is_array($field) && isset($field['name']) ? $field['name'] : $field;
        }, array_filter($selectedFields));
    }


    public function collection()
    {
        $query = RiderOnboardingLog::with(['deliveryman', 'customer', 'createdBy.get_role' ,'location']);

        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        } else {
            if (in_array($this->status, ['deliveryman', 'adhoc', 'helper', 'in-house']) && $this->status !== 'all') {
                $query->where('role_type', $this->status);
            }

            if ($this->timeline) {
                match ($this->timeline) {
                    'today'      => $query->whereDate('created_at', today()),
                    'this_week'  => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                    'this_month' => $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]),
                    'this_year'  => $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]),
                };
            } else {
                if ($this->from_date) {
                    $query->whereDate('created_at', '>=', $this->from_date);
                }
                if ($this->to_date) {
                    $query->whereDate('created_at', '<=', $this->to_date);
                }
            }
        }
        if (!empty($this->dm_id)) {
            $query->where('dm_id', $this->dm_id);
        }
        if (!empty($this->c_id)) {
            $query->where('customer_master_id', $this->c_id);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function map($row): array
    {
        $mapped = [];
        $roleType = $row->role_type ?? null;
        $roleTypeName = match ($roleType) {
            'deliveryman' => 'Rider',
            'adhoc'       => 'Adhoc',
            'helper'      => 'Helper',
            'in-house'    => 'Employee',
            default       => 'N/A',
        };

        foreach ($this->selectedFields as $key) {
            switch ($key) {
                case 'rider_id':
                    $mapped[] = $row->deliveryman->emp_id ?? '-';
                    break;
                case 'role_type':
                    $mapped[] = $roleTypeName ?? '-';
                    break;
                case 'name':
                case 'rider_name':
                    $mapped[] = trim(($row->deliveryman->first_name ?? '') . ' ' . ($row->deliveryman->last_name ?? '')) ?: '-';
                    break;
                case 'client_id':
                    $mapped[] = $row->customer->id ?? '-';
                    break;
                case 'client_name':
                    $mapped[] = $row->customer->name ?? '-';
                    break;
                case 'city':
                    $mapped[] = $row->location->name ?? '-';
                    break;
                case 'hub':
                    $hub = CustomerOperationalHub::find($row->hub_id); // or use ->where('id', $row->hub_id)->first()
                    $mapped[] = $hub->hub_name ?? '-';
                    break;
                case 'onboarded_date':
                    $mapped[] = $row->onboard_date
                        ? Carbon::parse($row->onboard_date)->format('d M Y')
                        : '-';
                    break;
                 case 'remarks':
                    $mapped[] = $row->remarks ?? '-';
                    break;
                case 'created_by':
                    $mapped[] = optional($row->createdBy)->name
                        ? $row->createdBy->name .
                            (optional($row->createdBy->get_role)->name
                                ? ' (' . $row->createdBy->get_role->name . ')'
                                : '')
                        : '-';
                    break;
                case 'created_at':
                    $mapped[] = $row->created_at
                        ? Carbon::parse($row->created_at)->format('d M Y h:i:s A')
                        : '-';
                    break;
                default:
                    $mapped[] = '-';
            }
        }

        return $mapped;
    }

    public function headings(): array
{
    $headers = [];

    foreach ($this->selectedFields as $field) {
        $fieldName = is_array($field) && isset($field['name']) ? $field['name'] : $field;
        $headers[] = ucfirst(str_replace('_', ' ', $fieldName));
    }

    return $headers;
}

}
