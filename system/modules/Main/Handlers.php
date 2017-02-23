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

        $events->on('response.prepared', '*', [app('csrf'), 'appendHeader']);

        /**
         * Base assets for front and admin
         */
        $events->on('render.started', '*', function (Document $doc) {
            $doc->addJs('jquery', '//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js', 0);
            $doc->addJs('sydes', '/system/assets/js/sydes.js', 1);
            $doc->addJs('ajax-router', '/system/assets/js/ajaxRouter.js', 2);

            $doc->addCss('notify', '/system/assets/css/notify.css', 10);
        });

        /**
         * Base assets for front
         */
        $events->on('render.started', 'front/*', function (Document $doc) {
            $doc->addContextMenu('left', 'brand_link', [
                'weight' => 0,
                'title' => 'admin_center',
                'url' => '/admin'
            ]);

            $root = '/system/assets/';
            $doc->addJs('sydes-front', $root.'js/front.js', 9);

            if (app('editor')->isLoggedIn()) {
                $doc->addCss('toolbar', $root.'css/toolbar.css', 10);
                $doc->addPackage('front-editor', $root.'js/frontEditor.js', $root.'css/frontEditor.css', 11);
            }

        });

        /**
         * Base assets for admin
         */
        $events->on('render.started', 'admin/*', function (Document $doc) {
            $doc->addContextMenu('left', 'brand_link', [
                'weight' => 0,
                'title' => 'site_name',
                'url' => '/'
            ]);

            $doc->addPackage('bootstrap', [
                    '//cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js',
                    '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js',
                ], [
                    '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
                    '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css',
                ],
                10
            );
            $doc->addPackage('fancybox',
                '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js',
                '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.css',
                11
            );

            $doc->addCss('toolbar', '/system/assets/css/toolbar.css', 9);
        });
    }
}
