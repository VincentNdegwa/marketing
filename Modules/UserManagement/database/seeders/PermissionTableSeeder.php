<?php

namespace Modules\UserManagement\App\Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class PermissionTableSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();
        Artisan::call('cache:clear');
        $module = 'UserManagement';

        $permissions = [
            // Please add the permissions for your module here.
        ];

        $superadmin_role = Role::where('name', 'superadmin')->first();
        foreach ($permissions as $key => $value) {
            $check = Permission::where('name', $value)->where('module', $module)->exists();
            if ($check == false) {
                $permission = Permission::create(
                    [
                        'name' => $value['name'],
                        'guard_name' => $value['description'],
                        'module' => $module,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                );
                if (! $superadmin_role->hasPermission($value)) {
                    $superadmin_role->givePermission($permission);
                }
            }
        }
    }
}
