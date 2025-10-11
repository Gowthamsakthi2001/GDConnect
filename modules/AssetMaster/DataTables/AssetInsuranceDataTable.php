<?php

namespace Modules\AssetMaster\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Modules\AssetMaster\Entities\AssetInsuranceDetails;
use Carbon\Carbon;

class AssetInsuranceDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->editColumn('Start_date_OD', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
            })
            ->editColumn('Start_date_3rd_party', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
            })
            ->editColumn('End_date_3rd_party', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
            })
            ->editColumn('End_date_OD', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
            })
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
            })
            ->editColumn('updated_at', function ($row) {
                return \Carbon\Carbon::parse($row->updated_at)->format('Y-m-d');
            })
            ->addColumn('action', function ($query) {
                return $query->actionBtn('asset-insurance-details');
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id')
            ->addIndexColumn();
    }

    public function query(AssetInsuranceDetails $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('asset-insurance-details')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>rt<'bottom'<'row'<'col-md-6'i><'col-md-6'p>>><'clear'>")
            ->orderBy(5)
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
            Column::make('vehicle_reg_no')->title('Vehicle Registration Number')->defaultContent('N/A'),
            Column::make('Insurance_Vendor_3rd_party')->title('Insurance Vendor (3rd Party)')->defaultContent('N/A'),
            Column::make('Start_date_3rd_party')->title('Start Date (3rd Party)')->defaultContent('N/A'),
            Column::make('End_date_3rd_party')->title('End Date (3rd Party)')->defaultContent('N/A'),
            Column::make('Declared_Value_3rd_party')->title('Declared Value (Own Damage)')->defaultContent('N/A'),
            Column::make('Policy_Number_OD')->title('Policy Number (Own Damage))')->defaultContent('N/A'),
            Column::make('Start_date_OD')->title('Start Date (Own Damage)')->defaultContent('N/A'),
            Column::make('End_date_OD')->title('End Date (Own Damage)')->defaultContent('N/A'),
            Column::make('Declared_Value_OD')->title('Declared Value (Own Damage)')->defaultContent('N/A'),
            Column::make('Insurance_Status_OD')->title('Insurance Status (Own Damage)')->defaultContent('N/A'),
            Column::computed('action')->title('Action')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->width(80)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'AssetInsuranceDetails_' . date('YmdHis');
    }

//     private function statusBtn($AssetInsuranceDetails)
// {
//     $isChecked = $AssetInsuranceDetails->status ? 'checked' : '';

//     $status = '<div class="form-check form-switch">';
//     $status .= '<label class="toggle-switch" for="statusCheckbox_' . $AssetInsuranceDetails->id . '">';
//     $status .= '<input type="checkbox" onclick="status_change_alert(\'' . route('admin.Green-Drive-Ev.asset-master.po_table_change_status', [$AssetInsuranceDetails->id, $AssetInsuranceDetails->status ? 0 : 1]) . '\', \'' . ($AssetInsuranceDetails->status ? 'Deactivate' : 'Activate') . ' this PoTable ?\', event)" class="form-check-input toggle-btn" id="statusCheckbox_' . $AssetInsuranceDetails->id . '" ' . $isChecked . '>';
//     $status .= '<span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>';
//     $status .= '</label></div>';

//     return $status;
// }

}
