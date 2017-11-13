<?php
namespace Module\Main;

use Sydes\Document;
use Sydes\Event;
use Sydes\Exception\ConfirmationException;

class Handlers
{
    public function __construct(Event $events)
    {
        /**
         * Csrf guard
         */
        $events->on('route.found', '*', [app('csrf'), 'check'], 5);

        $events->on('response.prepared', '*', [app('csrf'), 'appendHeader']);

        /**
         * Base assets for front and admin
         */
        $events->on('render.started', '*', function (Document $doc) {
            $doc->addJs('jquery', '//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js', 0);
            $doc->addJs('sydes', 'main:js/sydes.js', 1);
            $doc->addJs('ajax-router', 'main:js/ajaxRouter.js', 2);

            $doc->addCss('main', 'main:css/main.css', 0);

            $doc->addJsSettings(['locale' => app('locale')]);
            $doc->addCsrfToken(app('csrf')->getTokenName(), app('csrf')->getTokenValue());

            $doc->addScript('extend', '$.extend(syd, '.json_encode($doc->js_syd, JSON_UNESCAPED_UNICODE).');');
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

            $doc->addJs('front', 'main:js/front.js', 10);

            if (app('auth')->check()) {
                $doc->addCss('toolbar', 'main:css/toolbar.css', 11);
                $doc->addPackage('front-editor', 'main:js/frontEditor.js', 'main:css/frontEditor.css', 12);
            }
        });

        /**
         * Base assets for admin
         */
        $events->on('render.started', 'admin/*', function (Document $doc) {
            $doc->addContextMenu('left', 'brand_link', [
                'weight' => 0,
                'title' => app('site')->get('name'),
                'url' => '//'.app('site')->get('domains')[0]
            ]);

            $doc->addPackage('bootstrap', [
                    '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js',
                    '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js',
                ], [
                    '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
                    '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css',
                ],
                10
            );
            $doc->addPackage('fancybox',
                '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.7/js/jquery.fancybox.min.js',
                '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.7/css/jquery.fancybox.min.css',
                11
            );

            $doc->addCss('toolbar', 'main:css/toolbar.css', 12);
            $doc->addCss('admin', 'main:css/admin.css', 14);
            $doc->addCss('skin', 'main:css/skin.black.css', 15);

            $doc->addJs('utils', 'main:js/utils.js', 13);
            $doc->addJs('admin', 'main:js/admin.js', 14);

            $doc->addPackage('jquery-ui', 'main:js/jquery-ui.min.js', 'main:css/jquery-ui.min.css', 0);
        });

        $events->on('route.found', 'admin/*', function () {
            if (app('request')->isMethod('delete') && !app('request')->has('confirmed')) {
                throw new ConfirmationException;
            }
        }, 15);

        /**
         * Replace "module-name:" with his assets path in addCss or addJs
         */
        $events->on('assets.prepared', '*', function (&$files) {
            foreach ($files as &$file) {
                if ($file[0] != '/' && substr($file, 0, 4) != 'http' &&
                    ($pos = strpos($file, ':')) !== false &&
                    ($path = moduleDir(substr($file, 0, $pos))) !== false) {
                    $path = str_replace(app('dir.root'), '', $path).'/assets/';
                    $file = substr_replace($file, $path, 0, $pos + 1);
                }
            }
        }, 10);
    }
}
