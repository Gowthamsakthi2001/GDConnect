<?php

namespace Modules\VehicleManagement\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\VehicleManagement\Entities\LegalDocumentation;

class LegalDocumentationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        LegalDocumentation::factory(1000)->create();
    }
}
