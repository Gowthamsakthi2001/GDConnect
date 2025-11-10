<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class LicenseTypeSeeder extends Seeder
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
            'Driver License', 'Motorcycle License', 'Forklift License', 'Heavy Equipment License', 'Crane License', 'Truck License', 'Bus License', 'Boat License', 'Aircraft License', 'Helicopter License', 'Train License', 'Subway License', 'Taxi License', 'Ride Sharing License', 'Others',
        ];
        foreach ($data as $value) {
            \Modules\Employee\Entities\LicenseType::create([
                'name' => $value,
            ]);
        }
    }
}
