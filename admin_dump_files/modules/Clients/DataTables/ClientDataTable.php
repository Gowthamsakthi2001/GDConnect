<?php

namespace Modules\Clients\DataTables;

use Modules\Clients\Entities\Client;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Modules\Zones\Entities\Zones;


class ClientDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('status', function ($query) {
                return $this->statusBtn($query);
            })
            ->addColumn('client_zone', function ($row) {
                return $row->zone->name ?? 'N/A'; // Display zone name or 'N/A' if null
            })
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
            })
            ->editColumn('updated_at', function ($row) {
                return \Carbon\Carbon::parse($row->updated_at)->format('Y-m-d');
            })
            ->addColumn('action', function ($query) {
                return $query->actionBtn('client-table');
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id')
            ->addIndexColumn();
    }

    public function query(Client $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('client-table')
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
            Column::make('client_name')->title('Client Name')->defaultContent('N/A'),
            Column::make('client_zone')->title('Client Zone')->defaultContent('N/A'),
            Column::make('client_location')->title('Client Location')->defaultContent('N/A'),
            Column::make('hub_name')->title('Hub Name')->defaultContent('N/A'),
            // Column::make('status')->title('Status')->orderable(false)->searchable(false),
            Column::make('created_at')->title('Created At'),
            Column::make('updated_at')->title('Updated At'),
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
        return 'Client_' . date('YmdHis');
    }

    private function statusBtn($client): string
    {
        $isChecked = $client->status ? 'checked' : '';
    
        $status = '<div class="form-check form-switch">';
        $status .= '<label class="toggle-switch" for="statusCheckbox_' . $client->id . '">';
        $status .= '<input type="checkbox" onclick="status_change_alert(\'' . route('admin.Green-Drive-Ev.City.status', [$client->id, $client->status ? 0 : 1]) . '\', \'' . ($client->status ? 'Deactivate' : 'Activate') . ' this Client?\', event)" class="form-check-input toggle-btn" id="statusCheckbox_' . $client->id . '" ' . $isChecked . '>';
        $status .= '<span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>';
        $status .= '</label>';
        $status .= '</div>';
    
        return $status;
    }
}
