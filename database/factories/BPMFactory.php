<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BPM>
 */
class BPMFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departments = ['IT Department', 'HR Department', 'Marketing', 'Finance', 'Operations'];
        
        return [
            // why no table for the employee? then I make one then - velvet underground 🍌
            'employee_name' => $this->faker->name(),
            'employee_department' => $this->faker->randomElement($departments),
            'bpm_systolic' => $this->faker->numberBetween(90, 140),
            'bpm_diastolic' => $this->faker->numberBetween(60, 90),
            'bpm_dateTaken' => $this->faker->date(),
        ];
    }
}
