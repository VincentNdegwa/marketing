<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laratrust\Models\Permission as PermissionModel;
use Modules\Business\App\Models\Business;

class Permission extends PermissionModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'business_id',
        'module',
    ];

    /**
     * The business that this permission belongs to.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    // /**
    //  * Scope a query to only include permissions for a specific business.
    //  *
    //  * @param  \Illuminate\Database\Eloquent\Builder  $query
    //  * @return \Illuminate\Database\Eloquent\Builder
    //  */
    // public function scopeForBusiness($query, int $businessId)
    // {
    //     return $query->where('business_id', $businessId);
    // }
}
