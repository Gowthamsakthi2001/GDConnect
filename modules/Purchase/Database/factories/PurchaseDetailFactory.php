<?php

namespace Modules\Purchase\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Purchase\Entities\PurchaseDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $qty = $this->faker->numberBetween(1000, 10000);
        $price = $this->faker->numberBetween(1000, 10000);
        $total = $qty * $price;

        return [
            'purchase_id' => $this->faker->numberBetween(1, 10),
            'category_id' => $this->faker->numberBetween(1, 10),
            'parts_id' => $this->faker->numberBetween(1, 10),
            'qty' => $qty,
            'price' => $price,
            'total' => $total,
        ];
    }
}
