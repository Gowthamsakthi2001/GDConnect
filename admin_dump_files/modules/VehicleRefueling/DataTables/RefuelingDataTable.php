<?php

namespace Modules\VehicleRefueling\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\VehicleRefueling\Entities\VehicleRefueling;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RefuelingDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))

            ->addColumn('action', function ($query) {
                $button = '<div class="align-items-center">';

                $button .= '<a href="javascript:void(0);" class="btn btn-success-soft btn-sm m-1" title="Edit" onclick="'."axiosModal('".route(\config('theme.rprefix').'.edit', $query->id).'\')"><i class="fa fa-edit"></i></a>';
                $button .= '<a href="javascript:void(0);" class="btn btn-danger-soft btn-sm mx-1" onclick="delete_modal(\''.route(\config('theme.rprefix').'.destroy', $query->id).'\')"  title="Delete"><i class="fa fa-trash"></i></a>';
                $button .= '</div>';

                return $button;
            })
            ->addColumn('vehicle_name', function ($query) {
                return $query->vehicle?->name;
            })->filterColumn('vehicle_name', function ($query, $keyword) {
                $query->whereHas('vehicle', function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('driver_name', function ($query) {
                return $query->driver?->name;
            })->filterColumn('driver_name', function ($query, $keyword) {
                $query->whereHas('driver', function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('fuel_type_name', function ($query) {
                return $query->fuel_type?->name;
            })->filterColumn('fuel_type_name', function ($query, $keyword) {
                $query->whereHas('fuel_type', function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                });
            })
            ->editColumn('strict_policy', function ($query) {
                return $query->strict_policy ? 'Yes' : 'No';
            })->filterColumn('strict_policy', function ($query, $keyword) {
                $query->where('strict_policy', $keyword == 'Yes' ? 1 : 0);
            })

            ->rawColumns(['action'])
            ->setRowId('id')
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     */
    public function query(VehicleRefueling $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['vehicle', 'driver']);

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('refueling-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>rt<'bottom'<'row'<'col-md-6'i><'col-md-6'p>>><'clear'>")
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

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title(localize('SL'))->searchable(false)->orderable(false)->width(30)->addClass('text-center'),
            Column::make('vehicle_name')->title(localize('Vehicle'))->defaultContent('N/A')->orderable(false),
            Column::make('fuel_type_name')->title(localize('Fuel Type'))->defaultContent('N/A')->orderable(false),
            Column::make('driver_name')->title(localize('Driver Name'))->defaultContent('N/A')->orderable(false),
            Column::make('last_reading')->title(localize('Last Reading'))->defaultContent('N/A'),
            Column::make('consumption_percent')->title(localize('Consumption'))->defaultContent('N/A'),
            Column::make('strict_policy')->title(localize('Strict Policy'))->defaultContent('N/A'),
            Column::make('refuel_limit')->title(localize('Refuel Limit'))->defaultContent('N/A'),
            Column::computed('action')->title(localize('Action'))
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->width(80)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     */
    protected function filename(): string
    {
        return 'Refueling_'.date('YmdHis');
    }
}
