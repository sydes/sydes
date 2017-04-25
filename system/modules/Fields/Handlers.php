<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fields;

use Sydes\Document;
use Sydes\Event;

class Handlers
{
    public function __construct(Event $events)
    {
        $events->on('site.found', '*', function () {
            model('Fields')->find();
        });

        $events->on('render.started', 'admin/*', function (Document $doc) {
            $root = assetsPath('fields');
            $doc->addPackage('fields', $root.'/js/fields.js', $root.'/css/fields.css', 16);
        });
    }
}
