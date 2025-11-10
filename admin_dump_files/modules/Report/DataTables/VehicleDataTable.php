<?php

namespace Modules\Report\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\VehicleManagement\Entities\Vehicle;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class VehicleDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))

            ->editColumn('vehicle_type_id', function ($query) {
                return $query->vehicle_type?->name ?? 'N/A';
            })
            ->filterColumn('vehicle_type_id', function ($query, $keyword) {
                $query->whereHas('vehicle_type', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->editColumn('department_id', function ($query) {
                return $query->department?->name ?? 'N/A';
            })->filterColumn('department_id', function ($query, $keyword) {
                $query->whereHas('department', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })->orderColumn('department_id', function ($query, $order) {
                $query->orderBy('department_id', $order);
            })
            ->editColumn('vendor_id', function ($query) {
                return $query->vendor?->name ?? 'N/A';
            })->filterColumn('vendor_id', function ($query, $keyword) {
                $query->whereHas('vendor', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })->orderColumn('vendor_id', function ($query, $order) {
                $query->orderBy('vendor_id', $order);
            })
            ->editColumn('ownership_id', function ($query) {
                return $query->ownership?->name ?? 'N/A';
            })->filterColumn('ownership_id', function ($query, $keyword) {
                $query->whereHas('ownership', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })->orderColumn('ownership_id', function ($query, $order) {
                $query->orderBy('ownership_id', $order);
            })
            ->setRowId('id')
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     */
    public function query(Vehicle $model): QueryBuilder
    {

        $department = $this->request()->get('department_id');
        $vehicle_type = $this->request()->get('vehicle_type_id');
        $ownership = $this->request()->get('ownership_id');
        $vendor = $this->request()->get('vendor_id');
        $date_from = $this->request()->get('date_from');
        $date_to = $this->request()->get('date_to');

        $query = $model->newQuery()
            ->when($department, function ($query) use ($department) {
                $query->where('department_id', $department);
            })
            ->when($vehicle_type, function ($query) use ($vehicle_type) {
                $query->where('vehicle_type_id', $vehicle_type);
            })
            ->when($ownership, function ($query) use ($ownership) {
                $query->where('ownership_id', $ownership);
            })
            ->when($vendor, function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor);
            })
            ->when($date_from, function ($query) use ($date_from) {
                $query->whereDate('requisition_date', '>=', $date_from);
            })
            ->when($date_to, function ($query) use ($date_to) {
                $query->whereDate('requisition_date', '<=', $date_to);
            });

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('vehicle-table')
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
            Column::make('name')->title(localize('Name'))->defaultContent('N/A'),
            Column::make('vehicle_type_id')->title(localize('Vehicle Type'))->defaultContent('N/A'),
            Column::make('department_id')->title(localize('Department'))->defaultContent('N/A'),
            Column::make('registration_date')->title(localize('Registration Date'))->defaultContent('N/A'),
            Column::make('ownership_id')->title(localize('Ownership'))->defaultContent('N/A'),
            Column::make('vendor_id')->title(localize('Vendor')),
        ];
    }

    /**
     * Get filename for export.
     */
    protected function filename(): string
    {
        return 'vehicle-'.date('YmdHis');
    }
}
