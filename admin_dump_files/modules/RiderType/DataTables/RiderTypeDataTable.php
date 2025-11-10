<?php
namespace Modules\RiderType\DataTables;

use Modules\RiderType\Entities\RiderType;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RiderTypeDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('status', function ($query) {
                return $this->statusBtn($query);
            })
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
            })
            ->editColumn('updated_at', function ($row) {
                return \Carbon\Carbon::parse($row->updated_at)->format('Y-m-d');
            })
            ->addColumn('action', function ($query) {
                return $query->actionBtn('rider-type-table');
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id')
            ->addIndexColumn();
    }

    public function query(RiderType $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('rider-type-table')
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

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('SL')->searchable(false)->orderable(false)->width(30)->addClass('text-center'),
            Column::make('type')->title('Type')->defaultContent('N/A'),
            Column::make('status')->title('Status')->orderable(false)->searchable(false),
            Column::make('created_at')->title('Created At'),
            Column::make('updated_at')->title('Updated At'),
            Column::computed('action')->title('Action')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->width(80)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'RiderType_' . date('YmdHis');
    }

    private function statusBtn($rider_type): string
    {
        $isChecked = $rider_type->status ? 'checked' : '';

        $status = '<div class="form-check form-switch">';
        $status .= '<label class="toggle-switch" for="statusCheckbox_' . $rider_type->id . '">';
        $status .= '<input type="checkbox" onclick="status_change_alert(\'' . route('admin.Green-Drive-Ev.rider-type.status', [$rider_type->id, $rider_type->status ? 0 : 1]) . '\', \'' . ($rider_type->status ? 'Deactivate' : 'Activate') . ' this Rider Type?\', event)" class="form-check-input toggle-btn" id="statusCheckbox_' . $rider_type->id . '" ' . $isChecked . '>';
        $status .= '<span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>';
        $status .= '</label></div>';

        return $status;
    }
}