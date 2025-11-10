<?php

namespace Modules\Purchase\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Purchase\Entities\Purchase;
use Modules\Purchase\Entities\PurchaseDetail;

class PurchaseSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        Purchase::factory()->count(10)->create()->each(function ($purchase) {
            $purchase->details()->saveMany(PurchaseDetail::factory()->count(5)->make());
        });
    }
}
