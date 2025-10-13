<?php

namespace Modules\Inventory\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\InventoryParts;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InventoryStockDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addColumn('category_name', function ($query) {
                return $query->category->name ?? 'N/A';
            })
            ->addColumn('in_qty', function ($query) {
                $in_qty = $query->in_qty + $query->qty;

                return number_format($in_qty, 2);
            })
            ->addColumn('out_qty', function ($query) {
                $out_qty = $query->out_qty;

                return number_format($out_qty, 2);
            })
            ->addColumn('current_qty', function ($query) {
                $current_qty = ($query->in_qty + $query->qty) - $query->out_qty;

                return number_format($current_qty, 2);
            })
            ->addColumn('current_value', function ($query) {
                $current_value = (($query->in_qty + $query->qty) - $query->out_qty) * $query->purchaseDetails->avg('in_avg_price');

                return number_format($current_value, 2);
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action']);
    }

    /**
     * Get query source of dataTable.
     */
    public function query(InventoryParts $model): QueryBuilder
    {
        $q = $model->newQuery();
        $from_date = request()->from_date ?? null;
        $to_date = request()->to_date ?? null;
        $category_id = request()->category_id ?? null;
        $parts_id = request()->parts_id ?? null;

        //
        $q = InventoryParts::with([
            'category:id,name',
            'vehicleMaintenanceDetails.maintenance' => function ($q) use ($from_date, $to_date) {
                $q->where('status', 'approved');
                $q->when($from_date, function ($q) use ($from_date) {
                    $q->where('date', '>=', $from_date);
                });
                $q->when($to_date, function ($q) use ($to_date) {
                    $q->where('date', '<=', $to_date);
                });
            },
        ])
            ->when($category_id, function ($q) use ($category_id) {
                $q->where('category_id', $category_id);
            })->when($parts_id, function ($q) use ($parts_id) {
                $q->where('id', $parts_id);
            })
            ->select('id', 'name', 'category_id', 'qty')
            ->withCount([
                'purchaseDetails as in_qty' => function ($q) use ($from_date, $to_date) {
                    $q->whereHas('purchase', function ($q) use ($from_date, $to_date) {
                        $q->where('status', 'approved');
                        $q->when($from_date, function ($q) use ($from_date) {
                            $q->where('date', '>=', $from_date);
                        });
                        $q->when($to_date, function ($q) use ($to_date) {
                            $q->where('date', '<=', $to_date);
                        });
                    });
                    $q->select(DB::raw('SUM(qty)'));
                },
                'vehicleMaintenanceDetails as out_qty' => function ($q) use ($from_date, $to_date) {
                    $q->whereHas('maintenance', function ($q) use ($from_date, $to_date) {
                        $q->where('status', 'approved');
                        $q->when($from_date, function ($q) use ($from_date) {
                            $q->where('date', '>=', $from_date);
                        });
                        $q->when($to_date, function ($q) use ($to_date) {
                            $q->where('date', '<=', $to_date);
                        });
                    });
                    $q->select(DB::raw('SUM(qty)'));
                },
            ])
            ->with([
                'purchaseDetails' => function ($q) use ($from_date, $to_date) {
                    $q->whereHas('purchase', function ($q) use ($from_date, $to_date) {
                        $q->where('status', 'approved');
                        $q->when($from_date, function ($q) use ($from_date) {
                            $q->where('date', '>=', $from_date);
                        });
                        $q->when($to_date, function ($q) use ($to_date) {
                            $q->where('date', '<=', $to_date);
                        });
                    });
                    $q->select('parts_id', DB::raw('AVG(price) as in_avg_price'))->groupBy('parts_id');
                },
                'vehicleMaintenanceDetails' => function ($q) use ($from_date, $to_date) {
                    $q->whereHas('maintenance', function ($q) use ($from_date, $to_date) {
                        $q->where('status', 'approved');
                        $q->when($from_date, function ($q) use ($from_date) {
                            $q->where('date', '>=', $from_date);
                        });
                        $q->when($to_date, function ($q) use ($to_date) {
                            $q->where('date', '<=', $to_date);
                        });
                    });
                    $q->select('parts_id', DB::raw('AVG(price) as out_avg_price'))->groupBy('parts_id');
                },
            ]);

        return $q;
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('inventory-stock-table')
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
            Column::make('category_name')->title(localize('Category'))->defaultContent('N/A')->orderable(false),
            Column::make('name')->title(localize('Parts'))->defaultContent('N/A'),
            Column::make('in_qty')->title(localize('In Quantity'))->defaultContent('00')->orderable(false),
            Column::make('out_qty')->title(localize('Out Quantity'))->defaultContent('00')->orderable(false),
            Column::make('current_qty')->title(localize('Current Quantity'))->defaultContent('00')->orderable(false),
            Column::make('current_value')->title(localize('Stock Value'))->defaultContent('00')->orderable(false),
        ];
    }

    /**
     * Get filename for export.
     */
    protected function filename(): string
    {
        return 'inventory-stock-'.date('YmdHis');
    }
}
