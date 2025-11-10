<?php

namespace Modules\Employee\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\Employee\Entities\Employee;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EmployeeDataTable extends DataTable
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

            ->editColumn('department', function ($query) {
                return $query->department?->name;
            })->filterColumn('department', function ($query, $keyword) {
                $query->whereHas('department', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })->orderColumn('department', function ($query, $order) {
                $query->orderBy('department_id', $order);
            })

            ->editColumn('position', function ($query) {
                return $query->position?->name;
            })->filterColumn('position', function ($query, $keyword) {
                $query->whereHas('position', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })->orderColumn('position', function ($query, $order) {
                $query->orderBy('position_id', $order);
            })

            ->rawColumns(['action'])
            ->setRowId('id')
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     */
    public function query(Employee $model): QueryBuilder
    {

        $employee_type = $this->request()->get('employee_type');
        $department_id = $this->request()->get('department');
        $position_id = $this->request()->get('designation');
        $blood_group = $this->request()->get('blood_group');
        $join_date_from = $this->request()->get('join_date_from');
        $join_date_to = $this->request()->get('join_date_to');

        $query = $model->newQuery();

        $query->when($employee_type, function ($query) use ($employee_type) {
            return $query->where('payroll_type', 'like', '%'.$employee_type.'%');
        });

        $query->when($department_id, function ($query) use ($department_id) {
            return $query->where('department_id', $department_id);
        });

        $query->when($position_id, function ($query) use ($position_id) {
            return $query->where('position_id', $position_id);
        });

        $query->when($blood_group, function ($query) use ($blood_group) {
            return $query->where('blood_group', $blood_group);
        });

        $query->when($join_date_from, function ($query) use ($join_date_from) {
            return $query->where('join_date', '>=', $join_date_from);
        });
        $query->when($join_date_to, function ($query) use ($join_date_to) {
            return $query->where('join_date', '<=', $join_date_to);
        });

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('employee-table')
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
            Column::make('nid')->title(localize('NID'))->defaultContent('N/A'),
            Column::make('payroll_type')->title(localize('Type'))->defaultContent('N/A'),
            Column::make('department')->title(localize('Department'))->defaultContent('N/A'),
            Column::make('position')->title(localize('Designation'))->defaultContent('N/A'),
            Column::make('phone')->title(localize('Phone'))->defaultContent('N/A'),
            Column::make('blood_group')->title(localize('Blood Group'))->defaultContent('N/A'),
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
        return 'Employee_'.date('YmdHis');
    }
}
