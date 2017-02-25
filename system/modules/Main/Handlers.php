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
            $root = '/system/assets/';
            $doc->addJs('jquery', '//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js', 0);
            $doc->addJs('sydes', $root.'js/sydes.js', 1);
            $doc->addJs('ajax-router', $root.'js/ajaxRouter.js', 2);

            $doc->addCss('main', $root.'css/main.css', 0);
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
            $doc->addJs('front', $root.'js/front.js', 10);

            if (app('editor')->isLoggedIn()) {
                $doc->addCss('toolbar', $root.'css/toolbar.css', 11);
                $doc->addPackage('front-editor', $root.'js/frontEditor.js', $root.'css/frontEditor.css', 12);
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

            $root = '/system/assets/';
            $doc->addCss('toolbar', $root.'css/toolbar.css', 12);
            $doc->addCss('admin', $root.'css/admin.css', 14);
            $doc->addCss('skin', $root.'css/skin.'.app('app')['adminSkin'].'.css', 15);

            $doc->addJs('utils', $root.'js/utils.js', 12);
            $doc->addJs('admin', $root.'js/admin.js', 14);
        });
    }
}
