<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PreferencesPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                "id" => "1.1.1_view_preferences",
                "permission_number" => "1.1.1",
                "name" => "View Preferences",
                "action" => "view"
            ],
            [
                "id" => "1.1.2_edit_preferences",
                "permission_number" => "1.1.2",
                "name" => "Edit Preferences",
                "action" => "edit"
            ]
        ];

        foreach ($permissions as $permissionData) {
            $permissionName = $permissionData['id'];
            
            Permission::create([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
            
            $this->command->info("Created preference permission: {$permissionData['name']}");
        }
    }
}
