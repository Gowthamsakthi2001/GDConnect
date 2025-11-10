<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Employee\Entities\Department;
use Modules\Employee\Entities\Driver;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Entities\Position;
use Modules\Inventory\Entities\Vendor;
use Modules\Report\DataTables\DriverDataTable;
use Modules\Report\DataTables\EmployeeDataTable;
use Modules\Report\DataTables\ExpenseDataTable;
use Modules\Report\DataTables\FuelRequisitionDataTable;
use Modules\Report\DataTables\PickAndDropDataTable;
use Modules\Report\DataTables\PurchaseDataTable;
use Modules\Report\DataTables\VehicleDataTable;
use Modules\Report\DataTables\VehicleMaintenanceDataTable;
use Modules\Report\DataTables\VehicleRequisitionDataTable;
use Modules\VehicleMaintenance\Entities\VehicleMaintenanceType;
use Modules\VehicleManagement\Entities\Vehicle;
use Modules\VehicleManagement\Entities\VehicleOwnershipType;
use Modules\VehicleManagement\Entities\VehicleRouteDetail;
use Modules\VehicleManagement\Entities\VehicleType;
use Modules\VehicleRefueling\Entities\FuelType;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:report_management', 'status_check']);

        \cs_set('theme', [
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Report',
                    'link' => false,
                ],
            ],
        ]);
    }
    
    public function live_orders(Request $request)
    {
        $orders = Order::orderBy('created_at','desc')->get();
        return view('report::live_orders', compact('orders')); 
    }
    
    public function live_order_view(Request $request,$id)
    {
        $order = Order::with(['order_details'])->where('id',$id)->first();
        if(!$order){
            return back()->with('error','Order not found');
        }
        return view('report::live-order-view', compact('order')); 
    }
    

    /**
     * Display a listing of the resource.
     */
    public function employee_report(EmployeeDataTable $dataTable)
    {
        \cs_set('theme', [
            'title' => 'Employee Report',
        ]);

        return $dataTable->render('report::employee.index', [
            'departments' => Department::all(),
            'positions' => Position::all(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function driver_report(DriverDataTable $dataTable)
    {

        \cs_set('theme', [
            'title' => 'Driver Report',
        ]);

        return $dataTable->render('report::driver.index', [
            'drivers' => Driver::all(),
            'vehicles' => Vehicle::all(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function vehicle_report(VehicleDataTable $dataTable)
    {
        \cs_set('theme', [
            'title' => 'Vehicle Report',
        ]);

        return $dataTable->render('report::vehicle.index', [
            'departments' => Department::all(),
            'vehicle_types' => VehicleType::all(),
            'ownerships' => VehicleOwnershipType::all(),
            'vendors' => Vendor::all(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function vehicle_requisition_report(VehicleRequisitionDataTable $dataTable)
    {
        \cs_set('theme', [
            'title' => 'Vehicle Requisition Report',
        ]);

        return $dataTable->render('report::vehicle.requisition.index', [
            'employees' => Employee::all(),
            'vehicle_types' => VehicleType::all(),
            'drivers' => Driver::all(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function pickdrop_requisition_report(PickAndDropDataTable $dataTable)
    {
        \cs_set('theme', [
            'title' => 'Pick And Drop Requisition Report',
        ]);

        return $dataTable->render('report::pick_drop.index', [
            'vehicle_routes' => VehicleRouteDetail::where('is_active', 1)->get(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function refuel_requisition_report(FuelRequisitionDataTable $dataTable)
    {
        \cs_set('theme', [
            'title' => 'Fuel Requisition Report',
        ]);

        return $dataTable->render('report::fuel_requisition.index', [
            'vehicles' => Vehicle::all(),
            'fuel_types' => FuelType::all(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function purchase_report(PurchaseDataTable $dataTable)
    {
        \cs_set('theme', [
            'title' => 'Purchase Report',
        ]);

        return $dataTable->render('report::purchase.index', [
            'vendors' => Vendor::all(),
        ]);
    }

    public function expense_report(ExpenseDataTable $dataTable)
    {
        \cs_set('theme', [
            'title' => 'Expense Report',
        ]);

        return $dataTable->render('report::expense.index', [
            'vendors' => Vendor::all(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function maintenance_report(VehicleMaintenanceDataTable $dataTable)
    {
        \cs_set('theme', [
            'title' => 'Maintenance Report',
        ]);

        return $dataTable->render('report::maintenance.index', [
            'maintenance_types' => VehicleMaintenanceType::all(),
            'vehicles' => Vehicle::all(),

        ]);
    }
}
