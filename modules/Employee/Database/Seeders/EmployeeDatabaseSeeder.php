<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class EmployeeDatabaseSeeder extends Seeder
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
            DepartmentSeeder::class,
            LicenseTypeSeeder::class,
            PositionSeeder::class,
            EmployeeSeeder::class,
            DriverSeeder::class,
            DriverPerformanceSeeder::class,
        ]);
    }
}
