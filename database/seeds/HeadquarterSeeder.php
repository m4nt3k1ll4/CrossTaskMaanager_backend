<?php

use Illuminate\Database\Seeder;
use App\Models\Headquarter;

class HeadquarterSeeder extends Seeder
{
    public function run()
    {
        Headquarter::insert([
            ['name' => 'Piedecuesta', 'ceo_id' => 1, 'manager_id' => 2],
            ['name' => 'Florida', 'ceo_id' => 1, 'manager_id' => 3],
            ['name' => 'Ciudadela', 'ceo_id' => 1, 'manager_id' => 4],
        ]);
    }
}


