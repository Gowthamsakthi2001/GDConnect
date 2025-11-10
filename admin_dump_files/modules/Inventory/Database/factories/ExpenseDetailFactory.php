<?php

namespace Modules\Inventory\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Inventory\Entities\ExpenseDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $qty = $this->faker->numberBetween(10, 2000);
        $price = $this->faker->randomFloat(2, 100, 1000);
        $total = $qty * $price;

        return [
            'expense_id' => $this->faker->numberBetween(1, 10),
            'type_id' => $this->faker->numberBetween(1, 10),
            'qty' => $qty,
            'price' => $price,
            'total' => $total,
        ];
    }
}
