<?php

namespace Modules\AssetMaster\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Modules\AssetMaster\Entities\ManufacturerMaster;

class ManufactureMasterDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('status', function ($query) {
                return $this->statusBtn($query);
            })
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
            })
            ->editColumn('updated_at', function ($row) {
                return \Carbon\Carbon::parse($row->updated_at)->format('Y-m-d');
            })
            ->addColumn('action', function ($query) {
                return $query->actionBtn('manufacture-master-table');
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id')
            ->addIndexColumn();
    }

    public function query(ManufacturerMaster $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('manufacture-master-table')
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
            Column::make('manufacturer_name')->title('Manufacturer Name')->defaultContent('N/A'),
            Column::make('Address_line_1')->title('Address Line 1')->defaultContent('N/A'),
            Column::make('Address_line_2')->title('Address Line 2')->defaultContent('N/A'),
            Column::make('Address_line_3')->title('Address Line 3')->defaultContent('N/A'),
            Column::make('Country')->title('Country')->defaultContent('N/A'),
            Column::make('State')->title('State')->defaultContent('N/A'),
            Column::make('Phone')->title('Phone')->defaultContent('N/A'),
            Column::make('Contact_Name')->title('Contact Name')->defaultContent('N/A'),
            Column::make('Web_site_URL')->title('Website URL')->defaultContent('N/A'),
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
        return 'ManufacturerMaster_' . date('YmdHis');
    }

    private function statusBtn($ManufacturerMaster)
{
    $isChecked = $ManufacturerMaster->status ? 'checked' : '';

    $status = '<div class="form-check form-switch">';
    $status .= '<label class="toggle-switch" for="statusCheckbox_' . $ManufacturerMaster->id . '">';
    $status .= '<input type="checkbox" onclick="status_change_alert(\'' . route('admin.Green-Drive-Ev.asset-master.manufacturer_master_change_status', [$ManufacturerMaster->id, $ManufacturerMaster->status ? 0 : 1]) . '\', \'' . ($ManufacturerMaster->status ? 'Deactivate' : 'Activate') . ' this ManufacturerMaster ?\', event)" class="form-check-input toggle-btn" id="statusCheckbox_' . $ManufacturerMaster->id . '" ' . $isChecked . '>';
    $status .= '<span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>';
    $status .= '</label></div>';

    return $status;
}

}
