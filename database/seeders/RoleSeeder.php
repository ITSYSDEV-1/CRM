<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\File;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Path to the JSON file
        $json = File::get(database_path('seeders/json/roles.json'));
        $data = json_decode($json);

        // Create roles from JSON data
        foreach ($data->roles as $roleData) {
            Role::create([
                'name' => $roleData->code,
                'guard_name' => 'web',
            ]);
            
            $this->command->info("Created role: {$roleData->name}");
        }
    }
}
