<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;

class User extends Authenticatable implements LaratrustUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRolesAndPermissions, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'avatar',
        'job_title',
        'bio',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'date_of_birth',
        'gender',
        'website',
        'social_links',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
            'social_links' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if the user is a Super Admin (system-wide administrator)
     * There should only be one Super Admin in the system
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    /**
     * Check if the user is an Admin for a specific business
     */
    public function isAdminForBusiness(int $businessId): bool
    {
        return $this->hasRoleInBusiness('admin', $businessId);
    }

    /**
     * The businesses that belong to the user.
     */
    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'user_business')
            ->using(UserBusiness::class)
            ->withPivot('is_default')
            ->withTimestamps();
    }

    /**
     * Get the user's default business.
     *
     * @return \App\Models\Business|null
     */
    public function defaultBusiness()
    {
        return $this->businesses()->wherePivot('is_default', true)->first();
    }

    /**
     * Set a business as the default for this user.
     */
    public function setDefaultBusiness(int $businessId): bool
    {
        $this->businesses()->updateExistingPivot(
            $this->businesses()->pluck('businesses.id')->toArray(),
            ['is_default' => false]
        );

        // Then set the specified business as default
        return $this->businesses()->updateExistingPivot($businessId, ['is_default' => true]);
    }

    /**
     * Get roles for a specific business
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function rolesForBusiness(int $businessId)
    {
        return $this->roles()->wherePivot('business_id', $businessId)->get();
    }

    /**
     * Get permissions for a specific business
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function permissionsForBusiness(int $businessId)
    {
        return $this->permissions()->wherePivot('business_id', $businessId)->get();
    }

    /**
     * Check if user has a specific role in a business
     */
    public function hasRoleInBusiness(string $role, int $businessId): bool
    {
        return $this->roles()->wherePivot('business_id', $businessId)->where('name', $role)->exists();
    }

    /**
     * Check if user has a specific permission in a business
     */
    public function hasPermissionInBusiness(string $permission, int $businessId): bool
    {
        return $this->permissions()->wherePivot('business_id', $businessId)->where('name', $permission)->exists();
    }
}
