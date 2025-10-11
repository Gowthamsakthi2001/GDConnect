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
use Modules\Deliveryman\Entities\Deliveryman;
class LeaveLogDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
             ->filterColumn('deliveryman_info', function ($query, $keyword) {
                 $query->where('first_name', 'LIKE', "%$keyword%");
            })
            ->addColumn('deliveryman_info', function ($row) {
                $fullName = $row->first_name . ' ' . $row->last_name;
                $mobileNumber = $row->mobile_number;
                return $fullName && $mobileNumber ? "$fullName ($mobileNumber)" : 'N/A';
            })

            ->addColumn('work_type', function ($row) {
                return $row->work_status_handler();
            })
            ->addColumn('no_of_days', function ($row) {
                return $row->leave_no_of_days() ;
            })
            ->addColumn('taken_leave', function ($row) {
                return $row->take_leave_total();

            })
             ->addColumn('balance_leave', function ($row) {
                 return $row->balance_leave_total();

            })
             ->addColumn('permission_hrs', function ($row) {
                return $row->leave_total_permission_hr() ;

            })

            ->addColumn('action', function ($row) {
                return $row->leave_log_view_btn('leave-log-table');
            })
            
            ->rawColumns(['action', 'work_type', 'status'])
            ->setRowId('id')
            ->addIndexColumn();
    }

    public function query(): Builder
    {
        return Deliveryman::query()->orderBy('id', 'desc');
        
    }
    
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('leave-log-table')
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
            Column::make('work_type')->title('Work Type')->defaultContent('N/A')->searchable(true),
            Column::make('no_of_days')->title('No. Of Day/Hour')->defaultContent('N/A')->searchable(true),
            Column::make('taken_leave')->title('Taken Leaves')->defaultContent('N/A')->searchable(true),
            Column::make('balance_leave')->title('Balance Leaves')->defaultContent('N/A')->searchable(true),
            Column::make('permission_hrs')->title('Permission Hours')->defaultContent('N/A')->searchable(true),            Column::computed('action')->title('Action')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->width(80)
                ->addClass('text-center'),
        ];
    }

   
}