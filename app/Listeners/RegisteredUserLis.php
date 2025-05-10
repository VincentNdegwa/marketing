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

        // Log the user registration event
        Log::info('User registered: ' . $user->name);

        // Perform any additional actions needed after user registration
        // For example, send a welcome email or assign roles
    }

}