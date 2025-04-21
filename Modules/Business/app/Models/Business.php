<?php

namespace Modules\Business\app\Models;

use App\Models\Permission;
use App\Models\Role;
// use Modules\Business\Database\Factories\BusinessFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Business extends Model
{
    use HasFactory;

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
}
