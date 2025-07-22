<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Path to the JSON file
        $json = File::get(database_path('seeders/json/permissions.json'));
        $data = json_decode($json);

        // Create permissions from JSON data
        foreach ($data->permission_groups as $group) {
            foreach ($group->sub_groups as $subGroup) {
                if (!empty($subGroup->permissions)) {
                    foreach ($subGroup->permissions as $permissionData) {
                        $permissionName = $permissionData->id;
                        
                        Permission::create([
                            'name' => $permissionName,
                            'guard_name' => 'web',
                        ]);
                        
                        $this->command->info("Created permission: {$permissionData->name}");
                    }
                }
            }
        }
    }
}
