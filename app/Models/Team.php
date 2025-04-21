<?php

namespace App\Models;

use Laratrust\Models\Team as TeamModel;

class Team extends TeamModel
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
    ];
}
