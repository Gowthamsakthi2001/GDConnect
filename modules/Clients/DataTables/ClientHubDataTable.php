<?php

namespace Modules\Clients\DataTables;

use Modules\Clients\Entities\Client;
use Modules\Clients\Entities\ClientHub;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Modules\Zones\Entities\Zones;


class ClientHubDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('status', function ($query) {
                return $this->statusBtn($query);
            })
            ->addColumn('hub_name', function ($row) {
                return $row->hub_name ?? 'N/A'; 
            })
           ->addColumn('client_name', function ($row) {
                return $row->client->client_name ?? 'N/A'; 
            })
             ->addColumn('created_at', function ($row) {
                return date('d-m-Y h:i:s A',strtotime($row->created_at)) ?? 'N/A'; 
            })
            ->addColumn('action', function ($query) {
                return $query->actionBtn('client-hub-table');
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id')
            ->addIndexColumn();
    }

    public function query(ClientHub $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id', 'desc');
    }

   public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('client-hub-table')
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
            Column::make('hub_name')
                ->title('Hub Name')
                ->defaultContent('N/A'),
            Column::make('client_name')
                ->title('Client')
                ->defaultContent('N/A'),
            Column::make('created_at')
            ->title('Created At')
            ->defaultContent('N/A'),
            Column::make('status')
                ->title('Status')
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
        return 'ClientHub_' . date('YmdHis');
    }

    private function statusBtn($client): string
    {
        $isChecked = $client->status ? 'checked' : '';
    
        $status = '<div class="form-check form-switch">';
        $status .= '<label class="toggle-switch" for="statusCheckbox_' . $client->id . '">';
        $status .= '<input type="checkbox" onclick="status_change_alert(\'' . route('admin.Green-Drive-Ev.clients.hub.status', [$client->id, $client->status ? 0 : 1]) . '\', \'' . ($client->status ? 'Deactivate' : 'Activate') . ' this Client Hub?\', event)" class="form-check-input toggle-btn" id="statusCheckbox_' . $client->id . '" ' . $isChecked . '>';
        $status .= '<span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>';
        $status .= '</label>';
        $status .= '</div>';
    
        return $status;
    }
    
}
