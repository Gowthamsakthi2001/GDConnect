<?php

namespace Modules\VehicleManagement\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class VehicleRequisitionTypeSeederTableSeeder extends Seeder
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
            'Vehicle Requisition',
            'Maintenance Requisition',
            'Re-Fueling Requisition',
            'Others',
        ];

        foreach ($data as $key => $value) {
            \Modules\VehicleManagement\Entities\VehicleRequisitionType::create([
                'name' => $value,
                'is_active' => true,
            ]);
        }
    }
}
