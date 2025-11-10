<?php

namespace Modules\VehicleManagement\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleRequisitionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\VehicleManagement\Entities\VehicleRequisition::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'employee_id' => $this->faker->numberBetween(1, 10),
            'vehicle_type_id' => $this->faker->numberBetween(1, 10),
            'where_from' => $this->faker->word,
            'where_to' => $this->faker->word,
            'pickup' => $this->faker->word,
            // in the last 12 months
            'requisition_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'time_from' => $this->faker->time(),
            'time_to' => $this->faker->time(),
            'tolerance' => $this->faker->word,
            'number_of_passenger' => $this->faker->randomDigitNotNull,
            'driver_id' => $this->faker->numberBetween(1, 10),
            'purpose' => $this->faker->word,
            'details' => $this->faker->text,
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
