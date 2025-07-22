<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the Super Admin role
        $superAdminRole = Role::where('name', 'SUPER_ADMIN')->first();
        
        if (!$superAdminRole) {
            $this->command->error('SUPER_ADMIN role not found!');
            return;
        }
        
        // Get all permissions
        $permissions = Permission::all();
        
        // Assign all permissions to Super Admin role
        foreach ($permissions as $permission) {
            $superAdminRole->givePermissionTo($permission);
            $this->command->info("Assigned permission '{$permission->name}' to SUPER_ADMIN role");
        }
        
        $this->command->info('All permissions have been assigned to SUPER_ADMIN role');
    }
}
