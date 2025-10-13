<?php

namespace Modules\RiderBackgroundVerification\DataTables;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Modules\LeaveManagement\Entities\LeaveType; 
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
class RecruiterDataTable extends DataTable
{
    private $roles;

    public function __construct()
    {
        $db = DB::table('model_has_roles')
            ->where('model_id', auth()->user()->id)
            ->first();

        $this->roles = DB::table('roles')
            ->where('id', $db->role_id)
            ->first();
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // return (new EloquentDataTable($query))
        //     ->editColumn('first_name', function ($row) {
        //         return trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')); 
        //     })
        //     ->editColumn('emp_id', function ($row) {
        //         return $row->emp_id ?? '-';
        //     })
        //     ->editColumn('mobile_number', function ($row) {
        //         return $row->mobile_number ?? '-';
        //     })
        //     ->editColumn('work_type', function ($row) {
        //         $workType = "";
        //         if($row->work_type == 'in-house'){
        //             $workType = "Employee";
        //         }else if($row->work_type == 'deliveryman'){
        //             $workType = "Deliveryman";
        //         }else{
        //             $workType = "Adhoc";
        //         }
        //         return $workType;
        //     })
        //       ->editColumn('work_status', function ($row) {
        //          $workStatus = "";
        //         if($row->work_status == 1){
        //             $workStatus = "Helper";
        //         }else if($row->work_status == 2){
        //             $workStatus = "Driver";
        //         }else{
        //             $workStatus = "Pending";
        //         }
        //         return $workStatus;
        //     })
        //     ->editColumn('active_date', function ($row) {
        //          return $row->get_active_date('delivery-man-table');
        //     })
        //     ->editColumn('status', function ($row) {
        //         return $this->statusBtn($row);
        //     })
        //      ->editColumn('last_login_date', function ($row) {
        //          return $row->get_last_login_date('delivery-man-table');
        //     })
        //     ->editColumn('kyc_verify', function ($row) {
        //         return $this->verifiedStatusBtn($row); 
        //     })
        //     ->editColumn('created_at', function ($row) {
        //         return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
        //     })
         
        //     ->addColumn('action', function ($row) {
        //         return $row->actionBtn_visible_supervisor('supervisor-list-table');
        //     })
        //     ->addColumn('image', function ($row) {
        //         $imageUrl = $row->photo
        //             ? asset('public/EV/images/photos/' . $row->photo)
        //             : asset('public/EV/images/dummy.jpg');
                
        //         return "<img src='{$imageUrl}' alt='Deliveryman Image' class='img-thumbnail' width='50' height='50'>";
        //     })
            
        //     ->addColumn('approve', function ($row) {
        //         return $row->actionBtn_visible_supervisor_approve('supervisor-list-table');
        //     })
        //     ->rawColumns(['status', 'kyc_verify', 'action', 'approve','image','last_login_date','active_date']) 
        //     ->setRowId('id')
        //     ->addIndexColumn();
    }

    


    public function query(Deliveryman $model ,Request $request): QueryBuilder
    {

        if($request->client_id != null){
            return $model->newQuery()->with('zone')->where('work_type','adhoc')->orderBy('id', 'desc')->where('client_id',$request->client_id);
        }elseif($request->zone_id != null){
            return $model->newQuery()->with('zone')->where('work_type','adhoc')->orderBy('id', 'desc')->where('zone_id',$request->zone_id);
        }elseif($request->current_city_id != null){
            return $model->newQuery()->with('zone')->where('work_type','adhoc')->orderBy('id', 'desc')->where('current_city_id',$request->current_city_id);
        }
        return $model->newQuery()->with('zone')->where('work_type','adhoc')->orderBy('id', 'desc'); //updated by Gowtham.s
    }

    public function html(): HtmlBuilder
    {
       
        return $this->builder()
            ->setTableId('supervisor-list-table')
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
        $columns = [
            Column::make('DT_RowIndex')->title('SL')->searchable(false)->orderable(false)->width(30)->addClass('text-center'),
            Column::make('reg_application_id')->title('Candidate ID'),
            Column::computed('image')->title('Image')->orderable(false)->searchable(false)->width(50)->addClass('text-center'),
            Column::make('first_name')->title('Candidate Name')->defaultContent('N/A'),
            Column::make('email')->title('Email ID')->defaultContent('N/A'),
            Column::make('verified_at')->title('Verified At')->defaultContent('N/A'),
            Column::make('work_type')->title('Role')->defaultContent('N/A')
            
        ];

        // if ($this->roles->name != 'Telecaller') {
        //     $columns[] = Column::computed('action')->title('Action')->orderable(false)->searchable(false)->exportable(false)->printable(false)->width(200)->addClass('text-center');
        //     $columns[] = Column::computed('approve')
        //         ->title('Approval')
        //         ->orderable(false)
        //         ->searchable(false)
        //         ->exportable(false)
        //         ->printable(false)
        //         ->width(80)
        //         ->addClass('text-center');
        // }

        return $columns;
    }

    protected function filename(): string
    {
        return 'Deliveryman_' . date('YmdHis');
    }

    private function statusBtn(Deliveryman $deliveryman): string
    {
        $isChecked = $deliveryman->rider_status ? 'checked' : '';
        $toggleStatusUrl = route('admin.Green-Drive-Ev.delivery-man.status', [$deliveryman->id, $deliveryman->rider_status ? 0 : 1]);
        $toggleText = $deliveryman->rider_status ? 'Deactivate' : 'Activate';

        return <<<HTML
            <div class="form-check form-switch">
                <label class="toggle-switch" for="statusCheckbox_{$deliveryman->id}">
                    <input type="checkbox" onclick="status_change_alert('{$toggleStatusUrl}', '{$toggleText} this Adhoc?', event)" class="form-check-input toggle-btn" id="statusCheckbox_{$deliveryman->id}" {$isChecked}>
                    <span class="toggle-switch-label">
                        <span class="toggle-switch-indicator"></span>
                    </span>
                </label>
            </div>
        HTML;
    }
    private function verifiedStatusBtn(Deliveryman $deliveryman): string
    {
        $isChecked = $deliveryman->kyc_verify ? 'checked' : '';
        $toggleVerifiedUrl = route('admin.Green-Drive-Ev.delivery-man.kyc_verify', [$deliveryman->id, $deliveryman->kyc_verify ? 0 : 1]);
        $toggleText = $deliveryman->kyc_verify ? 'Unverify' : 'Verify';

        return <<<HTML
            <div class="form-check form-switch">
                <label class="toggle-switch" for="verifiedStatusCheckbox_{$deliveryman->id}">
                    <input type="checkbox" onclick="status_change_alert('{$toggleVerifiedUrl}', '{$toggleText} this Adhoc?', event)" class="form-check-input toggle-btn" id="verifiedStatusCheckbox_{$deliveryman->id}" {$isChecked}>
                    <span class="toggle-switch-label">
                        <span class="toggle-switch-indicator"></span>
                    </span>
                </label>
            </div>
        HTML;
    }
}