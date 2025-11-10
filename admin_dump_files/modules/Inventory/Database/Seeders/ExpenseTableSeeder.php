<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Inventory\Entities\Expense;
use Modules\Inventory\Entities\ExpenseDetail;

class ExpenseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Expense::factory(1000)->create()->each(function ($expense) {
            $expense->details()->saveMany(ExpenseDetail::factory(5)->make());
        });
    }
}
