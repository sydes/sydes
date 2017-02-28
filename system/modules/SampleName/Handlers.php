<?php
namespace Module\SampleName;

use App\Document;
use App\Event;

class Handlers
{
    public function __construct(Event $events)
    {
        $events->on('module.executed', '*', [$this, 'handleEvent1']);

        $events->on(
            'module.executed',
            'front/samplename/*, admin/samplename/*',
            function ($content) {
                if ($content instanceof Document) {
                    $content->addContextMenu('right', 'support', [
                        'weight' => 10,
                        'title'  => 'support',
                        'url'    => '/html',
                        'modal'  => 'sm'
                    ]);
                }
            });
    }

    public function handleEvent1($content)
    {
        // do something with $result
    }
}
