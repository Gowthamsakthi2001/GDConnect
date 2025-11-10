<?php

namespace Modules\VehicleMaintenance\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\VehicleMaintenance\Entities\VehicleMaintenance;
use Modules\VehicleMaintenance\Entities\VehicleMaintenanceDetail;

class VehicleMaintenanceSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        VehicleMaintenance::factory(1000)->create()->each(function ($vehicleMaintenance) {
            $vehicleMaintenance->details()->saveMany(VehicleMaintenanceDetail::factory(5)->make());
        });
    }
}
