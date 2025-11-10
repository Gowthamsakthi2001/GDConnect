<?php

namespace Modules\VehicleManagement\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\VehicleManagement\Entities\Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $vehicle_name = [
            'Toyota',
            'Suzuki',
            'Honda',
            'Hyundai',
            'Kia',
            'Mazda',
            'Nissan',
            'Chevrolet',
            'Ford',
            'Mercedes-Benz',
            'BMW',
            'Audi',
            'Volkswagen',
            'Volvo',
            'Porsche',
            'Jaguar',
            'Land Rover',
            'Lexus',
            'Infiniti',
            'Acura',
            'Cadillac',
            'Buick',
        ];

        return [
            'name' => $this->faker->randomElement($vehicle_name).'-'.$this->faker->numberBetween(10000, 99999),
            'department_id' => $this->faker->numberBetween(1, 10),
            'registration_date' => $this->faker->date(),
            'license_plate' => $this->faker->numberBetween(1000000000, 9999999999),
            'alert_cell_no' => $this->faker->phoneNumber,
            'alert_email' => $this->faker->email,
            'ownership_id' => $this->faker->numberBetween(1, 10),
            'vehicle_type_id' => $this->faker->numberBetween(1, 10),
            'vehicle_division_id' => $this->faker->numberBetween(1, 10),
            'rta_circle_office_id' => $this->faker->numberBetween(1, 10),
            'driver_id' => $this->faker->numberBetween(1, 10),
            'vendor_id' => $this->faker->numberBetween(1, 10),
            'seat_capacity' => $this->faker->numberBetween(20, 10),
        ];
    }
}
