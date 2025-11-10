<?php

namespace Modules\AssetMaster\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Modules\AssetMaster\Entities\ModelMasterCharger;

class ModalMasterChargerDataTable extends DataTable
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
                return $query->actionBtn('modal-master-charger-table');
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id')
            ->addIndexColumn();
    }

    public function query(ModelMasterCharger $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('modal-master-charger-table')
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
        
        // Add columns for the fields you mentioned
        Column::make('name')->title('Name')->searchable(true)->orderable(true),
        Column::make('manufacturer_name')->title('Manufacturer Name')->searchable(true)->orderable(true),
        Column::make('nominal_c_rating')->title('Nominal C Rating')->searchable(true)->orderable(true),
        Column::make('charging_mode')->title('Charging Mode')->searchable(true)->orderable(true),
        Column::make('output_voltage')->title('Output Voltage')->searchable(true)->orderable(true),
        Column::make('output_current')->title('Output Current')->searchable(true)->orderable(true),
        Column::make('input_voltage')->title('Input Voltage')->searchable(true)->orderable(true),
        Column::make('input_current')->title('Input Current')->searchable(true)->orderable(true),
        Column::make('connector_rating')->title('Connector Rating')->searchable(true)->orderable(true),
        Column::make('status')->title('Status')->orderable(false)->searchable(false),
        // Action column (for edit, delete actions, etc.)
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
        return 'ModelMasterCharger_' . date('YmdHis');
    }

    private function statusBtn($ModelMasterCharger): string
    {
        $isChecked = $ModelMasterCharger->status ? 'checked' : '';

        $status = '<div class="form-check form-switch">';
        $status .= '<label class="toggle-switch" for="statusCheckbox_' . $ModelMasterCharger->id . '">';
        $status .= '<input type="checkbox" onclick="status_change_alert(\'' . route('admin.Green-Drive-Ev.asset-master.model_master_charger_change_status', [$ModelMasterCharger->id, $ModelMasterCharger->status ? 0 : 1]) . '\', \'' . ($ModelMasterCharger->status ? 'Deactivate' : 'Activate') . ' this Master Modal Charger ?\', event)" class="form-check-input toggle-btn" id="statusCheckbox_' . $ModelMasterCharger->id . '" ' . $isChecked . '>';
        $status .= '<span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>';
        $status .= '</label></div>';

        return $status;
    }
}
