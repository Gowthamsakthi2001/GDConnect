<?php

namespace Modules\VehicleManagement\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LegalDocumentationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\VehicleManagement\Entities\LegalDocumentation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'document_type_id' => $this->faker->randomDigitNotNull,
            'vehicle_id' => $this->faker->randomDigitNotNull,
            'issue_date' => $this->faker->date(),
            'expiry_date' => $this->faker->date(),
            'charge_paid' => $this->faker->randomFloat(2, 0, 999999),
            'vendor_id' => $this->faker->randomDigitNotNull,
            'commission' => $this->faker->randomFloat(2, 0, 999999),
            'notify_before' => null,
            'email' => $this->faker->email,
            'document_file_path' => null,
        ];

    }
}
