<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;

class InventoryCategorySeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'Tire',
            'Engine',
            'Battery',
            'Brake',
            'Suspension',
            'Steering',
            'Transmission',
            'Electrical',
            'Cooling',
            'Exhaust',
            'Fuel',
            'Ignition',
            'Lighting',
            'Heating',
            'Air Conditioning',
            'Wiper',
            'Filters',
            'Belts',
            'Hoses',
            'Gasket',
            'Seal',
            'Bearing',
            'Clutch',
        ];

        foreach ($categories as $category) {
            \Modules\Inventory\Entities\InventoryCategory::create([
                'name' => $category,
                'is_active' => true,
            ]);
        }
    }
}
