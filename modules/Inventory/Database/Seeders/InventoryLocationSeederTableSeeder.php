<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Entities\InventoryLocation;

class InventoryLocationSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'Main Warehouse',
            'Khulna Warehouse',
            'Dhaka Warehouse',
            'Chittagong Warehouse',
            'Rajshahi Warehouse',
            'Sylhet Warehouse',
            'Barishal Warehouse',
            'Rangpur Warehouse',
            'Mymensingh Warehouse',
            'Comilla Warehouse',
            'Jessore Warehouse',
            'Bogra Warehouse',
        ];

        foreach ($data as $i) {
            InventoryLocation::create([
                'name' => $i,
                'room' => rand(1, 10),
                'self' => rand(1, 10),
                'drawer' => rand(1, 10),
                'capacity' => rand(100, 1000),
                'dimension' => rand(1, 10),
                'is_active' => true,
            ]);
        }
    }
}
