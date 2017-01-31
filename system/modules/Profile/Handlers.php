<?php
namespace Module\Profile;

use App\Document;
use App\Event;

class Handlers
{
    public static function init(Event $events)
    {
        $events->on('module.executed', '*', function ($content) {
            if ($content instanceof Document) {
                $content->addContextMenu('right', 'profile', [
                    'weight' => 0,
                    'title' => app('editor')->username,
                    'items' => [
                        'profile' => [
                            'title' => 'profile',
                            'url' => '/admin/profile',
                        ],
                        'div1' => [
                            'attr' => 'class="divider"',
                        ],
                        'logout' => [
                            'html' => '<a href="/auth/logout" onclick="event.preventDefault();'.
                                '$(\'#logout-form\').submit();">'.t('logout').'</a>'.
                                '<form id="logout-form" action="/auth/logout" method="POST" style="display: none;"></form>',
                        ]
                    ]
                ]);
            }
        });
    }
}
