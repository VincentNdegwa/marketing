<?php

namespace Modules\UserManagement\App\Database\Seeders;

use App\Models\Business;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class PermissionTableSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();
        Artisan::call('cache:clear');
        $module = 'UserManagement';

        Log::info('Starting UserManagement permission seeding');

        $permissions = [
            [
                'name' => 'usermanagement.view',
                'display_name' => 'View Users',
                'description' => 'Can view users'
            ],
            [
                'name' => 'usermanagement.create',
                'display_name' => 'Create Users',
                'description' => 'Can create new users'
            ],
            [
                'name' => 'usermanagement.edit',
                'display_name' => 'Edit Users',
                'description' => 'Can edit existing users'
            ],
            [
                'name' => 'usermanagement.delete',
                'display_name' => 'Delete Users',
                'description' => 'Can delete users'
            ],
            [
                'name' => 'role.view',
                'display_name' => 'View Roles',
                'description' => 'Can view roles'
            ],
            [
                'name' => 'role.create',
                'display_name' => 'Create Roles',
                'description' => 'Can create new roles'
            ],
            [
                'name' => 'role.edit',
                'display_name' => 'Edit Roles',
                'description' => 'Can edit existing roles'
            ],
            [
                'name' => 'role.delete',
                'display_name' => 'Delete Roles',
                'description' => 'Can delete roles'
            ],
            [
                'name' => 'permission.manage',
                'display_name' => 'Manage Permissions',
                'description' => 'Access to permission management'
            ],
            [
                'name' => 'permission.assign',
                'display_name' => 'Assign Permissions',
                'description' => 'Can assign permissions to roles'
            ]
        ];

        $superadminRole = Role::where('name', 'superadmin')->first();
        Log::info('SuperAdmin role found: ' . ($superadminRole ? 'Yes' : 'No'));

        $adminRoles = Role::where('name', 'admin')->get();
        Log::info('Admin roles found: ' . $adminRoles->count());

        $businesses = Business::all();
        Log::info('Businesses found: ' . $businesses->count());
        foreach ($permissions as $permissionData) {
            $permissionExists = Permission::where('name', $permissionData['name'])
                ->whereNull('business_id')
                ->exists();

            if (!$permissionExists) {
                try {
                    $permission = Permission::create([
                        'name' => $permissionData['name'],
                        'display_name' => $permissionData['display_name'],
                        'description' => $permissionData['description'],
                        'business_id' => null,
                        'module' => $module
                    ]);
                    Log::info('Created system permission: ' . $permissionData['name']);
                } catch (\Exception $e) {
                    Log::error('Failed to create system permission: ' . $e->getMessage());
                }

                if ($superadminRole && isset($permission)) {
                    try {
                        $superadminRole->givePermissions([$permission]);
                        Log::info('Attached permission to superadmin role: ' . $permissionData['name']);
                    } catch (\Exception $e) {
                        Log::error('Failed to attach permission to superadmin: ' . $e->getMessage());
                    }
                }
            }
        }

        foreach ($businesses as $business) {
            foreach ($permissions as $permissionData) {
                $permissionExists = Permission::where('name', $permissionData['name'])
                    ->where('business_id', $business->id)
                    ->exists();

                if (!$permissionExists) {
                    try {
                        $permission = Permission::create([
                            'name' => $permissionData['name'],
                            'display_name' => $permissionData['display_name'],
                            'description' => $permissionData['description'],
                            'business_id' => $business->id,
                            'module' => $module
                        ]);
                        Log::info('Created business permission: ' . $permissionData['name'] . ' for business ID: ' . $business->id);
                    } catch (\Exception $e) {
                        Log::error('Failed to create business permission: ' . $e->getMessage());
                    }

                    $businessAdminRole = $adminRoles->where('business_id', $business->id)->first();

                    if ($businessAdminRole && isset($permission)) {
                        try {
                            $businessAdminRole->givePermissions([$permission]);
                            Log::info('Attached permission to business admin role: ' . $permissionData['name'] . ' for business ID: ' . $business->id);
                        } catch (\Exception $e) {
                            Log::error('Failed to attach permission to business admin: ' . $e->getMessage());
                        }
                    }
                }
            }
        }
    }
}
