<?php

namespace Modules\LeaveManagement\DataTables;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Modules\LeaveManagement\Entities\LeaveType; 
use Illuminate\Database\Eloquent\Builder;
class LeaveDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->filterColumn('leave_name', function($query, $keyword) {
                $query->where('leave_name', 'LIKE', "%$keyword%");
            })
            ->filterColumn('short_name', function($query, $keyword) {
                $query->where('short_name', 'LIKE', "%$keyword%");
            })
            ->filterColumn('days', function($query, $keyword) {
                $query->where('days', 'LIKE', "%$keyword%");
            })
            ->addColumn('leave_name', function ($row) {
                return $row->leave_name ?? 'N/A'; 
            })
            ->addColumn('short_name', function ($row) {
                return $row->short_name ?? 'N/A'; 
            })
            ->addColumn('days', function ($row) {
                $type_base = $row->days .' ('.$row->leave_type.')';
                return $type_base ?? 'N/A'; 
            })
            ->addColumn('action', function ($query) {
                return $query->actionBtn('client-table');
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id')
            ->addIndexColumn();
    }


    public function query(): Builder
    {
        return LeaveType::query()->where('status',1)->orderBy('id', 'desc');
    }
    
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('leave-table')
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
            Column::make('leave_name')->title('Leave Name')->defaultContent('N/A'),
            Column::make('short_name')->title('Short Name')->defaultContent('N/A'),
            Column::make('days')->title('Type Base')->defaultContent('N/A'),
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