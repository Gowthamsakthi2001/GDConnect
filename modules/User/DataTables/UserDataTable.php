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
                return $this->actionBtn('user-table',$query);
            })
             ->addColumn('city', function ($query) {
                return $query->city->city_name ?? 'N/A';
            })
            ->addColumn('loginType', function ($query) { //updated by Gowtham.s
                return $query->login_type == 1 ? 'Master' : 'Zone';
            })
            ->addColumn('zone', function ($query) { //updated by Gowtham.s
                return $query->getZone->name ?? 'N/A';
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
            Column::make('emp_id')->title('Staff ID')->defaultContent('-'),
            Column::make('name')->title(localize('Name'))->defaultContent('N/A'),
            Column::make('email')->title(localize('Email'))->defaultContent('N/A'),
            Column::make('loginType')->title('Login Type'), //updated by Gowtham.s 
            Column::make('city')->title(localize('City')),
            Column::make('zone')->title('Zone'), //updated by Gowtham.s 
            Column::make('role')->title(localize('Role'))->defaultContent('N/A')->orderable(false)->searchable(false),
            Column::computed('status')->title(localize('Status'))->orderable(false)->searchable(false),
            // Column::make('updated_at')->title(localize('Updated')),
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
    
    private function actionBtn($table = null,$user)
    {
        if ($user->delete_status == 1) {
            $icon = '<i class="fas fa-undo"></i>';
            $btnClass = 'btn-dark-soft btn-outline-dark';
            $btnText = 'Restore';
        } else {
           
            
            $icon = '<i class="fas fa-trash"></i>';
            $btnClass = 'btn-danger-soft';
            $btnText = 'Delete';
        }
        
        // return '<a href="'.route(config('theme.rprefix').'.edit', $user->id).'" class="btn btn-success-soft btn-sm me-1" title="Edit"><i class="fa fa-edit"></i></a>'.
        //     '<a href="#" class="btn btn-danger-soft btn-sm" onclick="delete_modal(\''.route(config('theme.rprefix').'.destroy', $user->id).'\',\''.$table.'\')"  title="Delete"><i class="fa fa-trash"></i></a>';
        
        return '<a href="'.route(config('theme.rprefix').'.edit', $user->id).'" class="btn btn-success-soft btn-sm me-1" title="Edit"><i class="fa fa-edit"></i></a>'.
            '<button onclick="route_alert(\'' . route(config('theme.rprefix').'.destroy', $user->id) . '\', \'' . $btnText . ' this Staff\')" class="btn ' . $btnClass . ' btn-sm me-1" title="' . $btnText . '">' . $icon . '
                    </button>';
    }
}
