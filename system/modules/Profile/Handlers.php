<?php
namespace Module\Profile;

use Sydes\Document;
use Sydes\Event;

class Handlers
{
    public function __construct(Event $events)
    {
        $events->on('render.started', '*', function (Document $doc) {

            $doc->addContextMenu('right', 'profile', [
                'weight' => 0,
                'title' => app('auth')->getUser('username'),
                'items' => [
                    'profile' => [
                        'title' => t('profile'),
                        'url' => '/admin/profile',
                    ],
                    'div1' => [
                        'attr' => ['class' => 'divider'],
                    ],
                    'logout' => [
                        'title' => t('logout'),
                        'url' => '/auth/logout',
                        'attr' => ['id' => 'logout'],
                    ]
                ]
            ])
            ->addScript('logout', "$('#logout a').attr('data-method', 'post');");

        });
    }
}
