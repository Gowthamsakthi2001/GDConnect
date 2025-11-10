<?php

namespace Modules\Inventory\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Inventory\Entities\Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(array_keys($this->model::getTypes())),
            'employee_id' => $this->faker->numberBetween(1, 10),
            'vendor_id' => $this->faker->numberBetween(1, 10),
            'vehicle_id' => $this->faker->numberBetween(1, 10),
            'trip_type_id' => $this->faker->numberBetween(1, 10),
            'trip_number' => $this->faker->numberBetween(1, 10),
            'odometer_millage' => $this->faker->numberBetween(1, 10),
            'vehicle_rent' => $this->faker->randomFloat(2, 100, 1000),
            'total' => $this->faker->randomFloat(2, 100, 1000),
            'date' => $this->faker->dateTimeBetween('-12 months', 'now'),
            'remarks' => $this->faker->sentence,
            'status' => $this->faker->randomElement(array_keys($this->model::getStatues())),
        ];
    }
}
