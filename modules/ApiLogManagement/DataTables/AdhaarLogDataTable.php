<?php

namespace Modules\ApiLogManagement\DataTables;
use App\Models\EvAdhaarOtpLog;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Button;
class AdhaarLogDataTable extends DataTable
{
    public function dataTable(EloquentBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->filterColumn('request_id', function($query, $keyword) {
                $query->where('ev_adhaar_otp_logs.request_id', 'LIKE', "%{$keyword}%");
            })
            ->filterColumn('adhaar_no', function($query, $keyword) {
                $query->where('ev_adhaar_otp_logs.adhaar_no', 'LIKE', "%{$keyword}%");
            })
            ->addColumn('adhaar_no', function ($row) {
                return $row->adhaar_no ?? 'N/A'; 
            })
            ->addColumn('request_id', function ($row) {
                return $row->request_id ?? 'N/A'; 
            })
            ->addColumn('ref_id', function ($row) {
                return $row->ref_id ?? 'N/A'; 
            })
            ->addColumn('message', function ($row) {
                return $row->message ?? 'N/A'; 
            })
            ->setRowId('id')
            ->addIndexColumn();
    }

    public function query(): EloquentBuilder
    {
        return EvAdhaarOtpLog::query()->orderBy('id', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('leave-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>rt<'bottom'<'row'<'col-md-6'i><'col-md-6'p>>><'clear'>")
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'headerCallback' => 'function(thead, data, start, end, display) {
                    $(thead).addClass("table-success");
                }',
                'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
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
            Column::make('adhaar_no')->title('Adhaar No')->defaultContent('N/A'),
            Column::make('request_id')->title('Request ID')->defaultContent('N/A'),
            Column::make('ref_id')->title('Reference ID')->defaultContent('N/A'),
            Column::make('message')->title('Message')->defaultContent('N/A'),
        ];
    }
}   