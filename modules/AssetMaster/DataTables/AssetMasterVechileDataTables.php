<?php

namespace Modules\AssetMaster\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Carbon\Carbon;
use Modules\Deliveryman\Entities\Deliveryman; // Correctly placed here

class AssetMasterVechileDataTables extends DataTable
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
            ->editColumn('Asset_In_Use_Date', function ($row) {
                return $row->Asset_In_Use_Date ? Carbon::parse($row->Asset_In_Use_Date)->format('Y-m-d') : 'N/A';
            })
            ->editColumn('Lease_Rental_End_Date', function ($row) {
                return $row->Lease_Rental_End_Date ? Carbon::parse($row->Lease_Rental_End_Date)->format('Y-m-d') : 'N/A';
            })
             ->editColumn('Procurement_Lease_Start_Date', function ($row) {
                return $row->Procurement_Lease_Start_Date ? Carbon::parse($row->Procurement_Lease_Start_Date)->format('Y-m-d') : 'N/A';
            })
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
            })
            ->editColumn('updated_at', function ($row) {
                return \Carbon\Carbon::parse($row->updated_at)->format('Y-m-d');
            })
            ->addColumn('asset_status', function ($row) {
                return $row->asset_status->status_name ?? ''; 
            })
            ->addColumn('is_swappable', function ($row) {
                $status = '';
                if($row->is_swappable == 1){
                   $status = 'Yes'; 
                }else if($row->is_swappable == 0){
                    $status = 'No';
                }else{
                     $status = 'N/A';
                }
                return $status; 
            })
            ->addColumn('action', function ($query) {
                return $query->actionBtn('asset-master-vehicle');
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id')
            ->addIndexColumn();
    }


    public function query(AssetMasterVehicle $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('asset-master-vehicle')
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

        Column::make('Reg_No')
            ->title('Registration No')
            ->defaultContent('N/A'),

        Column::make('Model')
            ->title('Model')
            ->defaultContent('N/A'),

        Column::make('Manufacturer')
            ->title('Manufacturer')
            ->defaultContent('N/A'),

        Column::make('Original_Motor_ID')
            ->title('Original Motor ID')
            ->defaultContent('N/A'),

        Column::make('Chassis_Serial_No')
            ->title('Chassis Serial No')
            ->defaultContent('N/A'),

        // Column::make('Purchase_order_ID')
        //     ->title('PO Number')
        //     ->defaultContent('N/A'),

        // Column::make('Warranty_Kilometers')
        //     ->title('Warranty Kilometers')
        //     ->defaultContent('N/A'),

        Column::make('Hub')
            ->title('Hub')
            ->defaultContent('N/A'),

        Column::make('Client')
            ->title('Client')
            ->defaultContent('N/A'),

        Column::make('Colour')
            ->title('Colour')
            ->defaultContent('N/A'),
        
        Column::make('asset_status')
            ->title('Asset Status')
            ->defaultContent('N/A'),
        
        Column::make('is_swappable')
            ->title('Is Swappable')
            ->defaultContent('N/A'),


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
        return 'AssetMasterVehicle_' . date('YmdHis');
    }

    private function statusBtn($AssetMasterVehicle)
    {
        $isChecked = $AssetMasterVehicle->Status ? 'checked' : '';

        $status = '<div class="form-check form-switch">';
        $status .= '<label class="toggle-switch" for="statusCheckbox_' . $AssetMasterVehicle->id . '">';
        $status .= '<input type="checkbox" onclick="status_change_alert(\'' . route('admin.Green-Drive-Ev.asset-master.asset_master_vehicle_change_status', [$AssetMasterVehicle->id, $AssetMasterVehicle->Status ? 0 : 1]) . '\', \'' . ($AssetMasterVehicle->Status ? 'Deactivate' : 'Activate') . ' this AssetMasterVehicle ?\', event)" class="form-check-input toggle-btn" id="statusCheckbox_' . $AssetMasterVehicle->id . '" ' . $isChecked . '>';
        $status .= '<span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>';
        $status .= '</label></div>';

        return $status;
    }
}
