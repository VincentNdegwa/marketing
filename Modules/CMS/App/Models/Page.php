<?php

namespace Modules\CMS\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Page extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cms_pages';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'status',
        'user_id',
        'template_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'published_at',
    ];

    protected $casts = [
        'content' => 'json',
        'published_at' => 'datetime',
    ];

    /**
     * Get the user that created the page
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the template for this page
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template()
    {
        return $this->belongsTo(\Modules\CMS\App\Models\Template::class, 'template_id');
    }

    /**
     * Scope a query to only include published pages
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include draft pages
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Get the URL for the page
     */
    public function getUrlAttribute()
    {
        return url('page/' . $this->slug);
    }

    /**
     * Check if the page is published
     */
    public function isPublished()
    {
        return $this->status === 'published';
    }
}
