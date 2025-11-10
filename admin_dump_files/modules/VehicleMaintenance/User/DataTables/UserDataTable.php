<?php

namespace Modules\User\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('role', function ($query) {
                return implode(', ', $query->getRoleNames()->toArray());
            })
            ->editColumn('status', function ($query) {
                return $this->statusBtn($query);
            })
            ->addColumn('action', function ($query) {
                return $query->actionBtn('user-table');
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id')
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id', 'desc');
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>rt<'bottom'<'row'<'col-md-6'i><'col-md-6'p>>><'clear'>")
            ->orderBy(5)
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
            Column::make('name')->title(localize('Name'))->defaultContent('N/A'),
            Column::make('email')->title(localize('Email'))->defaultContent('N/A'),
            Column::make('role')->title(localize('Role'))->defaultContent('N/A')->orderable(false)->searchable(false),
            Column::computed('status')->title(localize('Status'))->orderable(false)->searchable(false),
            Column::make('updated_at')->title(localize('Updated')),
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
        return 'User_'.date('YmdHis');
    }

    /**
     * Status Button
     *
     * @param  User  $user
     */
    private function statusBtn($user): string
    {
        $status = '<select class="form-control" name="status" id="status_id_'.$user->id.'" ';
        $status .= 'onchange="userStatusUpdate(\''.route(config('theme.rprefix').'.status-update', $user->id).'\','.$user->id.',\''.$user->status.'\')">';

        foreach (User::statusList() as $key => $value) {
            $status .= "<option value='$key' ".selected($key, $user->status).">$value</option>";
        }

        $status .= '</select>';

        return $status;
    }
}
