<?php
/*
 * Sample event handler for module
 */
namespace Module\Sample;

use App\Event;

class Handlers
{
    public function __construct(Event $events)
    {
        $events->on('module.executed', '*', [$this, 'handleEvent1']);

        $events->on(
            'module.executed',
            'front/pages/*, admin/pages/*',
            function (&$result) {
                // do something with $result
            });
    }

    public function handleEvent1(&$result)
    {
        // do something with $result
    }
}
