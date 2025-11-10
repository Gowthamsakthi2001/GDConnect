<?php

namespace Modules\VehicleManagement\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DocumentTypeTableSeeder extends Seeder
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
            'Vehicle Fitness',
            'Vehicle Insurance',
            'Vehicle Tax Token',
            'Vehicle Route Permit',
            'Vehicle Ownership Transfer',
            'Vehicle Road Worthiness',
        ];

        foreach ($data as $key => $value) {
            \Modules\VehicleManagement\Entities\DocumentType::create([
                'name' => $value,
                'is_active' => true,
            ]);
        }
    }
}
