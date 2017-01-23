<?php
namespace Module\SampleName;

use App\Event;

class Handlers
{
    public static function init(Event $events)
    {
        new self($events);
    }

    public function __construct(Event $events)
    {
        $events->on('module.executed', '*', [$this, 'handleEvent1']);

        $events->on(
            'module.executed',
            'front/sample-name/*, admin/sample-name/*',
            function (&$result) {
                // do something with $result
                // you should know type of $result
            });
    }

    public function handleEvent1(&$result)
    {
        // do something with $result
    }
}
