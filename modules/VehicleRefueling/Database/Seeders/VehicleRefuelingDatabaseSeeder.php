<?php

namespace Modules\VehicleRefueling\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class VehicleRefuelingDatabaseSeeder extends Seeder
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
            FuelTypeSeederTableSeeder::class,
            FuelStationSeederTableSeeder::class,
            VehicleRefuelingSeederTableSeeder::class,
            FuelRequisitionSeederTableSeeder::class,
        ]);
    }
}
