<?php

namespace Modules\UserManagement\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    /**
     * Test that the module routes are registered.
     *
     * @return void
     */
    public function test_module_routes_are_registered()
    {
        $this->assertTrue(Route::has('usermanagement.index'));
        $this->assertTrue(Route::has('usermanagement.create'));
        $this->assertTrue(Route::has('usermanagement.store'));
        $this->assertTrue(Route::has('usermanagement.show'));
        $this->assertTrue(Route::has('usermanagement.edit'));
        $this->assertTrue(Route::has('usermanagement.update'));
        $this->assertTrue(Route::has('usermanagement.destroy'));
    }

    /**
     * Test that the module controllers exist.
     *
     * @return void
     */
    public function test_module_controllers_exist()
    {
        $this->assertTrue(class_exists('Modules\\UserManagement\\App\\Http\\Controllers\\UserManagementController'));
        $this->assertTrue(class_exists('Modules\\UserManagement\\App\\Http\\Controllers\\UserRoleController'));
    }

    /**
     * Test that the module views exist.
     *
     * @return void
     */
    public function test_module_views_exist()
    {
        $this->assertFileExists(module_path('UserManagement', 'resources/js/pages/users/Index.vue'));
        $this->assertFileExists(module_path('UserManagement', 'resources/js/pages/users/Create.vue'));
        $this->assertFileExists(module_path('UserManagement', 'resources/js/pages/users/Edit.vue'));
    }
}
