<?php

namespace Modules\Employee\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DriverPerformanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Employee\Entities\DriverPerformance::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'driver_id' => $this->faker->numberBetween(1, 10),
            'over_time_status' => $this->faker->numberBetween(0, 1),
            'salary_status' => $this->faker->numberBetween(0, 1),
            'ot_payment' => $this->faker->numberBetween(1000, 9999),
            'performance_bonus' => $this->faker->numberBetween(1000, 9999),
            'penalty_amount' => $this->faker->numberBetween(1000, 9999),
            'penalty_reason' => $this->faker->sentence,
            'penalty_date' => $this->faker->date(),
            'insert_date' => $this->faker->date(),
        ];
    }
}
