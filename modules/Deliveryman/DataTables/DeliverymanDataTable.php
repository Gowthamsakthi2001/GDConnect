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
use Modules\Clients\Entities\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliverymanDataTable extends DataTable
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
        return (new EloquentDataTable($query))
            ->editColumn('first_name', function ($row) {
                return trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')); // Combine first_name and last_name
            })
            ->editColumn('emp_id', function ($row) {
                return $row->emp_id ?? '-';
            })
            ->editColumn('mobile_number', function ($row) {
                return $row->mobile_number ?? '-';
            })
            ->editColumn('work_type', function ($row) {
                 $workType = "";
                if($row->work_type == 'in-house'){
                    $workType = "Employee";
                }else if($row->work_type == 'deliveryman'){
                    $workType = "Deliveryman";
                }else{
                    $workType = "Adhoc";
                }
                return $workType;
            })
            ->editColumn('status', function ($row) {
                return $this->statusBtn($row);
            })
            ->editColumn('last_login_date', function ($row) {
                // return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y');
                 return $row->get_last_login_date('delivery-man-table');
            })
            ->editColumn('kyc_verify', function ($row) {
                return $this->verifiedStatusBtn($row); // Add toggle button for kyc_verify
            })
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
            })
            
            ->addColumn('city_name', function ($row) {
                return $row->current_city->city_name ?? '-'; // Display zone name or 'N/A' if null
            })
            ->addColumn('zone_name', function ($row) {
                return $row->zone->name ?? '-'; // Display zone name or 'N/A' if null
            })
            ->addColumn('client_name', function ($row) {
                return $row->client->client_name ?? '-'; // Display zone name or 'N/A' if null
            })
            ->addColumn('hub_name', function ($row) {
                // return $row->client->hub_name ?? '-'; 
                return  $row->get_client_hub();
            })
            ->addColumn('action', function ($row) {
                return $row->actionBtn('delivery-man-table');
            })
            ->addColumn('image', function ($row) {
            // Dynamically create image URL or fallback to a placeholder
                $imageUrl = $row->photo
                    ? asset('public/EV/images/photos/' . $row->photo)
                    : asset('public/EV/images/dummy.jpg');
                
                // Return the HTML for the image
                return "<img src='{$imageUrl}' alt='Deliveryman Image' class='img-thumbnail' width='50' height='50'>";
            })
             ->addColumn('aadhar_verify', function ($row) {
                $status = $row->aadhar_verify ?'Yes': 'No';
                return $this->adhaar_statusBtn($row);
            })
            ->addColumn('pan_verify', function ($row) {
                
                $status = $row->pan_verify ?'Yes': 'No';
               return $this->pan_statusBtn($row);
            })
            ->addColumn('bank_verify', function ($row) {
                $status = $row->bank_verify ?'Yes': 'No';
                return $this->bank_statusBtn($row);
            })
            ->addColumn('lisence_verify', function ($row) {
                $status = $row->lisence_verify ?'Yes' : 'No';
                return $this->license_statusBtn($row);
            })
            ->addColumn('approve', function ($row) {
                return $row->approveBtn('delivery-man-table');
            })
            ->rawColumns(['status', 'kyc_verify', 'action', 'approve','image','last_login_date','aadhar_verify','pan_verify','bank_verify','lisence_verify']) // Add kyc_verify to rawColumns
            ->setRowId('id')
            ->addIndexColumn();
    }

    


    public function query(Deliveryman $model ,Request $request): QueryBuilder
    {

        if($request->client_id != null){
            return $model->newQuery()->with('zone')->where('work_type','deliveryman')->orderBy('id', 'desc')->where('client_id',$request->client_id);
        }elseif($request->zone_id != null){
            return $model->newQuery()->with('zone')->where('work_type','deliveryman')->orderBy('id', 'desc')->where('zone_id',$request->zone_id);
        }elseif($request->current_city_id != null){
            return $model->newQuery()->with('zone')->where('work_type','deliveryman')->orderBy('id', 'desc')->where('current_city_id',$request->current_city_id);
        }
        return $model->newQuery()->with('zone')->where('work_type','deliveryman')->orderBy('id', 'desc'); //updated by Gowtham.s
        // return $model->newQuery()->with('zone')->orderBy('id', 'desc');
    }

    public function html(): HtmlBuilder
    {
       
        return $this->builder()
            ->setTableId('delivery-man-table')
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
            Column::computed('image')->title('Image')->orderable(false)->searchable(false)->width(50)->addClass('text-center'),
            Column::make('first_name')->title('Deliveryman Name')->defaultContent('N/A'),
            Column::make('emp_id')->title('GDM ID')->defaultContent('N/A'),
            Column::make('mobile_number')->title('Mobile Number')->defaultContent('N/A'),
            Column::make('work_type')->title('Work Type')->defaultContent('N/A'),
            Column::make('city_name')->title('City')->defaultContent('N/A'),
            Column::make('zone_name')->title('Zone')->defaultContent('N/A'),
            Column::make('client_name')->title('Client Name')->defaultContent('N/A'),
            Column::make('hub_name')->title('Hub Name')->defaultContent('N/A'),
            Column::make('status')->title('Rider Status')->orderable(false)->searchable(false)->width(30)->addClass('text-center'),
            Column::make('last_login_date')->title('Last Login Date')->orderable(false)->searchable(false)->width(30)->addClass('text-center'),
            // Column::make('last_login_date')->title('Last Login Date')->defaultContent('N/A'),
            Column::make('aadhar_verify')->title('Aadhar Verified')->defaultContent('N/A'),
            Column::make('pan_verify')->title('Pan Verified')->defaultContent('N/A'),
            Column::make('bank_verify')->title('Bank Verified')->defaultContent('N/A'),
            Column::make('lisence_verify')->title('License Verified')->defaultContent('N/A'),
            
        ];

        if ($this->roles->name != 'Telecaller') {
            $columns[] = Column::computed('action')->title('Action')->orderable(false)->searchable(false)->exportable(false)->printable(false)->width(200)->addClass('text-center');
            $columns[] = Column::computed('approve')
                ->title('Approval')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->width(80)
                ->addClass('text-center');
        }

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
                    <input type="checkbox" onclick="status_change_alert('{$toggleStatusUrl}', '{$toggleText} this Deliveryman?', event)" class="form-check-input toggle-btn" id="statusCheckbox_{$deliveryman->id}" {$isChecked}>
                    <span class="toggle-switch-label">
                        <span class="toggle-switch-indicator"></span>
                    </span>
                </label>
            </div>
        HTML;
    }
    
    private function adhaar_statusBtn(Deliveryman $deliveryman): string
    {
        $isChecked = $deliveryman->aadhar_verify ? 'checked' : '';
        $toggleStatusUrl = route('admin.Green-Drive-Ev.delivery-man.status', [$deliveryman->id, $deliveryman->aadhar_verify ? 0 : 1]); 
        $toggleText = $deliveryman->aadhar_verify ? 'Deactivate' : 'Activate';

        return <<<HTML
            <div class="form-check form-switch">
                <label class="toggle-switch" for="adhaar_statusCheckbox_{$deliveryman->id}">
                    <input type="checkbox" class="form-check-input toggle-btn" id="adhaar_statusCheckbox_{$deliveryman->id}" {$isChecked}>
                    <span class="toggle-switch-label">
                        <span class="toggle-switch-indicator"></span>
                    </span>
                </label>
            </div>
        HTML;
    }
     private function pan_statusBtn(Deliveryman $deliveryman): string
    {
        $isChecked = $deliveryman->pan_verify ? 'checked' : '';
        $toggleStatusUrl = route('admin.Green-Drive-Ev.delivery-man.status', [$deliveryman->id, $deliveryman->pan_verify ? 0 : 1]); 
        $toggleText = $deliveryman->pan_verify ? 'Deactivate' : 'Activate';

        return <<<HTML
            <div class="form-check form-switch">
                <label class="toggle-switch" for="pan_statusCheckbox_{$deliveryman->id}">
                    <input type="checkbox" class="form-check-input toggle-btn" id="pan_statusCheckbox_{$deliveryman->id}" {$isChecked}>
                    <span class="toggle-switch-label">
                        <span class="toggle-switch-indicator"></span>
                    </span>
                </label>
            </div>
        HTML;
    }
    
    private function bank_statusBtn(Deliveryman $deliveryman): string
    {
        $isChecked = $deliveryman->bank_verify ? 'checked' : '';
        $toggleStatusUrl = route('admin.Green-Drive-Ev.delivery-man.status', [$deliveryman->id, $deliveryman->bank_verify ? 0 : 1]); 
        $toggleText = $deliveryman->bank_verify ? 'Deactivate' : 'Activate';

        return <<<HTML
            <div class="form-check form-switch">
                <label class="toggle-switch" for="bank_statusCheckbox_{$deliveryman->id}">
                    <input type="checkbox" class="form-check-input toggle-btn" id="bank_statusCheckbox_{$deliveryman->id}" {$isChecked}>
                    <span class="toggle-switch-label">
                        <span class="toggle-switch-indicator"></span>
                    </span>
                </label>
            </div>
        HTML;
    }
    
    private function license_statusBtn(Deliveryman $deliveryman): string
    {
        $isChecked = $deliveryman->lisence_verify ? 'checked' : '';
        $toggleStatusUrl = route('admin.Green-Drive-Ev.delivery-man.status', [$deliveryman->id, $deliveryman->lisence_verify ? 0 : 1]); 
        $toggleText = $deliveryman->lisence_verify ? 'Deactivate' : 'Activate';

        return <<<HTML
            <div class="form-check form-switch">
                <label class="toggle-switch" for="license_statusCheckbox_{$deliveryman->id}">
                    <input type="checkbox" class="form-check-input toggle-btn" id="license_statusCheckbox_{$deliveryman->id}" {$isChecked}>
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
                    <input type="checkbox" onclick="status_change_alert('{$toggleVerifiedUrl}', '{$toggleText} this Deliveryman?', event)" class="form-check-input toggle-btn" id="verifiedStatusCheckbox_{$deliveryman->id}" {$isChecked}>
                    <span class="toggle-switch-label">
                        <span class="toggle-switch-indicator"></span>
                    </span>
                </label>
            </div>
        HTML;
    }
}
