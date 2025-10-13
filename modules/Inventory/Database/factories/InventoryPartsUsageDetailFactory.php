<?php

namespace Modules\Inventory\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryPartsUsageDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Inventory\Entities\InventoryPartsUsageDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'parts_usage_id' => $this->faker->numberBetween(1, 10),
            'category_id' => $this->faker->numberBetween(1, 10),
            'parts_id' => $this->faker->numberBetween(1, 10),
            'qty' => $this->faker->numberBetween(100, 1000),
        ];
    }
}
