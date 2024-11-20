<?php

namespace Database\Factories;

use App\Models\AppUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppUserFactory extends Factory
{
    protected $model = AppUser::class;

    public function definition()
    {
        return [
            // Define tus campos aquí
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            // Agrega otros campos según sea necesario
        ];
    }
}

