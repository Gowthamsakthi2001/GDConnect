<?php

namespace Modules\Report\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\VehicleManagement\Entities\VehicleRequisition;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class VehicleRequisitionDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))

            ->editColumn('employee_id', function ($query) {
                return $query->employee?->name ?? 'N/A';
            })->filterColumn('employee_id', function ($query, $keyword) {
                $query->whereHas('employee', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })->orderColumn('employee_id', function ($query, $order) {
                $query->orderBy('employee_id', $order);
            })

            ->editColumn('driver_id', function ($query) {
                return $query->driver?->name ?? 'N/A';
            })
            ->filterColumn('driver_id', function ($query, $keyword) {
                $query->whereHas('driver', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->orderColumn('driver_id', function ($query, $order) {
                $query->orderBy('driver_id', $order);
            })
            ->addColumn('requested_by', function ($query) {
                return $query->driver?->name ?? 'N/A';
            })->filterColumn('requested_by', function ($query, $keyword) {
                $query->whereHas('driver', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })->orderColumn('requested_by', function ($query, $order) {
                $query->orderBy('driver_id', $order);
            })
            ->editColumn('status', function ($query) {
                return $query->status == 1 ? localize('Release') : localize('Pending');
            })
            ->addColumn('mobile_no', function ($query) {
                return $query->employee?->phone ?? 'N/A';
            })->filterColumn('mobile_no', function ($query, $keyword) {
                $query->whereHas('employee', function ($query) use ($keyword) {
                    $query->where('phone', 'like', '%'.$keyword.'%');
                });
            })->orderColumn('mobile_no', function ($query, $order) {
                $query->orderBy('employee_id', $order);
            })
            ->addColumn('driver_mobile', function ($query) {
                return $query->driver?->phone ?? 'N/A';
            })->filterColumn('driver_mobile', function ($query, $keyword) {
                $query->whereHas('driver', function ($query) use ($keyword) {
                    $query->where('phone', 'like', '%'.$keyword.'%');
                });
            })->orderColumn('driver_mobile', function ($query, $order) {
                $query->orderBy('driver_id', $order);
            })
            ->setRowId('id')
            ->addIndexColumn()
            ->rawColumns(['status']);
    }

    /**
     * Get query source of dataTable.
     */
    public function query(VehicleRequisition $model): QueryBuilder
    {

        $requisition_for = $this->request()->get('employee_id');
        $vehicle_type = $this->request()->get('vehicle_type_id');
        $date_from = $this->request()->get('date_from');
        $date_to = $this->request()->get('date_to');
        $status = $this->request()->get('status');
        $driver = $this->request()->get('driver_id');

        $query = $model->newQuery()
            ->when($requisition_for, function ($query) use ($requisition_for) {
                $query->where('employee_id', $requisition_for);
            })
            ->when($vehicle_type, function ($query) use ($vehicle_type) {
                $query->where('vehicle_type_id', $vehicle_type);
            })
            ->when($date_from, function ($query) use ($date_from) {
                $query->whereDate('requisition_date', '>=', $date_from);
            })
            ->when($date_to, function ($query) use ($date_to) {
                $query->whereDate('requisition_date', '<=', $date_to);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($driver, function ($query) use ($driver) {
                $query->where('driver_id', $driver);
            });

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('vehicle-requisition-table')
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
            Column::make('employee_id')->title(localize('Requisition For'))->defaultContent('N/A'),
            Column::make('mobile_no')->title(localize('User Mobile'))->defaultContent('N/A'),
            Column::make('requisition_date')->title(localize('Requisition Date'))->defaultContent('N/A'),
            Column::make('driver_id')->title(localize('Driver Name')),
            Column::make('driver_mobile')->title(localize('Driver Mobile'))->defaultContent('N/A'),
            Column::make('where_from')->title(localize('From'))->defaultContent('N/A'),
            Column::make('where_to')->title(localize('To'))->defaultContent('N/A'),
            Column::make('tolerance')->title(localize('Duration'))->defaultContent('N/A'),
            Column::make('number_of_passenger')->title(localize('Total Passenger'))->defaultContent('N/A'),
            Column::make('purpose')->title(localize('Purpose'))->defaultContent('N/A'),
            Column::make('requested_by')->title(localize('Requested By'))->defaultContent('N/A'),
            Column::make('status')->title(localize('Status'))->defaultContent('N/A'),
            // Column::make('details')->title(localize('Remarks')),
        ];
    }

    /**
     * Get filename for export.
     */
    protected function filename(): string
    {
        return 'vehicle-requisition-'.date('YmdHis');
    }
}
