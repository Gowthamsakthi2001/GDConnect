<?php

namespace Modules\VehicleManagement\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\VehicleManagement\Entities\Insurance;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InsuranceDataTable extends DataTable
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
            ->editColumn('company_id', function ($query) {
                return $query->company?->name ?? 'N/A';
            })->filterColumn('company_id', function ($query, $keyword) {
                $query->whereHas('company', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->editColumn('vehicle_id', function ($query) {
                return $query->vehicle?->name ?? 'N/A';
            })->filterColumn('vehicle_id', function ($query, $keyword) {
                $query->whereHas('vehicle', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->editColumn('status', function ($query) {
                return $query->status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            })
            ->editColumn('recurring_period_id', function ($query) {
                return $query->recurring_period?->name ?? 'N/A';
            })->filterColumn('recurring_period_id', function ($query, $keyword) {
                $query->whereHas('recurring_period', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->setRowId('id')
            ->addIndexColumn()
            ->rawColumns(['status', 'action']);
    }

    /**
     * Get query source of dataTable.
     */
    public function query(Insurance $model): QueryBuilder
    {
        $company = $this->request()->get('company_id');
        $vehicle = $this->request()->get('vehicle_id');
        $policy_number = $this->request()->get('policy_number');
        $date_from = $this->request()->get('date_from');
        $date_to = $this->request()->get('date_to');

        $query = $model->newQuery()
            ->when($company, function ($query) use ($company) {
                $query->where('company_id', $company);
            })
            ->when($vehicle, function ($query) use ($vehicle) {
                $query->where('vehicle_id', $vehicle);
            })
            ->when($policy_number, function ($query) use ($policy_number) {
                $query->where('policy_number', $policy_number);
            })
            ->when($date_from, function ($query) use ($date_from) {
                $query->where('start_date', '>=', $date_from);
            })
            ->when($date_to, function ($query) use ($date_to) {
                $query->where('end_date', '<=', $date_to);
            });

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('insurance-table')
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
            Column::make('company_id')->title(localize('Policy Vendor Name'))->defaultContent('N/A'),
            Column::make('vehicle_id')->title(localize('Vehicle Name'))->defaultContent('N/A'),
            Column::make('policy_number')->title(localize('Policy Number'))->defaultContent('N/A'),
            Column::make('start_date')->title(localize('Start Date'))->defaultContent('N/A'),
            Column::make('end_date')->title(localize('End Date'))->defaultContent('N/A'),
            Column::make('recurring_period_id')->title(localize('Recurring_Period'))->defaultContent('N/A'),
            Column::make('recurring_date')->title(localize('Recurring Date'))->defaultContent('N/A'),
            Column::make('status')->title(localize('status'))->orderable(false),
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
        return 'insurance-'.date('YmdHis');
    }
}
