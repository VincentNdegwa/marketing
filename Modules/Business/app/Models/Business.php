<?php

namespace Modules\Business\app\Models;

use App\Models\Permission;
use App\Models\Role;
use Modules\Business\App\Database\Factories\BusinessFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Hash;

class Business extends Model
{
    use HasFactory;
    
    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return BusinessFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'website',
        'is_active',
    ];

    /**
     * The users that belong to the business.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_business')
            ->using(UserBusiness::class)
            ->withPivot('is_default')
            ->withTimestamps();
    }

    /**
     * The roles associated with this business.
     */
    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    /**
     * The permissions associated with this business.
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    public function adminUser()
    {
        return $this->users()
            ->whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->first();
    }

    public static function permitNewBusiness($business_id, $admin_name, $admin_email)
    {
        $clientAdmin = Role::create([
            'name' => 'admin',
            'display_name' => 'Client Administrator',
            'description' => 'Administrator of the client business',
            'business_id' => $business_id,
        ]);

        $clientAdminUser = User::where('email', $admin_email)->where('name', $admin_name)->first();
        if (! $clientAdminUser) {
            $clientAdminUser = User::create([
                'name' => $admin_name,
                'email' => $admin_email,
                'password' => Hash::make('password'),
                'type' => 'admin',
            ]);
        }

        $permissions = Permission::whereNull('business_id')->get();
        foreach ($permissions as $permission) {
            $permission->business_id = $business_id;
            $new_permission = Permission::create($permission->toArray());
            $clientAdmin->givePermissions([$new_permission]);
        }

        $clientAdminUser->roles()->attach($clientAdmin->id);
        $clientAdminUser->businesses()->attach($business_id, ['is_default' => true]);
    }
}
