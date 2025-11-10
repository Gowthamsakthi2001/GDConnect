<?php

namespace Modules\VehicleManagement\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\VehicleManagement\Entities\LegalDocumentation;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class LegalDocumentationDataTable extends DataTable
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
            ->editColumn('document_type_id', function ($query) {
                return $query->document_type?->name ?? 'N/A';
            })->filterColumn('document_type_id', function ($query, $keyword) {
                $query->whereHas('document_type', function ($query) use ($keyword) {
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
            ->editColumn('vendor_id', function ($query) {
                return $query->vendor?->name ?? 'N/A';
            })->filterColumn('vendor_id', function ($query, $keyword) {
                $query->whereHas('vendor', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->setRowId('id')
            ->addIndexColumn()
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     */
    public function query(LegalDocumentation $model): QueryBuilder
    {
        $document_type = $this->request()->get('document_type_id');
        $vehicle = $this->request()->get('vehicle_id');
        $date_from = $this->request()->get('date_from');
        $date_to = $this->request()->get('date_to');

        $query = $model->newQuery()
            ->when($document_type, function ($query) use ($document_type) {
                $query->where('document_type_id', $document_type);
            })
            ->when($vehicle, function ($query) use ($vehicle) {
                $query->where('vehicle_id', $vehicle);
            })
            ->when($date_from, function ($query) use ($date_from) {
                $query->whereDate('expiry_date', '>=', $date_from);
            })
            ->when($date_to, function ($query) use ($date_to) {
                $query->whereDate('expiry_date', '<=', $date_to);
            });

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('legal-documentation-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>rt<'bottom'<'row'<'col-md-6'i><'col-md-6'p>>><'clear'>")
            ->orderBy(4)
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
            Column::make('document_type_id')->title(localize('Document Type'))->defaultContent('N/A'),
            Column::make('vehicle_id')->title(localize('Vehicle Name'))->defaultContent('N/A'),
            Column::make('issue_date')->title(localize('Last Issue Date'))->defaultContent('N/A'),
            Column::make('expiry_date')->title(localize('Expiry Date'))->defaultContent('N/A'),
            Column::make('charge_paid')->title(localize('Charge Paid'))->defaultContent('N/A'),
            Column::make('vendor_id')->title(localize('Vendor'))->defaultContent('N/A'),
            Column::make('commission')->title(localize('Commission'))->defaultContent('N/A'),
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
        return 'legal-documentation-'.date('YmdHis');
    }
}
