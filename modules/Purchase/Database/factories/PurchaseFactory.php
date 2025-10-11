<?php

namespace Modules\Purchase\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Purchase\Entities\Purchase::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vendor_id' => $this->faker->numberBetween(1, 6),
            'date' => $this->faker->date(),
            'total' => $this->faker->numberBetween(1, 10),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
