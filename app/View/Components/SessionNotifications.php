<?php

namespace App\View\Components;

use App\Services\NotificationService;
use Illuminate\View\Component;

class SessionNotifications extends Component
{
    public $notifications;

    public function __construct()
    {
        $this->notifications = NotificationService::getAll();
    }

    public function render()
    {
        return view('components.session-notifications');
    }
}

