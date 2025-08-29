<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bpm>
 */
class BpmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $medicalHistories = ['NONE', 'Hypertension', 'Diabetes', 'Asthma', 'Allergies', 'Heart Condition'];

        return [
            // Use control_no to reference vwActive instead of storing employee data
            'control_no' => $this->faker->unique()->numerify('0#####'),
            'medical_history' => $this->faker->randomElement($medicalHistories),
            'bpm_systolic' => $this->faker->numberBetween(90, 140),
            'bpm_diastolic' => $this->faker->numberBetween(60, 90),
            'bpm_dateTaken' => $this->faker->date(),
        ];
    }
}