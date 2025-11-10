<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Inventory\Entities\Vendor;

class VendorSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vendors = [
            'Rahim Motors',
            'Karim Cars',
            'Tariq Traders',
            'Ali Traders',
            'Bilal Gears & Co',
            'Saeed Brothers',
        ];

        foreach ($vendors as $vendor) {
            Vendor::create([
                'name' => $vendor,
                'email' => Str::slug($vendor, '.').'@gmail.com',
                'phone' => '03'.rand(10000000, 99999999),
                'address' => 'Street '.rand(1, 100).', House '.rand(1, 100).',Dhaka, Bangladesh',
            ]);
        }
    }
}
