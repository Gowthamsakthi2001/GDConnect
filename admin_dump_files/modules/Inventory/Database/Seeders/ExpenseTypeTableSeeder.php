<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class ExpenseTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $expenseTypes = [
            ['name' => 'Transport', 'is_active' => true],
            ['name' => 'Food', 'is_active' => true],
            ['name' => 'Accommodation', 'is_active' => true],
            ['name' => 'Miscellaneous', 'is_active' => true],
        ];

        foreach ($expenseTypes as $expenseType) {
            \Modules\Inventory\Entities\ExpenseType::create($expenseType);
        }

    }
}
