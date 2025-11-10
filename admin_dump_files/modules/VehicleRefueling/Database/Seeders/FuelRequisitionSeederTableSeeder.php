<?php

namespace Modules\VehicleRefueling\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\VehicleRefueling\Entities\FuelRequisition;

class FuelRequisitionSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        FuelRequisition::factory()->count(1000)->create();
    }
}
