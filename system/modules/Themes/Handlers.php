<?php
namespace Module\Themes;

use Sydes\Event;
use Sydes\View\View;

class Handlers
{
    public function __construct(Event $events)
    {
        $events->on('view.render.started', '*', function (View $view) {
            list($module, $file) = explode('/', $view->name(), 2);
            if ($path = model('Themes')->getActive()->getThemedView('module', $module, $file)) {
                $view->setPath($path);
            }
        });
    }
}
