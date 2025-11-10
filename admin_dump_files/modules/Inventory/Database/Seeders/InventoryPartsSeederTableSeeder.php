<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Inventory\Entities\InventoryCategory;

class InventoryPartsSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $categories = InventoryCategory::get();

        foreach ($categories as $si => $category) {
            $category->parts()->createMany([
                [
                    'name' => 'Part '.rand(100, 100000),
                    'location_id' => rand(1, 12),
                    'description' => 'Part '.rand(100, 10000).' Description',
                    'qty' => rand(100, 1000),
                    'is_active' => true,
                ],
            ]);
        }
    }
}
