<?php

namespace Modules\Report\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\VehicleRefueling\Entities\FuelRequisition;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FuelRequisitionDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))

            ->addColumn('vehicle_name', function ($query) {
                return $query->vehicle_id ? $query->vehicle->name : 'N/A';
            })
            ->filterColumn('vehicle_name', function ($query, $keyword) {
                $query->whereHas('vehicle', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->orderColumn('vehicle_name', function ($query, $order) {
                $query->orderBy('vehicle_id', $order);
            })
            ->addColumn('station_name', function ($query) {
                return $query->station_id ? $query->station->name : 'N/A';
            })
            ->filterColumn('station_name', function ($query, $keyword) {
                $query->whereHas('station', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->orderColumn('station_name', function ($query, $order) {
                $query->orderBy('station_id', $order);
            })
            ->addColumn('type_name', function ($query) {
                return $query->type_id ? $query->type->name : 'N/A';
            })
            ->filterColumn('type_name', function ($query, $keyword) {
                $query->whereHas('type', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->orderColumn('type_name', function ($query, $order) {
                $query->orderBy('type_id', $order);
            })
            ->setRowId('id')
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     */
    public function query(FuelRequisition $model): QueryBuilder
    {
        $vehicle = $this->request()->get('vehicle_id');
        $fuel_type = $this->request()->get('fuel_type_id');
        $date_from = $this->request()->get('date_from');
        $date_to = $this->request()->get('date_to');

        $query = $model->newQuery();

        $query->when($vehicle, function ($q, $vehicle) {
            return $q->where('vehicle_id', $vehicle);
        });

        $query->when($fuel_type, function ($q, $fuel_type) {
            return $q->where('type_id', $fuel_type);
        });

        $query->when($date_from, function ($q, $date_from) {
            return $q->whereDate('date', '>=', $date_from);
        });

        $query->when($date_to, function ($q, $date_to) {
            return $q->whereDate('date', '<=', $date_to);
        });

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('fuel-requisition-table')
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
            Column::make('code')->title(localize('Code'))->defaultContent('N/A'),
            Column::make('vehicle_name')->title(localize('Vehicle'))->defaultContent('N/A'),
            Column::make('station_name')->title(localize('Station'))->defaultContent('N/A'),
            Column::make('type_name')->title(localize('Fuel Type'))->defaultContent('N/A'),
            Column::make('current_qty')->title(localize('Current Odometer'))->defaultContent('0.00'),
            Column::make('qty')->title(localize('Quantity'))->defaultContent('0.00'),
            Column::make('date')->title(localize('Date'))->defaultContent('0.00'),
            Column::make('updated_at')->title(localize('Updated'))->defaultContent('N/A'),
        ];
    }

    /**
     * Get filename for export.
     */
    protected function filename(): string
    {
        return 'fuel-requisition-'.date('YmdHis');
    }
}
