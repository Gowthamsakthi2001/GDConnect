<?php

namespace Modules\VehicleMaintenance\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleMaintenanceDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\VehicleMaintenance\Entities\VehicleMaintenanceDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $qty = $this->faker->numberBetween(100, 1000);
        $price = $this->faker->numberBetween(100, 1000);
        $total = $qty * $price;

        return [
            'maintenance_id' => $this->faker->numberBetween(1, 10),
            'category_id' => $this->faker->numberBetween(1, 10),
            'parts_id' => $this->faker->numberBetween(1, 10),
            'qty' => $qty,
            'price' => $price,
            'total' => $total,
        ];
    }
}
