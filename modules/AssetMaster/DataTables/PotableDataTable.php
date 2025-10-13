<?php

namespace Modules\AssetMaster\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Modules\AssetMaster\Entities\PoTable;
use Carbon\Carbon;

class PotableDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('status', function ($query) {
                return $this->statusBtn($query);
            })
             ->editColumn('PO_Date', function ($row) {
                return $row->PO_Date ? Carbon::parse($row->PO_Date)->format('Y-m-d') : 'N/A';
            })
            ->editColumn('Delivery_Date', function ($row) {
                return $row->Delivery_Date ? Carbon::parse($row->Delivery_Date)->format('Y-m-d') : 'N/A';
            })
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
            })
            ->editColumn('updated_at', function ($row) {
                return \Carbon\Carbon::parse($row->updated_at)->format('Y-m-d');
            })
            ->addColumn('action', function ($query) {
                return $query->actionBtn('po-table');
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id')
            ->addIndexColumn();
    }

    public function query(PoTable $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('po-table')
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
            Column::make('AMS_Location')->title('Ams Location')->defaultContent('N/A'),
            Column::make('PO_Number')->title('Po Number')->defaultContent('N/A'),
            Column::make('Supplier_Name')->title('Supplier Name')->defaultContent('N/A'),
            Column::make('Description')->title('Description')->defaultContent('N/A'),
            Column::make('Manufacturer')->title('Manufacturer')->defaultContent('N/A'),
            Column::make('PO_Date')->title('PO Date')->defaultContent('N/A'),
            Column::make('Other_Amount')->title('Other Amount')->defaultContent('N/A'),
            Column::make('Tax_Amount')->title('Tax Amount')->defaultContent('N/A'),
            Column::make('Delivery_Date')->title('Delivery Date')->defaultContent('N/A'),
            Column::make('status')->title('status')->orderable(false)->searchable(false),
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
        return 'PoTable_' . date('YmdHis');
    }

    private function statusBtn($PoTable)
{
    $isChecked = $PoTable->status ? 'checked' : '';

    $status = '<div class="form-check form-switch">';
    $status .= '<label class="toggle-switch" for="statusCheckbox_' . $PoTable->id . '">';
    $status .= '<input type="checkbox" onclick="status_change_alert(\'' . route('admin.Green-Drive-Ev.asset-master.po_table_change_status', [$PoTable->id, $PoTable->status ? 0 : 1]) . '\', \'' . ($PoTable->status ? 'Deactivate' : 'Activate') . ' this PoTable ?\', event)" class="form-check-input toggle-btn" id="statusCheckbox_' . $PoTable->id . '" ' . $isChecked . '>';
    $status .= '<span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>';
    $status .= '</label></div>';

    return $status;
}

}
