<?php

namespace Modules\CRM\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_notes';

    protected $fillable = [
        'title',
        'content',
        'notable_id',
        'notable_type',
        'created_by',
    ];

    public function notable()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(\Modules\UserManagement\App\Models\User::class, 'created_by');
    }
}
