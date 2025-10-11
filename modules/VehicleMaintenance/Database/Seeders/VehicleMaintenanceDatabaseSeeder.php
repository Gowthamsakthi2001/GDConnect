<?php

namespace Modules\VehicleMaintenance\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class VehicleMaintenanceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call([
            VehicleMaintenanceTypeSeederTableSeeder::class,
            VehicleMaintenanceSeederTableSeeder::class,
        ]);
    }
}
