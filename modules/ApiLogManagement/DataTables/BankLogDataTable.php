<?php

namespace Modules\ApiLogManagement\DataTables;
use App\Models\EvAdhaarOtpLog;
use App\Models\EvLicenseVerifyLog;
use App\Models\EvBankVerifyLog;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Button;
class BankLogDataTable extends DataTable
{
    public function dataTable(EloquentBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->filterColumn('request_id', function($query, $keyword) {
                $query->where('ev_bank_verify_log.request_id', 'LIKE', "%{$keyword}%");
            })
            ->filterColumn('beneficiary_account', function($query, $keyword) {
                $query->where('ev_bank_verify_log.beneficiary_account', 'LIKE', "%{$keyword}%");
            })
            ->addColumn('request_id', function ($row) {
                return $row->request_id ?? 'N/A'; 
            })
            ->addColumn('beneficiary_account', function ($row) {
                return $row->beneficiary_account ?? 'N/A'; 
            })
            ->addColumn('beneficiary_name', function ($row) {
                return $row->beneficiary_name ?? 'N/A'; 
            })
            ->addColumn('beneficiary_ifsc', function ($row) {
                return $row->beneficiary_ifsc ?? 'N/A'; 
            })
            ->addColumn('bank_name', function ($row) {
                return $row->bank_name ?? 'N/A'; 
            })
            ->addColumn('branch_name', function ($row) {
                return $row->branch_name ?? 'N/A'; 
            })
            ->addColumn('status', function ($row) {
                return $row->account_status ?? 'N/A'; 
            })
            ->addColumn('message', function ($row) {
                return $row->message ?? 'N/A'; 
            })
            ->setRowId('id')
            ->addIndexColumn();
    }

    public function query(): EloquentBuilder
    {
        return EvBankVerifyLog::query()->orderBy('id', 'desc');
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
            Column::make('beneficiary_account')->title('Account No')->defaultContent('N/A'),
            Column::make('beneficiary_name')->title('Holder Name')->defaultContent('N/A'),
            Column::make('beneficiary_ifsc')->title('IFSC Code')->defaultContent('N/A'),
            Column::make('bank_name')->title('Bank Name')->defaultContent('N/A'),
            Column::make('branch_name')->title('Branch Name')->defaultContent('N/A'),
            Column::make('status')->title('Status')->defaultContent('N/A'),
            Column::make('message')->title('Message')->defaultContent('N/A'),
        ];
    }
}   