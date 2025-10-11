<?php

namespace Modules\VehicleRefueling\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FuelRequisitionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\VehicleRefueling\Entities\FuelRequisition::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vehicle_id' => $this->faker->numberBetween(1, 10),
            'station_id' => $this->faker->numberBetween(1, 10),
            'type_id' => $this->faker->numberBetween(1, 10),
            'qty' => $this->faker->randomFloat(2, 0, 1000),
            'current_qty' => $this->faker->randomFloat(2, 0, 1000),
            'date' => $this->faker->dateTimeBetween('-12 months', 'now'),
            'status' => $this->faker->randomElement(array_keys($this->model::getStatues())),
        ];
    }
}
