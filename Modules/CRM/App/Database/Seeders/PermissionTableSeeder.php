<?php

namespace Modules\CRM\App\Database\Seeders;

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
        $module = 'CRM';

        Log::info('Starting CRM permission seeding');

        // Define module permissions
        $permissions = [
            // Example permission format - add your module permissions here
            /*
            [
                'name' => 'crm.view',
                'display_name' => 'View CRM',
                'description' => 'Can view CRM module'
            ],
            [
                'name' => 'crm.create',
                'display_name' => 'Create CRM',
                'description' => 'Can create in CRM module'
            ],
            [
                'name' => 'crm.edit',
                'display_name' => 'Edit CRM',
                'description' => 'Can edit in CRM module'
            ],
            [
                'name' => 'crm.delete',
                'display_name' => 'Delete CRM',
                'description' => 'Can delete in CRM module'
            ]
            */
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
                        if(!$superadminRole->hasPermission($permission['name'])) {
                            $superadminRole->givePermissions([$permission]);
                            Log::info('Attached permission to superadmin role: '.$permissionData['name']);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to attach permission to superadmin: '.$e->getMessage());
                    }
                }

                foreach ($adminRoles as $adminRole) {
                    try {
                        if(!$adminRole->hasPermission($permission['name'])) {
                            $adminRole->givePermissions([$permission]);
                            Log::info('Attached permission to admin role: '.$permissionData['name']);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to attach permission to admin: '.$e->getMessage());
                    }
                }
            }
        }
        
        //Assign business admins roles
        foreach ($businesses as $business) {
            $businessAdminRole = $adminRoles->where('business_id', $business->id)->first();

            if ($businessAdminRole && isset($all_permissions)) {
                try {
                    $businessAdminRole->givePermissions($all_permissions);
                } catch (\Exception $e) {
                    Log::error('Failed to attach permission to business admin: ' . $e->getMessage());
                }
            }
        }
    }
}
