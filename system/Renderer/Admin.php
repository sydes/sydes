<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */
namespace System\Renderer;

use Sydes\Document;
use Sydes\Renderer\Base;

class Admin extends Base
{
    public function render(Document $doc)
    {
        $this->event->trigger('render.started', [$doc]);

        $doc->title = ifsetor($doc->data['title'], $doc->title).' - '.app('site')->get('name').' @ SyDES';

        $this->prepare($doc);
        $this->fillHead();
        $this->fillFooter();

        $dummy = [
            'lang' => app('app')->get('locale'),
            'head'   => implode("\n    ", $this->head),
            'footer' => implode("\n    ", $this->footer),
            'menu' => $this->getMenu(),
            'content' => '',
            'footer_left' => '',
            'footer_center' => '',
            'title' => '',
            'header_actions' => '',
        ];

        $data = array_merge($dummy, $doc->data);

        $template = render(app('dir.system').'/modules/Main/views/admin.php', $data);
        $this->event->trigger('render.ended', [&$template]);
        return $template;
    }

    public function getMenu()
    {
        $rawMenu = app('admin.menu')->getMenu();
        $menuFlat = [];

        uasort($rawMenu, 'sortByWeight');
        foreach ($rawMenu as $groupName => $group) {
            if (empty($group['items'])) {
                continue;
            }
            $menuFlat[] = [
                'level' => 1,
                'title' => $group['title'],
                'icon' => $group['icon'],
                'attr' => ['id' => 'menu_'.$groupName],
            ];
            uasort($group['items'], 'sortByWeight');
            $path = app('request')->getUri()->getPath();
            foreach ($group['items'] as $itemName => $item) {
                $mItem = [
                    'level' => 2,
                    'title' => $item['title'],
                    'url' => $item['url'],
                    'attr' => [
                        'id' => $itemName
                    ],
                ];
                if ($path == $item['url']) {
                    $mItem['attr']['class'] = 'active';
                }
                $mItem['quick_add'] = isset($item['quick_add']);
                $menuFlat[] = $mItem;

                if (isset($item['items'])) {
                    foreach ($item['items'] as $subItem) {
                        $menuFlat[] = [
                            'level' => 3,
                            'title' => $subItem['title'],
                            'url' => $subItem['url'],
                            'quick_add' => false,
                        ];
                    }
                }
            }
        }

        foreach (app('site')->get('modules') as $module => $void) {
            app('translator')->loadFrom('module', $module);
        }

        return \H::treeList($menuFlat, function ($item) {
            $icon = '';
            if (isset($item['icon'])) {
                if (strpos($item['icon'], '.')) {
                    $icon = '<img src="'.$item['icon'].'">';
                } else {
                    $icon = '<i class="fa fa-'.$item['icon'].'"></i>';
                }
            }

            if (isset($item['url'])) {
                $add = $item['quick_add'] ? '<a href="'.$item['url'].'/add" data-toggle="tooltip"
                data-placement="right" title="'.t('tip_add').'">[+1]</a>' : '';
                $return = '<a href="'.$item['url'].'">'.t($item['title']).'</a>'.$add;
            } else {
                $return = '<div class="group">'.$icon.t($item['title']).'</div>';
            }

            return $return;
        }, ['id' => 'menu'], 3);
    }
}
