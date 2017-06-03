<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Mailer;

class EventsController
{
    public function index()
    {
        $d = document([
            'title' => t('mailer_events'),
            'content' => 'mailer events',
        ]);

        return $d;
    }

}
