<?php

namespace Modules\AssetMaster\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\AssetMaster\Entities\AssetStatus;
use Carbon\Carbon;
use Modules\Deliveryman\Entities\Deliveryman;
class AssetStatusDataTable extends DataTable
{
  public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
             ->filterColumn('status_name', function($query, $keyword) {
                $query->where('ev_asset_status.status_name', 'LIKE', "%{$keyword}%");
            })
            ->editColumn('status_name', function ($row) {
                return $row->status_name ?? 'N/A';
            })
            ->addColumn('status', function ($row) {
                return $this->statusBtn($row);
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at ? date('d-m-Y h:i:s A',strtotime($row->created_at)) : '';
            })
            ->addColumn('action', function ($row) {
                return $this->actionBtn('asset-status-table', $row);
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id');
    }

    public function query(AssetStatus $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('asset-status-table')
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
            Column::make('DT_RowIndex')
                ->title('SL')
                ->searchable(false)
                ->orderable(false)
                ->width(30)
                ->addClass('text-center'),
            Column::make('status_name')
                ->title('Name')
                ->defaultContent('N/A'),
            Column::make('status')
                ->title('Status')
                ->defaultContent('N/A'),
            Column::make('created_at')
            ->title('Created At')
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

    private function statusBtn($row)
    {
        $isChecked = $row->status ? 'checked' : '';

        $status = '<div class="form-check form-switch">';
        $status .= '<label class="toggle-switch" for="statusCheckbox_' . $row->id . '">';
        $status .= '<input type="checkbox" onclick="status_change_alert(\'' . route('admin.Green-Drive-Ev.asset-master.asset_update_status', [$row->id, $row->status ? 0 : 1]) . '\', \'' . ($row->status ? 'Deactivate' : 'Activate') . ' this Asset Status ?\', event)" class="form-check-input toggle-btn" id="statusCheckbox_' . $row->id . '" ' . $isChecked . '>';
        $status .= '<span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>';
        $status .= '</label></div>';

        return $status;
    }
    
     public function actionBtn($tableId = 'asset-status-table',$row)
    {
        return '
        <div class="d-flex align-items-center gap-1">
            <a href="javascript:void(0);" onclick="AddorEditStatusModal('.$row->id.')" class="btn btn-success-soft btn-sm me-1">
                <svg class="svg-inline--fa fa-pen-to-square" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="pen-to-square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                    <path fill="currentColor" d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.8 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"></path>
                </svg>
            </a>
            <button onclick="route_alert(\'' . route('admin.Green-Drive-Ev.asset-master.asset_status_delete', $row->id) . '\', \'Delete this Status\')" class="btn btn-danger-soft btn-sm">
                <svg class="svg-inline--fa fa-trash" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="trash" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="">
                    <path fill="currentColor" d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"></path>
                </svg>
            </button>
        </div>
        ';
        
    }
}
