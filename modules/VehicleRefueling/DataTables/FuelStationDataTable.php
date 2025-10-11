<?php

namespace Modules\VehicleRefueling\DataTables;

use App\Traits\RemovePrefixFromSearch;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\VehicleRefueling\Entities\FuelStation;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FuelStationDataTable extends DataTable
{
    use RemovePrefixFromSearch;

    public function __construct()
    {
        $this->removePrefix(setting('fuel.station_code_prefix'));
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
            ->editColumn('is_active', function ($query) {
                return $query->is_active == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('vendor_name', function ($query) {
                return $query->vendor_id ? $query->vendor->name : 'N/A';
            })
            ->setRowId('id')
            ->addIndexColumn()
            ->rawColumns(['is_active', 'action']);
    }

    /**
     * Get query source of dataTable.
     */
    public function query(FuelStation $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('fuel-station-table')
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
            Column::make('name')->title(localize('Station name'))->defaultContent('N/A'),
            Column::make('vendor_name')->title(localize('Vendor name'))->defaultContent('N/A'),
            Column::make('address')->title(localize('address'))->defaultContent('N/A'),
            Column::make('contact_person')->title(localize('contact_person'))->defaultContent('N/A'),
            Column::make('contact_number')->title(localize('contact_number'))->defaultContent('N/A'),
            Column::make('is_active')->title(localize('status')),
            Column::make('updated_at')->title(localize('Updated'))->defaultContent('N/A'),
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
        return 'fuel-station-'.date('YmdHis');
    }
}
