<?php

namespace Modules\CRM\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_contacts';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'job_title',
        'company_id',
        'lead_source',
        'lead_status',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'website',
        'notes',
        'tags',
        'assigned_to',
        'status'
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    protected $appends = ['full_name'];


    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'activitable');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
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

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
