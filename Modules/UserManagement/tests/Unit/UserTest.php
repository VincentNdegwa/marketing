<?php

namespace Modules\UserManagement\Tests\Unit;

use App\Models\User;
use App\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test that the User model exists and has the required relationships.
     *
     * @return void
     */
    public function test_user_model_exists_with_required_relationships()
    {
        $this->assertTrue(class_exists(User::class));
        
        $user = new User();
        $this->assertTrue(method_exists($user, 'roles'));
        $this->assertTrue(method_exists($user, 'businesses'));
    }

    /**
     * Test that the Role model exists and has the required relationships.
     *
     * @return void
     */
    public function test_role_model_exists_with_required_relationships()
    {
        $this->assertTrue(class_exists(Role::class));
        
        $role = new Role();
        // Check if the business relationship exists
        $this->assertTrue(method_exists($role, 'business'));
        $this->assertInstanceOf('Laratrust\Models\Role', $role);
    }

    /**
     * Test that the module has the required files.
     *
     * @return void
     */
    public function test_module_has_required_files()
    {
        $this->assertFileExists(module_path('UserManagement', 'app/Http/Controllers/UserManagementController.php'));
        $this->assertFileExists(module_path('UserManagement', 'app/Http/Controllers/UserRoleController.php'));
        $this->assertFileExists(module_path('UserManagement', 'routes/web.php'));
    }
}
