<?php

namespace Modules\VehicleManagement\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class VehicleDivisionTableSeeder extends Seeder
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
            'Accra',
            'Tema',
            'Kumasi',
            'Tamale',
            'Takoradi',
            'BMW M',

        ];

        foreach ($data as $key => $value) {
            \Modules\VehicleManagement\Entities\VehicleDivision::create([
                'name' => $value,
                'is_active' => true,
            ]);
        }
    }
}
