<?php

namespace Modules\AssetMaster\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Modules\AssetMaster\Entities\AssetMasterCharger;
use Carbon\Carbon;
use Modules\Deliveryman\Entities\Deliveryman; // Correctly placed here

class AssetMasterChargerDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('dm_id', function ($row) {
                // Fetch delivery man data
                $deliveryman = Deliveryman::find($row->dm_id);
                return $deliveryman ? $deliveryman->first_name . ' ' . $deliveryman->last_name : 'N/A';
            })
            ->editColumn('status', function ($query) {
                return $this->statusBtn($query);
            })
            ->editColumn('In_Use_Date', function ($row) {
                return $row->In_Use_Date ? Carbon::parse($row->In_Use_Date)->format('Y-m-d') : 'N/A';
            })
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
            })
            ->editColumn('updated_at', function ($row) {
                return \Carbon\Carbon::parse($row->updated_at)->format('Y-m-d');
            })
            ->addColumn('action', function ($query) {
                return $query->actionBtn('asset-master-charger');
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id')
            ->addIndexColumn();
    }


    public function query(AssetMasterCharger $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('asset-master-charger')
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
            Column::make('DT_RowIndex')
                ->title('SL')
                ->searchable(false)
                ->orderable(false)
                ->width(30)
                ->addClass('text-center'),
    
            Column::make('AMS_Location')
                ->title('Ams Location')
                ->defaultContent('N/A'),
    
            Column::make('PO_ID')
                ->title('Po Number')
                ->defaultContent('N/A'),
    
            Column::make('Invoice_Number')
                ->title('Invoice Number')
                ->defaultContent('N/A'),
    
            Column::make('Charger_Model')
                ->title('Charger Model')
                ->defaultContent('N/A'),
    
            Column::make('Serial_Number')
                ->title('Serial Number')
                ->defaultContent('N/A'),
    
            Column::make('Engraved_Serial_Num')
                ->title('Engraved Serial Number')
                ->defaultContent('N/A'),
    
            // Column::make('Sub_status')
            //     ->title('Sub Status')
            //     ->defaultContent('N/A'),
    
            Column::make('In_Use_Date')
                ->title('In Use Date')
                ->defaultContent('N/A'),
    
            Column::make('Assigned_to')
                ->title('Assigned To')
                ->defaultContent('N/A'),
    
            // Column::make('dm_id')
            //     ->title('Delivery Man Name'), // No need to specify logic here, it's in the dataTable method
    
            Column::make('status')
                ->title('Status')
                ->orderable(false)
                ->searchable(false),
    
            Column::computed('action')
                ->title('Action')
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
        return 'AssetMasterCharger_' . date('YmdHis');
    }

    private function statusBtn($AssetMasterCharger)
    {
        $isChecked = $AssetMasterCharger->Status ? 'checked' : '';

        $status = '<div class="form-check form-switch">';
        $status .= '<label class="toggle-switch" for="statusCheckbox_' . $AssetMasterCharger->id . '">';
        $status .= '<input type="checkbox" onclick="status_change_alert(\'' . route('admin.Green-Drive-Ev.asset-master.asset_master_charger_change_status', [$AssetMasterCharger->id, $AssetMasterCharger->Status ? 0 : 1]) . '\', \'' . ($AssetMasterCharger->Status ? 'Deactivate' : 'Activate') . ' this AssetMasterCharger ?\', event)" class="form-check-input toggle-btn" id="statusCheckbox_' . $AssetMasterCharger->id . '" ' . $isChecked . '>';
        $status .= '<span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>';
        $status .= '</label></div>';

        return $status;
    }
}
