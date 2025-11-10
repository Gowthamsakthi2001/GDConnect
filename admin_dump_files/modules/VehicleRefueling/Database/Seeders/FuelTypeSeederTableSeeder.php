<?php

namespace Modules\VehicleRefueling\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\VehicleRefueling\Entities\FuelType;

class FuelTypeSeederTableSeeder extends Seeder
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
            'Petrol',
            'Diesel',
            'CNG',
            'LPG',
            'Electric',
            'Hybrid',
            'Bio-Diesel',
            'Bio-Ethanol',
            'Bio-Gas',
            'Hydrogen',
            'Methanol',
        ];

        foreach ($data as $key => $value) {
            FuelType::create([
                'name' => $value,
                'is_active' => true,
            ]);
        }
    }
}
