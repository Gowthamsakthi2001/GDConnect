<?php

namespace Modules\Report\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\VehicleManagement\Entities\PickupAndDrop;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PickAndDropDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))

            ->editColumn('route_id', function ($query) {
                return $query->route?->route_name ?? 'N/A';
            })->filterColumn('route_id', function ($query, $keyword) {
                $query->whereHas('route', function ($query) use ($keyword) {
                    $query->where('route_name', 'like', '%'.$keyword.'%');
                });
            })->orderColumn('route_id', function ($query, $order) {
                $query->orderBy('route_id', $order);
            })
            ->editColumn('employee_id', function ($query) {
                return $query->employee?->name ?? 'N/A';
            })
            ->filterColumn('employee_id', function ($query, $keyword) {
                $query->whereHas('employee', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->editColumn('status', function ($query) {
                return $query->status == 1 ? localize('Release') : localize('Pending');
            })
            ->editColumn('request_type', function ($query) {
                return $query->request_type == 0 ? localize('Regular') : localize('Specific Day');
            })
            ->editColumn('type', function ($query) {

                switch ($query->type) {
                    case 'Pickup':
                        return localize('Pick Up');
                        break;
                    case 'Drop':
                        return localize('Drop Off');
                        break;
                    case 'PickDrop':
                        return localize('Pick And Drop');
                        break;
                    default:
                        return localize('N/A');
                }
            })

            ->setRowId('id')
            ->addIndexColumn()
            ->rawColumns(['status']);
    }

    /**
     * Get query source of dataTable.
     */
    public function query(PickupAndDrop $model): QueryBuilder
    {

        $route = $this->request()->get('route_id');
        $type = $this->request()->get('type');
        $request_type = $this->request()->get('request_type');
        $status = $this->request()->get('status');
        $date = $this->request()->get('date');

        $query = $model->newQuery()
            ->when($route, function ($query) use ($route) {
                $query->where('route_id', $route);
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', 'LIKE', '%'.$type.'%');
            })
            ->when($request_type != null, function ($query) use ($request_type) {
                $query->where('request_type', $request_type);
            })
            ->when($status != null, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('effective_date', '<=', $date);
            });

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('pick-drop-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>rt<'bottom'<'row'<'col-md-6'i><'col-md-6'p>>><'clear'>")
            ->parameters([
                'order' => [],
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

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title(localize('SL'))->searchable(false)->orderable(false)->width(30)->addClass('text-center'),
            Column::make('route_id')->title(localize('Route'))->defaultContent('N/A'),
            Column::make('effective_date')->title(localize('Requisition Date'))->defaultContent('N/A'),
            Column::make('type')->title(localize('Requisition Type'))->defaultContent('N/A'),
            Column::make('employee_id')->title(localize('Requested By'))->defaultContent('N/A'),
            Column::make('request_type')->title(localize('Request Type'))
                ->defaultContent('N/A'),
            Column::make('status')->title(localize('Status'))->defaultContent('N/A'),
        ];
    }

    /**
     * Get filename for export.
     */
    protected function filename(): string
    {
        return 'pick-drop-'.date('YmdHis');
    }
}
