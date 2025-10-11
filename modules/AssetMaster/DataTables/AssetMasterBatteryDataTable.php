<?php

namespace Modules\AssetMaster\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Modules\AssetMaster\Entities\AssetMasterBattery;
use Carbon\Carbon;
use Modules\Deliveryman\Entities\Deliveryman; // Correctly placed here

class AssetMasterBatteryDataTable extends DataTable
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
                return $query->actionBtn('asset-master-battery');
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id')
            ->addIndexColumn();
    }


    public function query(AssetMasterBattery $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('asset-master-battery')
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
    
            Column::make('Battery_Model')
                ->title('Battery Model')
                ->defaultContent('N/A'),
    
            Column::make('Serial_Number')
                ->title('Serial Number')
                ->defaultContent('N/A'),
    
            Column::make('Engraved_Serial_Num')
                ->title('Engraved Serial Number')
                ->defaultContent('N/A'),
    
           
    
            
    
            Column::make('Assigned_To')
                ->title('Assigned To')
                ->defaultContent('N/A'),
    
            
    
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
        return 'AssetMasterBattery_' . date('YmdHis');
    }

    private function statusBtn($AssetMasterBattery)
    {
        $isChecked = $AssetMasterBattery->Status ? 'checked' : '';

        $status = '<div class="form-check form-switch">';
        $status .= '<label class="toggle-switch" for="statusCheckbox_' . $AssetMasterBattery->id . '">';
        $status .= '<input type="checkbox" onclick="status_change_alert(\'' . route('admin.Green-Drive-Ev.asset-master.asset_master_battery_change_status', [$AssetMasterBattery->id, $AssetMasterBattery->Status ? 0 : 1]) . '\', \'' . ($AssetMasterBattery->Status ? 'Deactivate' : 'Activate') . ' this AssetMasterBattery ?\', event)" class="form-check-input toggle-btn" id="statusCheckbox_' . $AssetMasterBattery->id . '" ' . $isChecked . '>';
        $status .= '<span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>';
        $status .= '</label></div>';

        return $status;
    }
}
