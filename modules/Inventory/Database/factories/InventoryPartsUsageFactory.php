<?php

namespace Modules\Inventory\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryPartsUsageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Inventory\Entities\InventoryPartsUsage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'created_by' => $this->faker->numberBetween(1, 2),
            'vehicle_id' => $this->faker->numberBetween(1, 10),
            'date' => $this->faker->date(),
            'remarks' => $this->faker->text,
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
