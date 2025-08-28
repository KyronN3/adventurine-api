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
        $designations = ['IT Department', 'HR Department', 'Marketing Department', 'Finance Department', 'Operations Department', 'Admin Department'];
        $statuses = ['Permanent', 'Coterminous', 'Temporary', 'Casual', 'Job Order', 'Honorarium'];
        $sexes = ['M', 'F'];
        $medicalHistories = ['NONE', 'Hypertension', 'Diabetes', 'Asthma', 'Allergies', 'Heart Condition'];

        return [
            // why no table for the employee? then I make one then - velvet underground 🍌
            'employee_name' => $this->faker->name(),
            'designation' => $this->faker->randomElement($designations),
            'sex' => $this->faker->randomElement($sexes),
            'medical_history' => $this->faker->randomElement($medicalHistories),
            'status' => $this->faker->randomElement($statuses),
            'bpm_systolic' => $this->faker->numberBetween(90, 140),
            'bpm_diastolic' => $this->faker->numberBetween(60, 90),
            'bpm_dateTaken' => $this->faker->date(),
        ];
    }
}