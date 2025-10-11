<?php

namespace Modules\VehicleManagement\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\VehicleManagement\Entities\VehicleInsuranceRecurringPeriod;

class VehicleInsuranceRecurringPeriodSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $data = [
            'Daily',
            'Weekly',
            'Three Weekly',
            'Monthly',
            'Six Monthly',
            'Yearly',
            'Two Yearly',
        ];

        foreach ($data as $key => $value) {
            VehicleInsuranceRecurringPeriod::create([
                'name' => $value,
                'is_active' => true,
            ]);
        }
    }
}
