<?php

namespace Modules\CMS\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Template extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cms_templates';


    protected $fillable = [
        'name',
        'description',
        'content',
        'thumbnail',
        'user_id',
        'category',
    ];

    protected $casts = [
        'content' => 'json',
    ];

    /**
     * Get the user that created the template
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the pages that use this template
     */
    public function pages()
    {
        return $this->hasMany(Page::class);
    }
}
