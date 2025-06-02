<?php

namespace Modules\CRM\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_tasks';

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'completed_date',
        'status', // pending, in_progress, completed, cancelled
        'priority', // high, medium, low
        'taskable_id',
        'taskable_type',
        'created_by',
        'assigned_to',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_date' => 'datetime',
    ];

    public function taskable()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(\Modules\UserManagement\App\Models\User::class, 'created_by');
    }

    public function assignedUser()
    {
        return $this->belongsTo(\Modules\UserManagement\App\Models\User::class, 'assigned_to');
    }
}
