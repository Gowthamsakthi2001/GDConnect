<?php

namespace Modules\Employee\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Employee\Entities\Driver::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'license_type_id' => $this->faker->numberBetween(1, 10),
            'license_num' => $this->faker->numberBetween(1000000000, 9999999999),
            'license_issue_date' => $this->faker->date(),
            'nid' => $this->faker->numberBetween(1000000000, 9999999999),
            'license_expiry_date' => $this->faker->date(),
            'authorization_code' => $this->faker->numberBetween(1000000000, 9999999999),
            'dob' => $this->faker->date(),
            'joining_date' => $this->faker->date(),
            'working_time_slot' => $this->faker->time(),
            'leave_status' => $this->faker->numberBetween(0, 1),
            'present_address' => $this->faker->address,
            'permanent_address' => $this->faker->address,
            'is_active' => $this->faker->numberBetween(0, 1),
        ];
    }
}
