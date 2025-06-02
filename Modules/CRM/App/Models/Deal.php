<?php

namespace Modules\CRM\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_deals';

    protected $fillable = [
        'name',
        'amount',
        'currency',
        'stage', // prospecting, qualification, proposal, negotiation, closed_won, closed_lost
        'expected_close_date',
        'probability',
        'contact_id',
        'company_id',
        'description',
        'source',
        'assigned_to',
        'products',
        'tags',
        'status'
    ];

    protected $casts = [
        'expected_close_date' => 'date',
        'probability' => 'integer',
        'amount' => 'float',
        'products' => 'array',
        'tags' => 'array',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
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
        return $this->belongsTo(\Modules\UserManagement\App\Models\User::class, 'assigned_to');
    }
}
