<?php
namespace Module\Auth;

use Sydes\Event;
use Sydes\Exception\RedirectException;

class Handlers
{
    public function __construct(Event $events)
    {
        /**
         * Auth middleware :)
         */
        $events->on('route.found', 'admin/*', function () {
            if (!app('user')->isLoggedIn()) {
                $_SESSION['entry'] = app('request')->getUri()->getPath();
                throw new RedirectException('/auth/login');
            }
        }, 10);
    }
}
