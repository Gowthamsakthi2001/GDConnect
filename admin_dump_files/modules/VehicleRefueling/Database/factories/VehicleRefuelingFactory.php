<?php

namespace Modules\VehicleRefueling\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleRefuelingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\VehicleRefueling\Entities\VehicleRefueling::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vehicle_id' => $this->faker->numberBetween(1, 10),
            'driver_id' => $this->faker->numberBetween(1, 10),
            'fuel_type_id' => $this->faker->numberBetween(1, 10),
            'fuel_station_id' => $this->faker->numberBetween(1, 10),
            'refueled_at' => $this->faker->dateTime(),
            'place' => $this->faker->city,
            'budget' => $this->faker->randomFloat(2, 0, 1000),
            'km_per_unit' => $this->faker->randomFloat(2, 0, 1000),
            'last_reading' => $this->faker->randomFloat(2, 0, 1000),
            'last_unit' => $this->faker->randomFloat(2, 0, 1000),
            'refuel_limit' => $this->faker->randomFloat(2, 0, 1000),
            'max_unit' => $this->faker->randomFloat(2, 0, 1000),
            'unit_taken' => $this->faker->randomFloat(2, 0, 1000),
            'odometer_day_end' => $this->faker->randomNumber(5),
            'odometer_refuel_time' => $this->faker->randomNumber(5),
            'consumption_percent' => $this->faker->randomNumber(2),
            'slip_path' => $this->faker->imageUrl(),
            'strict_policy' => $this->faker->boolean(),
        ];
    }
}
