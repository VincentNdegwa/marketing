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
            // Module level permissions
            [
                'name' => 'crm.manage',
                'display_name' => 'Manage CRM',
                'description' => 'Can manage CRM module'
            ],
            [
                'name' => 'crm.view',
                'display_name' => 'View CRM Dashboard',
                'description' => 'Can view CRM dashboard and reports'
            ],
            
            // Contacts permissions
            [
                'name' => 'crm.contacts.view',
                'display_name' => 'View Contacts',
                'description' => 'Can view contacts in CRM'
            ],
            [
                'name' => 'crm.contacts.create',
                'display_name' => 'Create Contacts',
                'description' => 'Can create new contacts in CRM'
            ],
            [
                'name' => 'crm.contacts.edit',
                'display_name' => 'Edit Contacts',
                'description' => 'Can edit existing contacts in CRM'
            ],
            [
                'name' => 'crm.contacts.delete',
                'display_name' => 'Delete Contacts',
                'description' => 'Can delete contacts in CRM'
            ],
            
            // Companies permissions
            [
                'name' => 'crm.companies.view',
                'display_name' => 'View Companies',
                'description' => 'Can view companies in CRM'
            ],
            [
                'name' => 'crm.companies.create',
                'display_name' => 'Create Companies',
                'description' => 'Can create new companies in CRM'
            ],
            [
                'name' => 'crm.companies.edit',
                'display_name' => 'Edit Companies',
                'description' => 'Can edit existing companies in CRM'
            ],
            [
                'name' => 'crm.companies.delete',
                'display_name' => 'Delete Companies',
                'description' => 'Can delete companies in CRM'
            ],
            
            // Deals permissions
            [
                'name' => 'crm.deals.view',
                'display_name' => 'View Deals',
                'description' => 'Can view deals in CRM'
            ],
            [
                'name' => 'crm.deals.create',
                'display_name' => 'Create Deals',
                'description' => 'Can create new deals in CRM'
            ],
            [
                'name' => 'crm.deals.edit',
                'display_name' => 'Edit Deals',
                'description' => 'Can edit existing deals in CRM'
            ],
            [
                'name' => 'crm.deals.delete',
                'display_name' => 'Delete Deals',
                'description' => 'Can delete deals in CRM'
            ],
            
            // Activities permissions
            [
                'name' => 'crm.activities.view',
                'display_name' => 'View Activities',
                'description' => 'Can view activities in CRM'
            ],
            [
                'name' => 'crm.activities.create',
                'display_name' => 'Create Activities',
                'description' => 'Can create new activities in CRM'
            ],
            [
                'name' => 'crm.activities.edit',
                'display_name' => 'Edit Activities',
                'description' => 'Can edit existing activities in CRM'
            ],
            [
                'name' => 'crm.activities.delete',
                'display_name' => 'Delete Activities',
                'description' => 'Can delete activities in CRM'
            ],
            [
                'name' => 'crm.activities.complete',
                'display_name' => 'Complete Activities',
                'description' => 'Can mark activities as completed in CRM'
            ],
            
            // Tasks permissions
            [
                'name' => 'crm.tasks.view',
                'display_name' => 'View Tasks',
                'description' => 'Can view tasks in CRM'
            ],
            [
                'name' => 'crm.tasks.create',
                'display_name' => 'Create Tasks',
                'description' => 'Can create new tasks in CRM'
            ],
            [
                'name' => 'crm.tasks.edit',
                'display_name' => 'Edit Tasks',
                'description' => 'Can edit existing tasks in CRM'
            ],
            [
                'name' => 'crm.tasks.delete',
                'display_name' => 'Delete Tasks',
                'description' => 'Can delete tasks in CRM'
            ],
            [
                'name' => 'crm.tasks.complete',
                'display_name' => 'Complete Tasks',
                'description' => 'Can mark tasks as completed in CRM'
            ],
            
            // Notes permissions
            [
                'name' => 'crm.notes.view',
                'display_name' => 'View Notes',
                'description' => 'Can view notes in CRM'
            ],
            [
                'name' => 'crm.notes.create',
                'display_name' => 'Create Notes',
                'description' => 'Can create new notes in CRM'
            ],
            [
                'name' => 'crm.notes.edit',
                'display_name' => 'Edit Notes',
                'description' => 'Can edit existing notes in CRM'
            ],
            [
                'name' => 'crm.notes.delete',
                'display_name' => 'Delete Notes',
                'description' => 'Can delete notes in CRM'
            ],
            
            // Reports permissions
            [
                'name' => 'crm.reports.view',
                'display_name' => 'View Reports',
                'description' => 'Can view CRM reports and analytics'
            ],
            [
                'name' => 'crm.reports.export',
                'display_name' => 'Export Reports',
                'description' => 'Can export CRM reports and data'
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
