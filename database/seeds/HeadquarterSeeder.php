<?php

use Illuminate\Database\Seeder;
use App\Models\Headquarter;

class HeadquarterSeeder extends Seeder
{
    public function run()
    {
        Headquarter::insert([
            ['name' => 'Piedecuesta','manager_id' => 2],
            ['name' => 'Florida','manager_id' => 3],
            ['name' => 'Ciudadela','manager_id' => 4],
        ]);
    }
}


