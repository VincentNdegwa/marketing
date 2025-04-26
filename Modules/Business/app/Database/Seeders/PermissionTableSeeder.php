<?php

namespace Modules\Business\App\Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Modules\Business\App\Models\Business;

class PermissionTableSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();
        Artisan::call('cache:clear');
        $module = 'Business';

        Log::info('Starting Business permission seeding');

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
        Log::info('SuperAdmin role found: '.($superadminRole ? 'Yes' : 'No'));

        $adminRoles = Role::where('name', 'admin')->get();
        Log::info('Admin roles found: '.$adminRoles->count());

        $businesses = Business::all();
        Log::info('Businesses found: '.$businesses->count());

        $all_permissions = [];
        foreach ($permissions as $permissionData) {
            $permissionExists = Permission::where('name', $permissionData['name'])
                ->whereNull('business_id')
                ->exists();

            if (! $permissionExists) {
                try {
                    $permission = Permission::create([
                        'name' => $permissionData['name'],
                        'display_name' => $permissionData['display_name'],
                        'description' => $permissionData['description'],
                        'business_id' => null,
                        'module' => $module,
                    ]);
                    $all_permissions[] = $permission;
                    Log::info('Created system permission: '.$permissionData['name']);
                } catch (\Exception $e) {
                    Log::error('Failed to create system permission: '.$e->getMessage());
                }

                if ($superadminRole && isset($permission)) {
                    try {
                        $superadminRole->givePermissions([$permission]);
                        Log::info('Attached permission to superadmin role: '.$permissionData['name']);
                    } catch (\Exception $e) {
                        Log::error('Failed to attach permission to superadmin: '.$e->getMessage());
                    }
                }
            }
        }

        // Assign business admins roles
        foreach ($businesses as $business) {
            $businessAdminRole = $adminRoles->where('business_id', $business->id)->first();

            if ($businessAdminRole && isset($all_permissions)) {
                try {
                    $businessAdminRole->givePermissions($all_permissions);
                } catch (\Exception $e) {
                    Log::error('Failed to attach permission to business admin: '.$e->getMessage());
                }
            }
        }
    }
}
