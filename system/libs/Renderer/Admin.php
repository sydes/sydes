<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Renderer;

use App\Document;

class Admin extends Base
{
    public function render(Document $doc)
    {
        app('event')->trigger('render.started', [$doc]);

        $this->prepare($doc);
        $this->fillHead();
        $this->fillFooter();

        $dummy = [
            'lang' => app('app')['locale'],
            'head'   => implode("\n    ", $this->head),
            'footer' => implode("\n    ", $this->footer),
            'menu' => $this->getMenu(),
            'form_url' => '',
            'sidebar_left' => '',
            'content' => '',
            'sidebar_right' => '',
            'footer_left' => '',
            'footer_center' => '',
            'col_sm' => 12,
            'col_lg' => 12,
        ];
        if (!empty($doc->data['sidebar_left'])){
            $dummy['col_sm'] = $dummy['col_sm']-3;
            $dummy['col_lg'] = $dummy['col_lg']-2;
        }
        if (!empty($doc->data['sidebar_right'])){
            $dummy['col_sm'] = $dummy['col_sm']-3;
            $dummy['col_lg'] = $dummy['col_lg']-2;
        }
        $data = array_merge($dummy, $doc->data);

        $template = render(DIR_SYSTEM.'/views/main.php', $data);
        app('event')->trigger('render.ended', [&$template]);
        return $template;
    }

    public function getMenu()
    {
        $rawMenu = app('site')->get('menu');
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
                'attr' => 'id="menu_'.$groupName.'"',
            ];
            usort($group['items'], 'sortByWeight');
            $path = app('request')->getUri()->getPath();
            foreach ($group['items'] as $item) {
                $mItem = [
                    'level' => 2,
                    'title' => $item['title'],
                    'url' => $item['url'],
                    'attr' => [
                        'id' => $item['title']
                    ],
                ];
                if ($path == $item['url']) {
                    $mItem['attr']['class'] = 'active';
                }
                $mItem['quick_add'] = isset($item['quick_add']);
                $menuFlat[] = $mItem;
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
        }, ['id' => 'menu'], 2);
    }
}
