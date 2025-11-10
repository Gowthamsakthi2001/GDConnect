<?php

namespace Modules\VehicleManagement\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class VehicleOwnershipTypeSeederTableSeeder extends Seeder
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
            'Rented Own',
            'Company Owned',
            'Leased',
            'Bank Financed',
            'Third Party Financed',
            'Own',
            'Others',
        ];

        foreach ($data as $key => $value) {
            \Modules\VehicleManagement\Entities\VehicleOwnershipType::create([
                'name' => $value,
                'is_active' => true,
            ]);
        }
    }
}
