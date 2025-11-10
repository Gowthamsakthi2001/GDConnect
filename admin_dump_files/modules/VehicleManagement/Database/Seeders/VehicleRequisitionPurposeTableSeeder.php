<?php

namespace Modules\VehicleManagement\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class VehicleRequisitionPurposeTableSeeder extends Seeder
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
            'Official Purpose',
            'Personal Purpose',
            'Maintenance Purpose',
            'Re-Fueling Purpose',
            'Accident Purpose',
            'Others',
        ];

        foreach ($data as $key => $value) {
            \Modules\VehicleManagement\Entities\VehicleRequisitionPurpose::create([
                'name' => $value,
                'is_active' => true,
            ]);
        }

    }
}
