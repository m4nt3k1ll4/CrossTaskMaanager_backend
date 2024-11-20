z<?php

use Illuminate\Database\Seeder;
use App\Models\AppUser;

class AppUserSeeder extends Seeder
{
    public function run()
    {
          AppUser::create([
            'name' => 'CEO',
            'email' => 'ceo@example.com',
            'password' => bcrypt('12345678'),
            'role_id' => 1,
        ]);


        $headquarters = ['Piedecuesta', 'Floridablanca', 'Ciudadela'];
        foreach ($headquarters as $headquarter) {
            AppUser::create([
                'name' => "Manager $headquarter",
                'email' => strtolower("$headquarter.manager@example.com"),
                'password' => bcrypt('password'),
                'role_id' => 2,
            ]);
        }


        foreach ($headquarters as $headquarter) {
            $horarios = ["manana", "tarde"];


            foreach ($horarios as $horario) {
                AppUser::create([
                    'name' => "Asesor $horario $headquarter",
                    'email' => strtolower("asesor.$horario.$headquarter@example.com"),
                    'password' => bcrypt('password'),
                    'role_id' => 3,
                ]);
            }
        }
    }
}

