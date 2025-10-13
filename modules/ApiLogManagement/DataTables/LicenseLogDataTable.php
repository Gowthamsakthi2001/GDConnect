<?php

namespace Modules\ApiLogManagement\DataTables;
use App\Models\EvAdhaarOtpLog;
use App\Models\EvLicenseVerifyLog;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Button;
class LicenseLogDataTable extends DataTable
{
    public function dataTable(EloquentBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->filterColumn('request_id', function($query, $keyword) {
                $query->where('ev_license_verify_log.request_id', 'LIKE', "%{$keyword}%");
            })
            ->filterColumn('license_number', function($query, $keyword) {
                $query->where('ev_license_verify_log.license_number', 'LIKE', "%{$keyword}%");
            })
            ->addColumn('request_id', function ($row) {
                return $row->request_id ?? 'N/A'; 
            })
            ->addColumn('license_number', function ($row) {
                return $row->license_number ?? 'N/A'; 
            })
            ->addColumn('holder_name', function ($row) {
                return $row->holder_name ?? 'N/A'; 
            })
            ->addColumn('gender', function ($row) {
                return $row->gender ?? 'N/A'; 
            })
            ->addColumn('rto', function ($row) {
                return $row->rto ?? 'N/A'; 
            })
            ->addColumn('rto_code', function ($row) {
                return $row->rto_code ?? 'N/A'; 
            })
            // ->addColumn('state', function ($row) {
            //     return $row->state ?? 'N/A'; 
            // })
            ->addColumn('vehicle_class', function ($row) {
                return $row->get_vehicle_class() ?? 'N/A'; 
            })
            ->addColumn('message', function ($row) {
                return $row->message ?? 'N/A'; 
            })
            ->setRowId('id')
            ->addIndexColumn();
    }

    public function query(): EloquentBuilder
    {
        return EvLicenseVerifyLog::query()->orderBy('id', 'desc');
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
            Column::make('request_id')->title('Request ID')->defaultContent('N/A'),
            Column::make('license_number')->title('License Number')->defaultContent('N/A'),
            Column::make('holder_name')->title('Holder Name')->defaultContent('N/A'),
            Column::make('gender')->title('Gender')->defaultContent('N/A'),
            Column::make('rto')->title('RTO')->defaultContent('N/A'),
            Column::make('rto_code')->title('RTO Code')->defaultContent('N/A'),
            // Column::make('state')->title('State')->defaultContent('N/A'),
            Column::make('vehicle_class')->title('Vehicle Class')->defaultContent('N/A'),
            Column::make('message')->title('Message')->defaultContent('N/A'),
        ];
    }
}   