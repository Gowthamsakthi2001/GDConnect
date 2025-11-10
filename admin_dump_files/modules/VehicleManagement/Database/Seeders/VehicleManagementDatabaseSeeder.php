<?php

namespace Modules\VehicleManagement\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class VehicleManagementDatabaseSeeder extends Seeder
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
            VehicleInsuranceRecurringPeriodSeederTableSeeder::class,
            VehicleInsuranceCompanySeederTableSeeder::class,
            RTAOfficeSeederTableSeeder::class,
            VehicleOwnershipTypeSeederTableSeeder::class,
            VehicleTypeSeederTableSeeder::class,
            VehicleTableSeeder::class,
            VehicleRequisitionTableSeeder::class,
            InsuranceTableSeeder::class,
            DocumentTypeTableSeeder::class,
            LegalDocumentationTableSeeder::class,
            VehicleRouteDetailTableSeeder::class,
            VehicleDivisionTableSeeder::class,
            VehicleRequisitionTypeSeederTableSeeder::class,
            VehicleRequisitionPurposeTableSeeder::class,
        ]);
    }
}
