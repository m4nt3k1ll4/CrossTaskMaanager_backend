<?php

use Illuminate\Database\Seeder;
use App\Models\Headquarter;

class HeadquarterSeeder extends Seeder
{
    public function run()
    {
        $headquarters = [
            ['id' => 1, 'name' => 'Piedecuesta'],
            ['id' => 2, 'name' => 'Floridablanca'],
            ['id' => 3, 'name' => 'Ciudadela'],
        ];

        foreach ($headquarters as $headquarter) {
            Headquarter::create($headquarter);
        }
    }
}
