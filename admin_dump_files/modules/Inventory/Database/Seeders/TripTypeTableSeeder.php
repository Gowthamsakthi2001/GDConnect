<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class TripTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $tripTypes = [
            ['name' => 'Local', 'is_active' => true],
            ['name' => 'Outstation', 'is_active' => true],
            ['name' => 'International', 'is_active' => true],
        ];

        foreach ($tripTypes as $tripType) {
            \Modules\Inventory\Entities\TripType::create($tripType);
        }

    }
}
