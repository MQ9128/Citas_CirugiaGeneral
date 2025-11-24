<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DoctorFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->name();
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'specialty' => 'Cirugía General',
            'bio' => $this->faker->paragraph(3),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'is_active' => true,
        ];
    }
}
