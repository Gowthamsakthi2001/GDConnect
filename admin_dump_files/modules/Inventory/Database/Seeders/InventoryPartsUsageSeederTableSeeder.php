<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Inventory\Entities\InventoryPartsUsage;
use Modules\Inventory\Entities\InventoryPartsUsageDetail;

class InventoryPartsUsageSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        // with InventoryPartsUsage details
        InventoryPartsUsage::factory(1000)->create()->each(function ($partsUsage) {
            $partsUsage->details()->saveMany(InventoryPartsUsageDetail::factory(10)->make());
        });
    }
}
