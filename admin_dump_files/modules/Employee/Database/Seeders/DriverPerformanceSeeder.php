<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Employee\Entities\DriverPerformance;

class DriverPerformanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        DriverPerformance::factory(1000)->create();
    }
}
