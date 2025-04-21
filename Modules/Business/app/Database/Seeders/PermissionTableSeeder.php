<?php

namespace Modules\Business\App\Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Modules\Business\App\Models\Business;

class PermissionTableSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();
        Artisan::call('cache:clear');
        $module = 'Business';

        // Define module permissions
        $permissions = [
            [
                'name' => 'business.view',
                'display_name' => 'View Business',
                'description' => 'Can view Business module',
            ],
            [
                'name' => 'business.create',
                'display_name' => 'Create Business',
                'description' => 'Can create in Business module',
            ],
            [
                'name' => 'business.edit',
                'display_name' => 'Edit Business',
                'description' => 'Can edit in Business module',
            ],
            [
                'name' => 'business.delete',
                'display_name' => 'Delete Business',
                'description' => 'Can delete in Business module',
            ],
            [
                'name' => 'business.manage',
                'display_name' => 'Manage Business',
                'description' => 'Can manage Business settings, add users, and set default business',
            ],
        ];

        $superadminRole = Role::where('name', 'superadmin')->first();

        $adminRoles = Role::where('name', 'admin')->get();

        // Get all businesses
        $businesses = Business::all();

        // Create system-wide permissions for the superadmin
        foreach ($permissions as $permissionData) {
            // Check if the permission already exists
            $permissionExists = Permission::where('name', $permissionData['name'])
                ->whereNull('business_id')
                ->exists();

            if (! $permissionExists) {
                // Create system-wide permission (null business_id)
                $permission = Permission::create([
                    'name' => $permissionData['name'],
                    'display_name' => $permissionData['display_name'],
                    'description' => $permissionData['description'],
                    'business_id' => null, // System-wide permission
                    'module' => $module,
                ]);

                // Attach permission to superadmin role
                if ($superadminRole) {
                    $superadminRole->givePermissions([$permission]);
                }
            }
        }

        // Create business-specific permissions for each business
        foreach ($businesses as $business) {
            foreach ($permissions as $permissionData) {
                // Check if the permission already exists for this business
                $permissionExists = Permission::where('name', $permissionData['name'])
                    ->where('business_id', $business->id)
                    ->exists();

                if (! $permissionExists) {
                    // Create business-specific permission
                    $permission = Permission::create([
                        'name' => $permissionData['name'],
                        'display_name' => $permissionData['display_name'],
                        'description' => $permissionData['description'],
                        'business_id' => $business->id,
                        'module' => $module,
                    ]);

                    // Find the admin role for this business
                    $businessAdminRole = $adminRoles->where('business_id', $business->id)->first();

                    // Attach permission to business admin role
                    if ($businessAdminRole) {
                        $businessAdminRole->givePermissions([$permission]);
                    }
                }
            }
        }
    }
}
