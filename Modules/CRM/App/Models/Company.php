<?php

namespace Modules\CRM\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_companies';

    protected $fillable = [
        'name',
        'industry',
        'employees',
        'annual_revenue',
        'website',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'description',
        'type', // prospect, customer, partner, etc.
        'status',
        'assigned_to',
        'tags'
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'company_id');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'activitable');
    }

    public function tasks()
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
