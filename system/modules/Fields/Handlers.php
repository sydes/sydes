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
            app('translator')->loadFrom('module', 'Fields');

            model('Fields')->register('text', [
                'name' => t('field_text'),
                'class' => 'Module\\Fields\\Field\\TextField',
                'description' => t('field_text_description'),
            ])->register('images', [
                'name' => t('field_images'),
                'class' => 'Module\\Fields\\Field\\ImagesField',
                'description' => t('field_images_description'),
            ])->register('map', [
                'name' => t('field_map'),
                'class' => 'Module\\Fields\\Field\\MapField',
                'description' => t('field_map_description'),
            ])->register('color_picker', [
                'name' => t('field_color_picker'),
                'class' => 'Module\\Fields\\Field\\ColorpickerField',
                'description' => t('field_color_picker_description'),
            ]);
        });

        $events->on('render.started', 'admin/*', function (Document $doc) {
            $root = assetsDir('fields');
            $doc->addPackage('fields', $root.'/js/fields.js', $root.'/css/fields.css', 16);
        });
    }
}
