<?php

namespace Modules\Employee\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Employee\Entities\Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'payroll_type' => $this->faker->randomElement(['monthly', 'hourly']),
            'department_id' => $this->faker->numberBetween(1, 10),
            'position_id' => $this->faker->numberBetween(1, 10),
            'nid' => $this->faker->numberBetween(1000000000, 9999999999),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'email2' => $this->faker->email,
            'phone2' => $this->faker->phoneNumber,
            'join_date' => $this->faker->date(),
            'blood_group' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'dob' => $this->faker->date(),
            'working_slot_from' => $this->faker->time(),
            'working_slot_to' => $this->faker->time(),
            'father_name' => $this->faker->name,
            'mother_name' => $this->faker->name,
            'present_contact' => $this->faker->phoneNumber,
            'present_address' => $this->faker->address,
            'permanent_contact' => $this->faker->phoneNumber,
            'permanent_address' => $this->faker->address,
            'present_city' => $this->faker->city,
            'permanent_city' => $this->faker->city,
            'contact_person_name' => $this->faker->name,
            'contact_person_mobile' => $this->faker->phoneNumber,
            'reference_name' => $this->faker->name,
            'reference_mobile' => $this->faker->phoneNumber,
            'reference_email' => $this->faker->email,
            'reference_address' => $this->faker->address,
        ];
    }
}
