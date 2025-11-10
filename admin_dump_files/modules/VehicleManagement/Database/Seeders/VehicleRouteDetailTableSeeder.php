<?php

namespace Modules\VehicleManagement\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class VehicleRouteDetailTableSeeder extends Seeder
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
            'Dhaka to Mymensingh',
        ];

        foreach ($data as $key => $value) {
            \Modules\VehicleManagement\Entities\VehicleRouteDetail::create([
                'route_name' => $value,
                'starting_point' => 'Dhaka',
                'destination_point' => 'Mymensingh',
                'description' => 'Dhaka to Mymensingh via Gazipur Chowrasta',
                'is_active' => true,
                'create_pick_drop_point' => false,
            ]);
        }

    }
}
