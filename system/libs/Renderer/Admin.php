<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Renderer;

use App\Document;

class Admin extends Base
{
    public function render(Document $doc)
    {
        $siteName = app('site')['name'];
        $doc->title .= ' - '.$siteName.' @ SyDES';
        $this->prepare($doc);

        $this->document->addPackage('bootstrap',
            '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
            '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
        $this->document->addPackage('fancybox',
            '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js',
            '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.css');

        $this->fillHead();
        $this->fillFooter();

        $dummy = [
            'lang' => app('app')['locale'],
            'head'   => implode("\n    ", $this->head),
            'footer' => implode("\n    ", $this->footer),
            //'context_menu' => $this->site ? $this->getContextMenu() : '',
            'site_name' => $siteName,
            'site_url' => '//'.app('site')['domains'][0], // TODO доавить ссылку на https
            'menu' => $this->getMenu(),
            'form_url' => '',
            'sidebar_left' => '',
            'content' => '',
            'sidebar_right' => '',
            'footer_left' => '',
            'footer_center' => '',
            'skin' => ifsetor(app('request')->cookie['skin'], 'black'), // TODO getCookieParams
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

        return render(DIR_SYSTEM.'/views/main.php', $data);
    }

    public function getMenu()
    {
        $rawMenu = app('site')['menu'];
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
                if (strpos($path, $item['url']) === 0) {
                    $mItem['attr']['class'] = 'active';
                }
                $mItem['quick_add'] = isset($item['quick_add']);
                $menuFlat[] = $mItem;
            }
        }

        return \H::treeList($menuFlat, function ($item) {
            $icon = '';
            if (isset($item['icon'])) {
                if (strpos($item['icon'], '.')) {
                    $icon = '<img src="'.$item['icon'].'">';
                } else {
                    $icon = '<span class="glyphicon glyphicon-'.$item['icon'].'" aria-hidden="true"></span>';
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
        }, ['id' => 'menu', 'class' => 'sticky-top'], 2);
    }
}
