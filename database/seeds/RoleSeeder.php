<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      
        DB::table('roles')->insert([
            [
                'name' => 'ceo',
                'scopes' => json_encode(['manage-users', 'manage-tasks', 'view-tasks']) 
            ],
            [
                'name' => 'manager',
                'scopes' => json_encode(['manage-tasks', 'view-tasks'])
            ],
            [
                'name' => 'adviser',
                'scopes' => json_encode(['view-tasks'])
            ],
        ]);
    }
}


