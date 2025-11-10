<?php

namespace Modules\VehicleManagement\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class VehicleTypeSeederTableSeeder extends Seeder
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
            'Saloon Car',
            'Pick Up',
            'Van',
            'Bus',
            'Truck',
            'Motorcycle',
            'Bicycle',
            'Others',
        ];

        foreach ($data as $key => $value) {
            \Modules\VehicleManagement\Entities\VehicleType::create([
                'name' => $value,
                'is_active' => true,
            ]);
        }
    }
}
