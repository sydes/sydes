<?php
namespace Module\Profile;

use App\Document;
use App\Event;

class Handlers
{
    public static function init(Event $events)
    {
        $events->on('render.started', '*', function (Document $content) {

            $content->addContextMenu('right', 'profile', [
                'weight' => 0,
                'title' => app('user')->username,
                'items' => [
                    'profile' => [
                        'title' => 'profile',
                        'url' => '/admin/profile',
                    ],
                    'div1' => [
                        'attr' => 'class="divider"',
                    ],
                    'logout' => [
                        'title' => 'logout',
                        'url' => '/auth/logout',
                        'attr' => 'class="toolbar-item" id="logout"',
                    ]
                ]
            ])
            ->addScript('logout', "$(document).on('click', '#logout a', function (e) {
                e.preventDefault();$.post($(this).attr('href'));
            })");

        });
    }
}
