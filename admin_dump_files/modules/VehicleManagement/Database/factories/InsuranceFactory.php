<?php

namespace Modules\VehicleManagement\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InsuranceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\VehicleManagement\Entities\Insurance::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'company_id' => $this->faker->numberBetween(1, 10),
            'vehicle_id' => $this->faker->numberBetween(1, 10),
            'policy_number' => $this->faker->word,
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'charge_payable' => $this->faker->randomFloat(2, 0, 100),
            'deductible' => $this->faker->randomFloat(2, 0, 100),
            'recurring_date' => $this->faker->date(),
            'recurring_period_id' => $this->faker->numberBetween(1, 10),
            'status' => $this->faker->boolean,
            'add_reminder' => $this->faker->boolean,
            'remarks' => $this->faker->text,
        ];
    }
}
