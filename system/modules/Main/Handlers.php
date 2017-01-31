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

        $events->on('module.executed', '*', function ($doc) {
            if ($doc instanceof Document) {
                $doc->addJs('jquery', '//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js', 0);
                $doc->addJs('sydes', '/system/assets/js/sydes.js', 1);
                $doc->addJs('ajax-router', '/system/assets/js/ajaxRouter.js', 2);
            }
        });

        $events->on('module.executed', 'front/*', function ($doc) {
            if ($doc instanceof Document) {
                $doc->addContextMenu('left', 'brand_link', [
                    'weight' => 0,
                    'title' => 'admin_center',
                    'url' => '/admin'
                ]);

                if (app('editor')->isLoggedIn()) {
                    $doc->addCss('toolbar', '/system/assets/css/toolbar.css', 10);
                    $doc->addPackage('front-editor',
                        '/system/assets/js/frontEditor.js',
                        '/system/assets/css/frontEditor.css'
                        );
                }
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
