<?php

namespace App\Listeners;

use App\Events\RegisteredUser;
use Illuminate\Support\Facades\Log;

class RegisteredUserLis
{
    /**
     * Handle menu events.
     */
    public function handle(RegisteredUser $event): void
    {
        $user = $event->user;

        Log::info('User registered: ' . $user->name);
        if($user->type =='admin'){
            $adminRole = \App\Models\Role::where('name', 'admin')->first();
            if ($adminRole && ! $user->hasRole('admin')) {
                $user->addRole($adminRole);
            } else {
                Log::error('Admin role not found.');
            }
        }

    }

}