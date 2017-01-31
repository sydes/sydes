<?php
namespace Module\Main;

use App\Document;
use App\Event;

class Handlers
{
    public static function init(Event $events)
    {
        /**
         * Csrf guard
         */
        $events->on('route.found', '*', [app('csrf'), 'check']);

        $events->on('module.executed', 'front/*', function ($content) {
            if ($content instanceof Document) {
                $content->addContextMenu('left', 'brand_link', [
                    'weight' => 0,
                    'title' => 'admin_center',
                    'url' => '/admin'
                ]);
            }
        });

        $events->on('module.executed', 'admin/*', function ($content) {
            if ($content instanceof Document) {
                $content->addContextMenu('left', 'brand_link', [
                    'weight' => 0,
                    'title' => 'site_name',
                    'url' => '/'
                ]);
            }
        });
    }
}
