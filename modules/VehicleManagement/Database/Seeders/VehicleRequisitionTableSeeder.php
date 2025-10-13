<?php

namespace Modules\VehicleManagement\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\VehicleManagement\Entities\VehicleRequisition;

class VehicleRequisitionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        VehicleRequisition::factory(10000)->create();
    }
}
