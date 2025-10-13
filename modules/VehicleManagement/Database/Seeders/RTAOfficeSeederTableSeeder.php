<?php

namespace Modules\VehicleManagement\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class RTAOfficeSeederTableSeeder extends Seeder
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
            'Uttara BRTA Office',
            'Mirpur BRTA Office',
            'Mohakhali BRTA Office',
            'Gulshan BRTA Office',
            'Dhanmondi BRTA Office',
            'Jatrabari BRTA Office',
            'Motijheel BRTA Office',
            'Keraniganj BRTA Office',
            'Tongi BRTA Office',
            'Gazipur BRTA Office',
            'Narayanganj BRTA Office',
            'Savar BRTA Office',
            'Demra BRTA Office',
            'Nawabganj BRTA Office',
            'Sutrapur BRTA Office',
        ];

        foreach ($data as $key => $value) {
            \Modules\VehicleManagement\Entities\RTAOffice::create([
                'name' => $value,
                'is_active' => true,
            ]);
        }
    }
}
