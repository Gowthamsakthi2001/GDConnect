<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Employee\Entities\Department;
use Modules\Employee\Entities\Driver;
use Modules\Inventory\Entities\Vendor;
use Modules\VehicleManagement\DataTables\VehicleDataTable;
use Modules\VehicleManagement\Entities\RTAOffice;
use Modules\VehicleManagement\Entities\Vehicle;
use Modules\VehicleManagement\Entities\VehicleOwnershipType;
use Modules\VehicleManagement\Entities\VehicleType;

class VehicleManageController extends Controller
{
    public function amv_dashboard(Request $request){
        return view('vehiclemanagement::amv_dashboard');
    }
}