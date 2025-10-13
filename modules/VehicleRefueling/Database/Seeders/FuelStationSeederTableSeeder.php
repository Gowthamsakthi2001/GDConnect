<?php

namespace Modules\VehicleRefueling\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\VehicleRefueling\Entities\FuelStation;

class FuelStationSeederTableSeeder extends Seeder
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
            'Ashraful Islam Fuel Station',
            'Shazzedul Filling Station',
            'Hafiz Fuel Station',
            'Mamun Fuel Station',
            'Rahim Fuel Station',
            'Kabir Fuel Station',
            'Rahman Fuel Station',
            'Salam Fuel Station',
            'Kalam Fuel Station',
            'Jalal Fuel Station',
            'Kamal Fuel Station',
        ];

        foreach ($data as $s) {
            FuelStation::create([
                'vendor_id' => rand(1, 6),
                'name' => $s,
                'contact_person' => fake()->name(),
                'contact_number' => fake()->phoneNumber(),
                'address' => fake()->address(),
                'is_active' => true,
            ]);
        }
    }
}
