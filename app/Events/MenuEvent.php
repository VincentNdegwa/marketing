<?php

namespace App\Events;

use App\Classes\Menu;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MenuEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $menu;

    /**
     * Create a new event instance.
     */
    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }
}
