<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Database\Seeders\EmployeeDatabaseSeeder;
use Modules\Inventory\Database\Seeders\InventoryDatabaseSeeder;
use Modules\Language\Database\Seeders\LanguageTableSeeder;
use Modules\Purchase\Database\Seeders\PurchaseDatabaseSeeder;
use Modules\Setting\Database\Seeders\SettingSeeder;
use Modules\VehicleMaintenance\Database\Seeders\VehicleMaintenanceDatabaseSeeder;
use Modules\VehicleManagement\Database\Seeders\VehicleManagementDatabaseSeeder;
use Modules\VehicleRefueling\Database\Seeders\VehicleRefuelingDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // db foreign key check disable
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->call([
            LanguageTableSeeder::class,
            RoleTableSeeder::class,
            SettingSeeder::class,
            // EmployeeDatabaseSeeder::class,
            // InventoryDatabaseSeeder::class,
            // VehicleMaintenanceDatabaseSeeder::class,
            // VehicleManagementDatabaseSeeder::class,
            // VehicleRefuelingDatabaseSeeder::class,
            // PurchaseDatabaseSeeder::class,

        ]);
        // db foreign key check enable
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Artisan::call('optimize:clear');
    }
}
