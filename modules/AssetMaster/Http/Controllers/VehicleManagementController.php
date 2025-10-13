<?php

namespace Modules\AssetMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AssetMasterVehicleImport; //updated by Gowtham.s

use Modules\AssetMaster\Entities\AmsLocationMaster; 
use Modules\AssetMaster\Entities\AssetInsuranceDetails;
use Modules\AssetMaster\Entities\AssetMasterBattery;
use Modules\AssetMaster\Entities\AssetMasterCharger;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\AssetMaster\Entities\ManufacturerMaster;
use Modules\AssetMaster\Entities\ModalMasterVechile;
use Modules\AssetMaster\Entities\ModelMasterBattery;
use Modules\AssetMaster\Entities\ModelMasterCharger;
use Modules\AssetMaster\Entities\AssetStatus;//updated by Gowtham.s
use Modules\AssetMaster\Entities\PoTable;
use Modules\Deliveryman\Entities\Deliveryman;

//dataTable
use Modules\AssetMaster\DataTables\ModalMasterVechileDataTable;
use Modules\AssetMaster\DataTables\ModalMasterBatteryDataTable;
use Modules\AssetMaster\DataTables\ModalMasterChargerDataTable;
use Modules\AssetMaster\DataTables\ManufactureMasterDataTable;
use Modules\AssetMaster\DataTables\PotableDataTable;
use Modules\AssetMaster\DataTables\AmsLocationMasterDataTable;
use Modules\AssetMaster\DataTables\AssetInsuranceDataTable;
use Modules\AssetMaster\DataTables\AssetMasterBatteryDataTable;
use Modules\AssetMaster\DataTables\AssetMasterChargerDataTable;
use Modules\AssetMaster\DataTables\AssetMasterVechileDataTables;
use Modules\AssetMaster\DataTables\AssetStatusDataTable;

class VehicleManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function vehicle_manage_dashboard()
    {
        return view('assetmaster::modal_master_vechile.index');
    }
    
    public function asset_manage_dashboard(){
        dd("hii");
        return view('assetmaster::asset_management_dashboard');
    }

}