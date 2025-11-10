<?php

namespace Modules\VehicleManagement\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class VehicleInsuranceCompanySeederTableSeeder extends Seeder
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
            'Royal Enfield',
            'Bajaj',
            'Hero',
            'Honda',
            'Yamaha',
            'Suzuki',
            'TVS',
            'KTM',
            'Mahindra',
            'Harley Davidson',
            'Kawasaki',
            'Ducati',
            'BMW',
        ];

        foreach ($data as $key => $value) {
            \Modules\VehicleManagement\Entities\VehicleInsuranceCompany::create([
                'name' => $value,
                'is_active' => true,
            ]);
        }
    }
}
