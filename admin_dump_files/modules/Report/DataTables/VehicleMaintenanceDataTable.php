<?php

namespace Modules\Report\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\VehicleMaintenance\Entities\VehicleMaintenance;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class VehicleMaintenanceDataTable extends DataTable
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
                return $query->vehicle?->name ?? 'N/A';
            })
            ->filterColumn('vehicle_name', function ($query, $keyword) {
                $query->whereHas('vehicle', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->orderColumn('vehicle_name', function ($query, $order) {
                $query->orderBy('vehicle_id', $order);
            })

            ->addColumn('employee_name', function ($query) {
                return $query->employee?->name ?? 'N/A';
            })
            ->filterColumn('employee_name', function ($query, $keyword) {
                $query->whereHas('employee', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->orderColumn('employee_name', function ($query, $order) {
                $query->orderBy('employee_id', $order);
            })

            ->editColumn('maintenance_type_id', function ($query) {
                return $query->maintenanceType?->name ?? 'N/A';
            })
            ->filterColumn('maintenance_type_id', function ($query, $keyword) {
                $query->whereHas('maintenanceType', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->orderColumn('maintenance_type_id', function ($query, $order) {
                $query->orderBy('maintenance_type_id', $order);
            })

            ->addColumn('mobile', function ($query) {
                return $query->employee?->phone ?? 'N/A';
            })
            ->filterColumn('mobile', function ($query, $keyword) {
                $query->whereHas('employee', function ($query) use ($keyword) {
                    $query->where('phone', 'like', '%'.$keyword.'%');
                });
            })
            ->orderColumn('mobile', function ($query, $order) {
                $query->orderBy('employee_id', $order);
            })

            ->addColumn('department', function ($query) {
                return $query->employee?->department?->name ?? 'N/A';
            })
            ->filterColumn('department', function ($query, $keyword) {
                $query->whereHas('employee.department', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })

            ->addColumn('designation', function ($query) {
                return $query->employee?->position?->name ?? 'N/A';
            })
            ->filterColumn('designation', function ($query, $keyword) {
                $query->whereHas('employee.position', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->editColumn('total', function ($query) {
                return $query->total;
            })->filterColumn('total', function ($query, $keyword) {
                $query->where('total', 'like', '%'.$keyword.'%');
            })->orderColumn('total', function ($query, $order) {
                $query->orderBy('total', $order);
            })

            ->editColumn('status', function ($query) {
                return $query->status;
            })
            ->setRowId('id')
            ->addIndexColumn()
            ->rawColumns(['status']);
    }

    /**
     * Get query source of dataTable.
     */
    public function query(VehicleMaintenance $model): QueryBuilder
    {

        $type = $this->request()->get('maintenance_type_id');
        $vehicle = $this->request()->get('vehicle_id');
        $status = $this->request()->get('status');
        $from = $this->request()->get('date_from');
        $to = $this->request()->get('date_to');

        $query = $model->newQuery()->with(['vehicle:id,name', 'employee']);

        $query->when($type, function ($query) use ($type) {
            return $query->where('maintenance_type_id', $type);
        });

        $query->when($vehicle, function ($query) use ($vehicle) {
            return $query->where('vehicle_id', $vehicle);
        });

        $query->when($status, function ($query) use ($status) {
            return $query->where('status', $status);
        });

        $query->when($from, function ($query) use ($from) {
            return $query->whereDate('date', '>=', $from);
        });

        $query->when($to, function ($query) use ($to) {
            return $query->whereDate('date', '<=', $to);
        });

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('vehicle-maintenance-table')
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
            Column::make('code')->title(localize('code'))->defaultContent('N/A'),
            Column::make('vehicle_name')->title(localize('vehicle'))->defaultContent('N/A'),
            Column::make('date')->title(localize('Requisition date'))->defaultContent('N/A'),
            Column::make('maintenance_type_id')->title(localize('Maintenance Type'))->defaultContent('N/A'),
            Column::make('employee_name')->title(localize('request by'))->defaultContent('N/A'),
            Column::make('mobile')->title(localize('Mobile'))->defaultContent('N/A'),
            Column::make('department')->title(localize('Department'))->orderable(false)->defaultContent('N/A'),
            Column::make('designation')->title(localize('Designation'))->orderable(false)->defaultContent('N/A'),
            Column::make('total')->title(localize('total'))->defaultContent('N/A'),
            Column::make('created_at')->title(localize('request date'))->defaultContent('N/A'),
            Column::make('status')->title(localize('status'))->defaultContent('N/A'),
        ];
    }

    /**
     * Get filename for export.
     */
    protected function filename(): string
    {
        return 'vehicle-maintenance-'.date('YmdHis');
    }
}
