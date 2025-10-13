<?php

namespace Modules\VehicleRefueling\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\VehicleRefueling\Entities\VehicleRefueling;

class VehicleRefuelingSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        VehicleRefueling::factory(1000)->create();
    }
}
