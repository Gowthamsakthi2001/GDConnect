<?php

namespace Modules\LeaveManagement\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Models\Holiday;
use Carbon\Carbon;

class HolidayDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->filterColumn('title', function($query, $keyword) {
                $query->where('title', 'LIKE', "%$keyword%");
            })
            ->filterColumn('type', function($query, $keyword) {
                $query->where('type', 'LIKE', "%$keyword%");
            })
            ->filterColumn('date', function($query, $keyword) {
                try {
                    if ($date = Carbon::createFromFormat('d-m-Y', $keyword)) {
                        $query->whereDate('date', $date->format('Y-m-d'));
                    }
                } catch (\Exception $e) {
                    $query->where('date', 'LIKE', "%$keyword%");
                }
            })
            ->filterColumn('status', function($query, $keyword) {
                $status = strtolower($keyword);
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->filterColumn('recurring', function($query, $keyword) {
                $recurring = strtolower($keyword);
                if ($recurring === 'yes') {
                    $query->where('is_recurring', true);
                } elseif ($recurring === 'no') {
                    $query->where('is_recurring', false);
                }
            })
            ->addColumn('date', function ($row) {
                return $row->date ? $row->date->format('d-m-Y') : '';
            })
            ->addColumn('type', function ($row) {
                $badgeClass = [
                    'national' => 'danger',
                    'state' => 'warning',
                    'regional' => 'info',
                    'company' => 'primary'
                ];
                return ucfirst($row->type);
            })
            ->addColumn('recurring', function ($row) {
                if ($row->recurring_group_id) {
                    return '<span class="badge bg-success">Yes (Grouped)</span>';
                }
                return $row->is_recurring 
                    ? 'Yes'
                    : 'No';
            })
            ->addColumn('status', function ($row) {
                return $row->is_active 
                    ? '<span class="btn btn-success btn-sm">Active</span>'
                    : '<span class="btn btn-danger btn-sm">Inactive</span>';
            })
            ->addColumn('action', function ($row) {
                return '<a href="'.route('admin.Green-Drive-Ev.leavemanagement.holidays.manage', ['holiday_id' => $row->id]).'" 
                        class="btn btn-success-soft btn-sm me-1 edit-btn">
                        <svg class="svg-inline--fa fa-pen-to-square" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="pen-to-square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path fill="currentColor" d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.8 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"></path>
                        </svg>
                    </a>
                    <button class="btn btn-danger-soft btn-sm delete-btn" data-id="'.$row->id.'">
                        <svg class="svg-inline--fa fa-trash" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="trash" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path fill="currentColor" d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"></path>
                        </svg>
                    </button>';
            })
            ->rawColumns(['type', 'recurring', 'status', 'action'])
            ->setRowId('id')
            ->addIndexColumn();
    }

    public function query(): QueryBuilder
    {
        $currentYear = Carbon::now()->year;
        
        return Holiday::query()
            ->where(function($query) use ($currentYear) {
                $query->whereNull('recurring_group_id')
                    ->orWhere(function($query) use ($currentYear) {
                        $query->whereNotNull('recurring_group_id')
                            ->whereNotNull('date')
                            ->whereYear('date', '<=', $currentYear)
                            ->whereIn('id', function($subQuery) use ($currentYear) {
                                $subQuery->selectRaw('MIN(id)')
                                    ->from('ev_master_holidays')
                                    ->whereNotNull('recurring_group_id')
                                    ->whereNotNull('date')
                                    ->whereYear('date', '<=', $currentYear)
                                    ->groupBy('recurring_group_id');
                            });
                    });
            })
            ->orderBy('date', 'desc');
    }
    
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('holiday-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>rt<'bottom'<'row'<'col-md-6'i><'col-md-6'p>>><'clear'>")
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'order' => [[1, 'desc']], // Default sort by date column
                'columnDefs' => array(
                    array(
                        'targets' => 1,
                        'type' => 'date-eu',
                        'render' => function($data, $type, $row) {
                            if ($type === 'sort') {
                                return $row['date'];
                            }
                            return $data;
                        }
                    ),
                    array(
                        'targets' => array(0, 6),
                        'orderable' => false,
                        'searchable' => false
                    )
                ),
                'headerCallback' => 'function(thead, data, start, end, display) {
                    $(thead).addClass("table-success");
                }',
                'initComplete' => 'function() {
                    var api = this.api();
                    
                    // Make the status filter dropdown
                    $(\'#holiday-table thead tr\').eq(1).find(\'th\').eq(4).html(
                        \'<select class="form-control form-control-sm status-filter">\'+
                        \'<option value="">All Status</option>\'+
                        \'<option value="active">Active</option>\'+
                        \'<option value="inactive">Inactive</option>\'+
                        \'</select>\'
                    );
                    
                    // Make the recurring filter dropdown
                    $(\'#holiday-table thead tr\').eq(1).find(\'th\').eq(3).html(
                        \'<select class="form-control form-control-sm recurring-filter">\'+
                        \'<option value="">All</option>\'+
                        \'<option value="yes">Yes</option>\'+
                        \'<option value="no">No</option>\'+
                        \'</select>\'
                    );
                    
                    // Apply the filter
                    $(".status-filter, .recurring-filter").change(function() {
                        api.draw();
                    });
                }',
                'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            ])
            ->buttons([
                Button::make('reset')->className('btn btn-success box-shadow--4dp btn-sm-menu'),
                Button::make('reload')->className('btn btn-success box-shadow--4dp btn-sm-menu'),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')
                ->title('SL')
                ->searchable(false)
                ->orderable(false)
                ->width(30)
                ->addClass('text-center'),
            Column::make('date')
                ->title('Date')
                ->addClass('text-center'),
            Column::make('title')
                ->title('Title'),
            Column::make('type')
                ->title('Type')
                ->addClass('text-center'),
            Column::make('recurring')
                ->title('Recurring')
                ->addClass('text-center'),
            Column::make('status')
                ->title('Status')
                ->addClass('text-center'),
            Column::computed('action')
                ->title('Action')
                ->width(120)
                ->addClass('text-center'),
        ];
    }
}