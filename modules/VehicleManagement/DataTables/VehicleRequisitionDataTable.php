<?php

namespace Modules\VehicleManagement\DataTables;

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
            ->addColumn('action', function ($query) {
                $button = '<div class="align-items-center">';
                $button .= '<a href="javascript:void(0);" class="btn btn-success-soft btn-sm m-1" title="Edit" onclick="'."axiosModal('".route(\config('theme.rprefix').'.edit', $query->id).'\')"><i class="fa fa-edit"></i></a>';
                $button .= '<a href="javascript:void(0);" class="btn btn-danger-soft btn-sm mx-1" onclick="delete_modal(\''.route(\config('theme.rprefix').'.destroy', $query->id).'\')"  title="Delete"><i class="fa fa-trash"></i></a>';

                $button .= '</div>';

                return $button;
            })
            ->editColumn('status', function ($query) {
                return $this->statusBtn($query);
            })
            ->editColumn('employee_id', function ($query) {
                return $query->employee?->name ?? 'N/A';
            })->filterColumn('employee_id', function ($query, $keyword) {
                $query->whereHas('employee', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->editColumn('driver_id', function ($query) {
                return $query->driver?->name ?? 'N/A';
            })->filterColumn('driver_id', function ($query, $keyword) {
                $query->whereHas('driver', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->addColumn('requested_by', function ($query) {
                return $query->driver?->name ?? 'N/A';
            })
            ->setRowId('id')
            ->addIndexColumn()
            ->rawColumns(['status', 'action']);
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
            })->latest();

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
            Column::make('requisition_date')->title(localize('Requisition Date'))->defaultContent('N/A'),
            Column::make('driver_id')->title(localize('Driver Name')),
            Column::make('requested_by')->title(localize('Requested By'))->defaultContent('N/A'),
            Column::make('status')->title(localize('Status'))->defaultContent('N/A'),
            Column::computed('action')
                ->title(localize('Action'))
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->width(150)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     */
    protected function filename(): string
    {
        return 'vehicle-requisition-'.date('YmdHis');
    }

    /**
     * status button
     *
     * @param  mixed  $i
     */
    private function statusBtn($i): string
    {
        $status = '<select class="form-control" name="status" id="status_id_'.$i->id.'" ';
        $status .= 'onchange="userStatusUpdate(\''.route(config('theme.rprefix').'.status-update', $i->id).'\','.$i->id.',\''.$i->status.'\')">';

        foreach ($i->getStatues() as $key => $value) {
            $selected = $i->status == $key ? 'selected' : '';
            $status .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
        }

        $status .= '</select>';

        return $status;
    }
}
