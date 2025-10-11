<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Inventory\Entities\Expense;
use Modules\Inventory\Entities\InventoryParts;
use Modules\Purchase\Entities\PurchaseDetail;
use Modules\VehicleMaintenance\Entities\VehicleMaintenance;
use Modules\VehicleMaintenance\Entities\VehicleMaintenanceDetail;
use Modules\VehicleManagement\Entities\LegalDocumentation;
use Modules\VehicleManagement\Entities\PickupAndDrop;
use Modules\VehicleManagement\Entities\VehicleRequisition;
use Modules\VehicleRefueling\Entities\FuelRequisition;
use Illuminate\Http\Request;
use App\Models\LoginTimeRecord;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\City\Entities\City;
use Modules\Zones\Entities\Zones; //updated by Gowtham.s - Zone Map
class GetZoneController extends Controller
{
    public function getZones(Request $request, $city_id) //updated by Gowtham.s - Zone Map
    {
        $zones = Zones::where('city_id', $city_id)->where('status',1)->get();
    
        return response()->json([
            'success' => true,
            'data' => $zones
        ]);
    }
}