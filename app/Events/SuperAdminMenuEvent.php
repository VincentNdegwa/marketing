<?php

namespace App\Events;

use App\Classes\Menu;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SuperAdminMenuEvent extends MenuEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(Menu $menu)
    {
        parent::__construct($menu);
    }
}
