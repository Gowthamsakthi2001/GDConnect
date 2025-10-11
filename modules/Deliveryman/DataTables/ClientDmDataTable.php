<?php

namespace Modules\Deliveryman\DataTables;

use Modules\Deliveryman\Entities\Deliveryman;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Modules\Zones\Entities\Zones;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ClientDmReport; //updated by Gowtham.s

class ClientDmDataTable extends DataTable
{
    public function dataTable($query)
    {
      
        return datatables()->of($query)
            ->editColumn('client_name', function ($row) {
                return $row['client_name'] ?? 'N/A';
            })
            ->editColumn('driver_name', function ($row) {
                return $row['driver_name'] ?? 'N/A';
            })
            ->editColumn('chass_serial_no', function ($row) {
                return $row['chass_serial_no'] ?? 'N/A';
            })
            ->editColumn('start_date', function ($row) {
                return $row['start_date'];
            })
            ->editColumn('end_date', function ($row) {
                return $row['end_date'];
            })
            ->editColumn('total_working_time', function ($row) {
                return $row['total_working_time'] ?? 'N/A';
            })
            ->setRowId('driver_id')
            ->addIndexColumn();
    }



    
  public function query()
    {
        $reports = DB::select("
            WITH paired_times AS (
                SELECT 
                    driver_id,
                    client_id,
                    start_time,
                    chass_serial_no,
                    LEAD(end_time) OVER (PARTITION BY driver_id, client_id ORDER BY id) AS next_end_time
                FROM 
                    ev_client_based_dm_working_reports
            )
            SELECT 
                driver_id,
                client_id,
                chass_serial_no,
                MIN(start_time) AS start_date,
                MAX(next_end_time) AS end_date,
                SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, start_time, next_end_time))) AS total_working_time
            FROM 
                paired_times
            WHERE 
                start_time IS NOT NULL 
                AND next_end_time IS NOT NULL
            GROUP BY 
                driver_id, client_id, chass_serial_no;
        ");
    
        $report_data = [];
        foreach ($reports as $report) {
            $dm_data = Deliveryman::find($report->driver_id);
            $client_data = DB::table('ev_tbl_clients')->where('id', $report->client_id)->first();
            $report_data[] = [
                'driver_id' => $report->driver_id,
                'driver_name' => $dm_data ? $dm_data->first_name . ' ' . $dm_data->last_name : 'N/A',
                'client_id' => $report->client_id,
                'client_name' => $client_data ? $client_data->client_name : 'N/A',
                'chass_serial_no' => $report->chass_serial_no,
                'start_date' => date('d M Y H:i:s A', strtotime($report->start_date)),
                'end_date' => date('d M Y H:i:s A', strtotime($report->end_date)),
                'total_working_time' => $report->total_working_time,
            ];
        }
        return collect($report_data);
    }


    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('client-rider-logs-table')
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
                'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            ])
            ->buttons([
                Button::make('reset')->className('btn btn-success box-shadow--4dp btn-sm-menu'),
                Button::make('reload')->className('btn btn-success box-shadow--4dp btn-sm-menu'),
            ]);
    }
    
   public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')
                ->title('SL')
                ->searchable(false)
                ->orderable(false)
                ->width(30)
                ->addClass('text-center'),
    
            Column::make('client_name')
                ->title('Client')
                ->defaultContent('N/A'),
    
            Column::make('driver_name')
                ->title('Rider')
                ->defaultContent('N/A'),
    
            Column::make('zone_name') 
                ->title('Zone')
                ->defaultContent('N/A'),
    
            Column::make('chass_serial_no') 
                ->title('Chassis Serial No')
                ->defaultContent('N/A'),
    
            Column::make('start_date')
                ->title('Start Date')
                ->defaultContent('N/A'),
    
            Column::make('end_date')
                ->title('End Date')
                ->defaultContent('N/A'),
    
            Column::make('total_working_time') 
                ->title('Total Working Hours')
                ->defaultContent('N/A'),
        ];
    }


}