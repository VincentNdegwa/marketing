<?php

namespace Modules\Marketing\App\Database\Seeders;

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
        $module = 'Marketing';

        Log::info('Starting Marketing permission seeding');

        // Define module permissions
        $permissions = [
            // Example permission format - add your module permissions here
            [
                'name' => 'marketing.manage',
                'display_name' => 'Manage Marketing',
                'description' => 'Can manage Marketing module'
            ],
            [
                'name' => 'facebook_ads.view',
                'display_name' => 'View Facebook Ads',
                'description' => 'Can view Facebook ad campaigns, ad sets, and ads.',
            ],
            [
                'name' => 'facebook_ads.create',
                'display_name' => 'Create Facebook Ads',
                'description' => 'Can create new Facebook ad campaigns, ad sets, and ads.',
            ],
            [
                'name' => 'facebook_ads.edit',
                'display_name' => 'Edit Facebook Ads',
                'description' => 'Can modify existing Facebook ad campaigns, ad sets, and ads.',
            ],
            [
                'name' => 'facebook_ads.pause_resume',
                'display_name' => 'Pause/Resume Facebook Ads',
                'description' => 'Can start and stop Facebook ad campaigns, ad sets, or individual ads.',
            ],
            [
                'name' => 'facebook_ads.manage_budget',
                'display_name' => 'Manage Facebook Ads Budget',
                'description' => 'Can set and adjust budgets for Facebook ad campaigns and ad sets.',
            ],
            [
                'name' => 'facebook_ads.manage_bidding',
                'display_name' => 'Manage Facebook Ads Bidding',
                'description' => 'Can configure and modify bidding strategies for Facebook ads.',
            ],
            [
                'name' => 'facebook_ads.manage_placements',
                'display_name' => 'Manage Facebook Ads Placements',
                'description' => 'Can select and change where Facebook ads are displayed.',
            ],
            [
                'name' => 'facebook_ads.manage_targeting',
                'display_name' => 'Manage Facebook Ads Targeting',
                'description' => 'Can define and edit audience targeting options for Facebook ads.',
            ],
            [
                'name' => 'facebook_ads.manage_creatives',
                'display_name' => 'Manage Facebook Ads Creatives',
                'description' => 'Can upload, edit, and manage visual and textual content for Facebook ads.',
            ],
            [
                'name' => 'facebook_ads.manage_reporting',
                'display_name' => 'Manage Facebook Ads Reporting',
                'description' => 'Can create, customize, and export reports on Facebook ad performance.',
            ],
            [
                'name' => 'facebook_ads.manage_rules',
                'display_name' => 'Manage Facebook Ads Rules',
                'description' => 'Can create and modify automated rules for managing Facebook ad campaigns.',
            ],
            [
                'name' => 'facebook_ads.manage_payment_methods',
                'display_name' => 'Manage Facebook Ads Payment Methods',
                'description' => 'Can add, edit, or remove payment methods associated with the Facebook ad account.',
            ],
            [
                'name' => 'facebook_ads.manage_account_settings',
                'display_name' => 'Manage Facebook Ads Account Settings',
                'description' => 'Can modify general settings of the Facebook ad account.',
            ],
            [
                'name' => 'facebook_ads.manage_users_roles',
                'display_name' => 'Manage Facebook Ads Users and Roles',
                'description' => 'Can invite, assign roles, and remove users from the Facebook ad account.',
            ],
            [
                'name' => 'facebook_ads.create_custom_audiences',
                'display_name' => 'Create Facebook Custom Audiences',
                'description' => 'Can create custom audiences for Facebook ad targeting.',
            ],
            [
                'name' => 'facebook_ads.create_lookalike_audiences',
                'display_name' => 'Create Facebook Lookalike Audiences',
                'description' => 'Can create lookalike audiences for Facebook ad targeting.',
            ],
            [
                'name' => 'facebook_ads.edit_saved_audiences',
                'display_name' => 'Edit Facebook Saved Audiences',
                'description' => 'Can modify previously saved audiences for Facebook ad targeting.',
            ],
            [
                'name' => 'facebook_ads.create_campaigns_only',
                'display_name' => 'Create Facebook Campaigns Only',
                'description' => 'Can create Facebook ad campaigns but not ad sets or ads.',
            ],
            [
                'name' => 'facebook_ads.create_ad_sets',
                'display_name' => 'Create Facebook Ad Sets',
                'description' => 'Can create Facebook ad sets within existing campaigns.',
            ],
            [
                'name' => 'facebook_ads.create_ads_under_ad_set',
                'display_name' => 'Create Facebook Ads',
                'description' => 'Can create individual Facebook ads within existing ad sets.',
            ],
            [
                'name' => 'google_ads.view',
                'display_name' => 'View Google Ads',
                'description' => 'Can view Google Ads campaigns, ad groups, and ads.',
            ],
            [
                'name' => 'google_ads.create',
                'display_name' => 'Create Google Ads',
                'description' => 'Can create new Google Ads campaigns, ad groups, and ads.',
            ],
            [
                'name' => 'google_ads.edit',
                'display_name' => 'Edit Google Ads',
                'description' => 'Can modify existing Google Ads campaigns, ad groups, and ads.',
            ],
            [
                'name' => 'google_ads.pause_resume',
                'display_name' => 'Pause/Resume Google Ads',
                'description' => 'Can start and stop Google Ads campaigns, ad groups, or individual ads.',
            ],
            [
                'name' => 'google_ads.manage_budget',
                'display_name' => 'Manage Google Ads Budget',
                'description' => 'Can set and adjust budgets for Google Ads campaigns.',
            ],
            [
                'name' => 'google_ads.manage_bidding',
                'display_name' => 'Manage Google Ads Bidding',
                'description' => 'Can configure and modify bidding strategies for Google Ads.',
            ],
            [
                'name' => 'google_ads.manage_placements',
                'display_name' => 'Manage Google Ads Placements',
                'description' => 'Can select and change where Google Ads are displayed on the Google Network.',
            ],
            [
                'name' => 'google_ads.manage_targeting',
                'display_name' => 'Manage Google Ads Targeting',
                'description' => 'Can define and edit audience and keyword targeting options for Google Ads.',
            ],
            [
                'name' => 'google_ads.manage_creatives',
                'display_name' => 'Manage Google Ads Creatives',
                'description' => 'Can upload, edit, and manage text, image, and video ads in Google Ads.',
            ],
            [
                'name' => 'google_ads.manage_reporting',
                'display_name' => 'Manage Google Ads Reporting',
                'description' => 'Can create, customize, and export reports on Google Ads performance.',
            ],
            [
                'name' => 'google_ads.manage_extensions',
                'display_name' => 'Manage Google Ads Extensions',
                'description' => 'Can create and manage ad extensions (sitelinks, callouts, etc.) in Google Ads.',
            ],
            [
                'name' => 'google_ads.manage_keywords',
                'display_name' => 'Manage Google Ads Keywords',
                'description' => 'Can add, edit, and manage keywords for Google Ads campaigns.',
            ],
            [
                'name' => 'google_ads.manage_negative_keywords',
                'display_name' => 'Manage Google Ads Negative Keywords',
                'description' => 'Can add and manage negative keywords to refine Google Ads targeting.',
            ],
            [
                'name' => 'google_ads.manage_account_settings',
                'display_name' => 'Manage Google Ads Account Settings',
                'description' => 'Can modify general settings of the Google Ads account.',
            ],
            [
                'name' => 'google_ads.manage_users_roles',
                'display_name' => 'Manage Google Ads Users and Roles',
                'description' => 'Can invite, assign roles, and manage user access to the Google Ads account.',
            ],
            [
                'name' => 'google_ads.create_campaigns_only',
                'display_name' => 'Create Google Ads Campaigns Only',
                'description' => 'Can create Google Ads campaigns but not ad groups or ads.',
            ],
            [
                'name' => 'google_ads.create_ad_groups',
                'display_name' => 'Create Google Ads Ad Groups',
                'description' => 'Can create Google Ads ad groups within existing campaigns.',
            ],
            [
                'name' => 'google_ads.create_ads_under_ad_group',
                'display_name' => 'Create Google Ads',
                'description' => 'Can create individual Google Ads within existing ad groups.',
            ],

        ];

        $superadminRole = Role::where('name', 'superadmin')->first();
        Log::info('SuperAdmin role found: ' . ($superadminRole ? 'Yes' : 'No'));

        $adminRoles = Role::where('name', 'admin')->get();
        Log::info('Admin roles found: ' . $adminRoles->count());

        $businesses = Business::all();
        Log::info('Businesses found: ' . $businesses->count());

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
                    Log::info('Created system permission: ' . $permissionData['name']);
                } catch (\Exception $e) {
                    Log::error('Failed to create system permission: ' . $e->getMessage());
                }

                if ($superadminRole && isset($permission)) {
                    try {
                        if (!$superadminRole->hasPermission($permission['name'])) {
                            $superadminRole->givePermissions([$permission]);
                            Log::info('Attached permission to superadmin role: ' . $permissionData['name']);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to attach permission to superadmin: ' . $e->getMessage());
                    }
                }

                foreach ($adminRoles as $adminRole) {
                    try {
                        if (!$adminRole->hasPermission($permission['name'])) {
                            $adminRole->givePermissions([$permission]);
                            Log::info('Attached permission to admin role: ' . $permissionData['name']);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to attach permission to admin: ' . $e->getMessage());
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
