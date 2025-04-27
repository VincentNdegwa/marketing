<?php

namespace Modules\CMS\App\Database\Seeders;

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
        $module = 'CMS';

        Log::info('Starting CMS permission seeding');

        // Define module permissions
        $permissions = [
            // General CMS permissions
            [
                'name' => 'cms.view',
                'display_name' => 'Access CMS',
                'description' => 'Can view the CMS module'
            ],
            
            // Pages permissions
            [
                'name' => 'cms.pages.view',
                'display_name' => 'View Pages',
                'description' => 'Can view CMS pages'
            ],
            [
                'name' => 'cms.pages.create',
                'display_name' => 'Create Pages',
                'description' => 'Can create new CMS pages'
            ],
            [
                'name' => 'cms.pages.edit',
                'display_name' => 'Edit Pages',
                'description' => 'Can edit existing CMS pages'
            ],
            [
                'name' => 'cms.pages.delete',
                'display_name' => 'Delete Pages',
                'description' => 'Can delete CMS pages'
            ],
            
            // Templates permissions
            [
                'name' => 'cms.templates.view',
                'display_name' => 'View Templates',
                'description' => 'Can view page templates'
            ],
            [
                'name' => 'cms.templates.create',
                'display_name' => 'Create Templates',
                'description' => 'Can create new page templates'
            ],
            [
                'name' => 'cms.templates.edit',
                'display_name' => 'Edit Templates',
                'description' => 'Can edit existing page templates'
            ],
            [
                'name' => 'cms.templates.delete',
                'display_name' => 'Delete Templates',
                'description' => 'Can delete page templates'
            ],
            
            // Media permissions
            [
                'name' => 'cms.media.view',
                'display_name' => 'View Media',
                'description' => 'Can view media library'
            ],
            [
                'name' => 'cms.media.upload',
                'display_name' => 'Upload Media',
                'description' => 'Can upload files to media library'
            ],
            [
                'name' => 'cms.media.delete',
                'display_name' => 'Delete Media',
                'description' => 'Can delete files from media library'
            ],
            
            // Settings permissions
            [
                'name' => 'cms.settings.view',
                'display_name' => 'View Settings',
                'description' => 'Can view CMS settings'
            ],
            [
                'name' => 'cms.settings.edit',
                'display_name' => 'Edit Settings',
                'description' => 'Can edit CMS settings'
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
