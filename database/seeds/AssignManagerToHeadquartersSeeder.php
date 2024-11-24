<?php

use Illuminate\Database\Seeder;
use App\Models\Headquarter;

class AssignManagerToHeadquartersSeeder extends Seeder
{
    public function run()
    {
        // Asignamos managers a cada sede por su ID
        $headquarters = [
            ['headquarter_id' => 1, 'manager_id' => 2], // Piedecuesta -> Manager con id 2
            ['headquarter_id' => 2, 'manager_id' => 3], // Floridablanca -> Manager con id 3
            ['headquarter_id' => 3, 'manager_id' => 4], // Ciudadela -> Manager con id 4
        ];

        // Actualizamos las sedes asignando los managers correspondientes
        foreach ($headquarters as $assignment) {
            $headquarter = Headquarter::find($assignment['headquarter_id']);
            if ($headquarter) {
                $headquarter->manager_id = $assignment['manager_id'];
                $headquarter->save();
            }
        }
    }
}
