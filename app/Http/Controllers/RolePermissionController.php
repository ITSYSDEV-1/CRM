<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class RolePermissionController extends Controller
{
    /**
     * Display the role-permission management page
     */
    public function index()
    {
        // Get all roles
        $roles = Role::all();
        
        // Get permission structure from JSON
        $permissionStructure = $this->getPermissionStructure();
        
        // Get configuration for page
        $configuration = \App\Models\Configuration::first();
        
        return view('admin.role-permissions.index', compact('roles', 'permissionStructure', 'configuration'));
    }
    
    /**
     * Get the permission structure from JSON file
     */
    private function getPermissionStructure()
    {
        $json = File::get(database_path('seeders/json/permissions.json'));
        return json_decode($json);
    }
    
    /**
     * Show the edit page for a specific role
     */
    public function edit($id)
    {
        // Get the role
        $role = Role::findOrFail($id);
        
        // Get permission structure from JSON
        $permissionStructure = $this->getPermissionStructure();
        
        // Get all permissions assigned to this role
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        // Get configuration for page
        $configuration = \App\Models\Configuration::first();
        
        return view('admin.role-permissions.edit', compact('role', 'permissionStructure', 'rolePermissions', 'configuration'));
    }
    
    /**
     * Update the permissions for a role
     */
    public function update(Request $request, $id)
    {
        // Validate request
        $request->validate([
            'permissions' => 'array',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Get the role
            $role = Role::findOrFail($id);
            
            // Get the permissions from the request
            $permissions = $request->input('permissions', []);
            
            // Sync the permissions
            $role->syncPermissions($permissions);
            
            DB::commit();
            
            return redirect()->route('role-permissions.edit', $id)
                ->with('success', 'Permissions updated successfully for role: ' . $role->name);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating role permissions: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error updating permissions: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Get users assigned to a specific role
     */
    public function getRoleUsers($id)
    {
        $role = Role::findOrFail($id);
        $users = User::role($role->name)->get();
        
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }
    
    /**
     * Show the assign users page for a specific role
     */
    public function assignUsers($id)
    {
        $role = Role::findOrFail($id);
        
        // Get users who don't have any roles
        $availableUsers = User::whereDoesntHave('roles')->get();
        
        // Get users already assigned to this role
        $assignedUsers = User::role($role->name)->get();
        
        $configuration = \App\Models\Configuration::first();
        
        return view('admin.role-permissions.assign-users', compact('role', 'assignedUsers', 'availableUsers', 'configuration'));
    }
    
    /**
     * Assign users to a role
     */
    public function storeUserAssignments(Request $request, $id)
    {
        $request->validate([
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id'
        ]);
        
        try {
            DB::beginTransaction();
            
            $role = Role::findOrFail($id);
            $userIds = $request->input('user_ids', []);
            
            // Get users to assign
            $users = User::whereIn('id', $userIds)->get();
            
            // Assign role to users
            foreach ($users as $user) {
                $user->assignRole($role->name);
            }
            
            DB::commit();
            
            return redirect()->route('role-permissions.assign-users', $id)
                ->with('success', 'Users assigned to role successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning users to role: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error assigning users: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove user from role
     */
    public function removeUserFromRole(Request $request, $roleId, $userId)
    {
        try {
            DB::beginTransaction();
            
            $role = Role::findOrFail($roleId);
            $user = User::findOrFail($userId);
            
            // Remove role from user
            $user->removeRole($role->name);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'User removed from role successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing user from role: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error removing user from role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the create user form
     */
    public function createUser()
    {
        // Get configuration for page
        $configuration = \App\Models\Configuration::first();
        
        return view('admin.role-permissions.create-user', compact('configuration'));
    }

    /**
     * Store a new user
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Create new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            
            DB::commit();
            
            return redirect()->route('role-permissions.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error creating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete a role
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            // Get the role
            $role = Role::findOrFail($id);
            
            // Check if role has users
            $userCount = User::role($role->name)->count();
            if ($userCount > 0) {
                return redirect()->route('role-permissions.index')
                    ->with('error', 'Cannot delete role: ' . $role->name . '. It has ' . $userCount . ' users assigned.');
            }
            
            // Delete the role
            $role->delete();
            
            DB::commit();
            
            return redirect()->route('role-permissions.index')
                ->with('success', 'Role deleted successfully: ' . $role->name);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting role: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error deleting role: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the create role form
     */
    public function create()
    {
        // Get configuration for page
        $configuration = \App\Models\Configuration::first();
        
        return view('admin.role-permissions.create', compact('configuration'));
    }
    
    /**
     * Store a new role
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Create new role with no permissions
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);
            
            DB::commit();
            
            return redirect()->route('role-permissions.edit', $role->id)
                ->with('success', 'Role created successfully. You can now assign permissions.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating role: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error creating role: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display a list of all users for management
     */
    public function manageUsers()
    {
        // Get all users
        $users = User::all();
        
        // Get configuration for page
        $configuration = \App\Models\Configuration::first();
        
        return view('admin.role-permissions.manage-users', compact('users', 'configuration'));
    }
    
    /**
     * Show the edit user form
     */
    public function editUser($id)
    {
        // Get the user
        $user = User::findOrFail($id);
        
        // Get configuration for page
        $configuration = \App\Models\Configuration::first();
        
        return view('admin.role-permissions.edit-user', compact('user', 'configuration'));
    }
    
    /**
     * Update user information
     */
    public function updateUser(Request $request, $id)
    {
        // Get the user
        $user = User::findOrFail($id);
        
        // Validate request
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        ];
        
        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }
        
        $request->validate($rules);
        
        try {
            DB::beginTransaction();
            
            // Update user information
            $user->name = $request->name;
            $user->email = $request->email;
            
            // Update password if provided
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }
            
            $user->save();
            
            DB::commit();
            
            return redirect()->route('role-permissions.manage-users')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error updating user: ' . $e->getMessage())
                ->withInput();
        }
    }
}