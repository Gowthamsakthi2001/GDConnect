<?php

namespace Modules\VehicleMaintenance\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class VehicleMaintenanceTypeSeederTableSeeder extends Seeder
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
            'Oil Change',
            'Filter Change',
            'Engine Tuning',
            'Brake Service',
            'Wheel Alignment',
            'Wheel Balancing',
            'Battery Service',
            'AC Service',
            'Suspension Service',
            'Transmission Service',
            'Electrical Service',
            'Cooling System Service',
            'Exhaust System Service',
            'Fuel System Service',
            'Steering Service',
            'Clutch Service',
            'Tyre Service',
            'Wiper Service',
            'Light Service',

        ];

        foreach ($data as $key => $value) {
            \Modules\VehicleMaintenance\Entities\VehicleMaintenanceType::create([
                'name' => $value,
                'is_active' => true,
            ]);
        }
    }
}
