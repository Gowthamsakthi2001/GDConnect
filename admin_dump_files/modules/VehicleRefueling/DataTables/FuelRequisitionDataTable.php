<?php

namespace Modules\VehicleRefueling\DataTables;

use App\Traits\RemovePrefixFromSearch;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\VehicleRefueling\Entities\FuelRequisition;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FuelRequisitionDataTable extends DataTable
{
    use RemovePrefixFromSearch;

    public function __construct()
    {
        $this->removePrefix(setting('fuel.requisition_code_prefix'));
    }

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
                return $query->vehicle?->name ?? 'N/A';
            })->filterColumn('vehicle_name', function ($query, $keyword) {
                $query->whereHas('vehicle', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->addColumn('station_name', function ($query) {
                return $query->station?->name ?? 'N/A';
            })->filterColumn('station_name', function ($query, $keyword) {
                $query->whereHas('station', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->addColumn('type_name', function ($query) {
                return $query->type?->name ?? 'N/A';
            })->filterColumn('type_name', function ($query, $keyword) {
                $query->whereHas('type', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->editColumn('status', function ($query) {
                return $this->statusBtn($query);
            })
            ->setRowId('id')
            ->addIndexColumn()
            ->rawColumns(['action', 'status']);
    }

    /**
     * Get query source of dataTable.
     */
    public function query(FuelRequisition $model): QueryBuilder
    {
        return $model->newQuery();
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
            ->orderBy(1)
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
            Column::make('code')->title(localize('Code'))->defaultContent('N/A'),
            Column::make('vehicle_name')->title(localize('Vehicle'))->defaultContent('N/A'),
            Column::make('station_name')->title(localize('Station'))->defaultContent('N/A'),
            Column::make('type_name')->title(localize('Fuel Type'))->defaultContent('N/A'),
            Column::make('current_qty')->title(localize('Current Odometer'))->defaultContent('0.00'),
            Column::make('qty')->title(localize('Quantity'))->defaultContent('0.00'),
            Column::make('date')->title(localize('Date'))->defaultContent('0.00'),
            Column::make('updated_at')->title(localize('Updated'))->defaultContent('N/A'),
            Column::make('status')->title(localize('status')),
            Column::computed('action')
                ->title(localize('Action'))
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
        return 'fuel-requisition-'.date('YmdHis');
    }

    private function statusBtn($i): string
    {
        $status = '<select class="form-control" name="status" id="status_id_'.$i->id.'" ';
        $status .= 'onchange="userStatusUpdate(\''.route(config('theme.rprefix').'.status-update', $i->id).'\','.$i->id.',\''.$i->status.'\')">';

        foreach (FuelRequisition::getStatues() as $key => $value) {
            $selected = $i->status == $key ? 'selected' : '';
            $status .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
        }

        $status .= '</select>';

        return $status;
    }
}
