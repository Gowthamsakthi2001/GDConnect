<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class InventoryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call([
            InventoryCategorySeederTableSeeder::class,
            InventoryLocationSeederTableSeeder::class,
            VendorSeederTableSeeder::class,
            InventoryPartsSeederTableSeeder::class,
            InventoryPartsUsageSeederTableSeeder::class,
            ExpenseTypeTableSeeder::class,
            TripTypeTableSeeder::class,
            ExpenseTableSeeder::class,
        ]);
    }
}
