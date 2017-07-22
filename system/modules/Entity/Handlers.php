<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity;

use Sydes\Document;
use Sydes\Event;

class Handlers
{
    public function __construct(Event $events)
    {
        $events->on('site.found', '*', function () {
            app('translator')->loadFrom('module', 'Entity');
            model('Entity/Fields')->find();
        });

        $events->on('render.started', 'admin/*', function (Document $doc) {
            $doc->addPackage('fields', 'entity:js/fields.js', 'entity:css/fields.css', 16);
            $doc->addPackage('entity-ui', 'entity:js/entity-ui.js', 'entity:css/entity-ui.css', 17);
        });
    }
}
