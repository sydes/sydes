<?php

namespace Module\Update;

use Sydes\Event;

class Handlers
{
    public function __construct(Event $events)
    {
        /**
         * Check updates for cmf in admin center
         */
        $events->on('module.executed', 'admin/*', function () {
            if (!app('settings')['checkUpdates']) {
                return;
            }

            $cache = app('cache');
            $cache->remember('update_checked', function () use ($cache) {
                $need = app('api')->checkUpdate();
                $updateText = 0;
                if ($need == 1) {
                    $updateText = t('common_update_cms');
                } elseif ($need == 2) {
                    $updateText = t('security_update_cms');
                }
                $cache->put('update_text', $updateText, 600);
                return 1;
            }, 86400);

            $update_text = $cache->get('update_text');
            if ($update_text) {
                alert($update_text);
            }
        });
    }
}
