<?php

namespace Modules\LeaveManagement\DataTables;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Modules\LeaveManagement\Entities\LeaveType; 
use Modules\LeaveManagement\Entities\LeaveRequest; 
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
class LeaveRequestDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
             ->filterColumn('deliveryman_info', function ($query, $keyword) {
                $query->whereHas('deliveryman', function ($q) use ($keyword) {
                    $q->where('first_name', 'LIKE', "%$keyword%");
                });
            })
            ->addColumn('deliveryman_info', function ($row) {
                $fullName = optional($row->deliveryman)->first_name . ' ' . optional($row->deliveryman)->last_name;
                $mobileNumber = optional($row->deliveryman)->mobile_number;
                return $fullName && $mobileNumber ? "$fullName ($mobileNumber)" : 'N/A';
            })
            ->filterColumn('leave_name', function ($query, $keyword) {
                $query->whereHas('leave', function ($q) use ($keyword) {
                    $q->where('leave_name', 'LIKE', "%$keyword%");
                });
            })
            ->filterColumn('days', function ($query, $keyword) {
                $query->whereHas('leave', function ($q) use ($keyword) {
                    $q->where('days', 'LIKE', "%$keyword%");
                });
            })
            // ->addColumn('leave_name', function ($row) {
            //     return optional($row->leave)->leave_name ?? 'N/A';
            // })
            ->addColumn('leave_name', function ($row) {
                $leaveName = optional($row->leave)->leave_name;
                $shortName = optional($row->leave)->short_name;
            
                if ($leaveName) {
                    return $shortName ? "$leaveName ($shortName)" : $leaveName;
                }
            
                return 'N/A';
            })
    
            ->addColumn('days', function ($row) {
                return optional($row->leave)->days ?? 'N/A';
            })
             ->addColumn('start_date', function ($row) {
                return $row->start_date ? Carbon::parse($row->start_date)->format('d-m-Y') : 'N/A';

            })
             ->addColumn('end_date', function ($row) {
                return $row->end_date ? Carbon::parse($row->end_date)->format('d-m-Y') : 'N/A';

            })
            ->addColumn('apply_days', function ($row) {
                $leave_type = $row->apply_days.' '.$row->leave->leave_type;
                return $leave_type ?? 'N/A';

            })
            ->addColumn('action', function ($row) {
                return $row->actionBtn('client-table');
            })
            ->rawColumns(['action']) // Only action has HTML
            ->setRowId('id')
            ->addIndexColumn();
    }



    public function query(): Builder
    {
        // return LeaveRequest::query()->with(['deliveryman','leave'])->where('req_status',1)->orderBy('id', 'desc');
        return LeaveRequest::query()
        ->leftJoin('ev_leave_types as b', 'ev_leave_requests.leave_id', '=', 'b.id')
        ->with(['deliveryman', 'leave'])
        ->where('ev_leave_requests.req_status', 1)
        ->where('b.leave_type', 'day')
        ->orderBy('ev_leave_requests.id', 'desc')
        ->select('ev_leave_requests.*');
    }
    
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('leave-request-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>rt<'bottom'<'row'<'col-md-6'i><'col-md-6'p>>><'clear'>")
            // ->orderBy(5)
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'headerCallback' => 'function(thead, data, start, end, display) {
                    $(thead).addClass("table-success");
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
            Column::make('DT_RowIndex')->title('SL')->searchable(false)->orderable(false)->width(30)->addClass('text-center'),
    
            Column::computed('deliveryman_info')
                ->title('Deliveryman Info')
                ->defaultContent('N/A')
                ->searchable(true)
                ->orderable(false),
    
            Column::make('leave.leave_name')->title('Leave Name')->defaultContent('N/A')->searchable(true),
            Column::make('leave.days')->title('No Of Days')->defaultContent('N/A')->searchable(true),
            Column::make('start_date')->title('Start Date')->defaultContent('N/A')->searchable(true),
            Column::make('end_date')->title('End Date')->defaultContent('N/A')->searchable(true),
            Column::make('apply_days')->title('Applied')->defaultContent('N/A')->searchable(true),
            Column::computed('action')->title('Action')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->width(80)
                ->addClass('text-center'),
        ];
    }

}