<?php

namespace Modules\VehicleMaintenance\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleMaintenanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\VehicleMaintenance\Entities\VehicleMaintenance::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'employee_id' => $this->faker->numberBetween(1, 10),
            'vehicle_id' => $this->faker->numberBetween(1, 10),
            'maintenance_type_id' => $this->faker->numberBetween(1, 10),
            'title' => $this->faker->sentence,
            //last 12 months date
            'date' => $this->faker->dateTimeBetween('-12 months', 'now'),
            'remarks' => $this->faker->sentence,
            'charge_bear_by' => $this->faker->randomElement(['company', 'employee']),
            'charge' => $this->faker->randomFloat(2, 100, 1000),
            'total' => $this->faker->randomFloat(2, 100, 1000),
            'type' => $this->faker->randomElement(array_keys($this->model::getTypes())),
            'priority' => $this->faker->randomElement(array_keys($this->model::getPriorities())),
            'status' => $this->faker->randomElement(array_keys($this->model::getStatues())),
        ];
    }
}
