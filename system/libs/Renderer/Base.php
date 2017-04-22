<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes\Renderer;

use Sydes\Document;
use Sydes\View;

class Base
{
    /** @var Document */
    protected $document;
    protected $head = [];
    protected $footer = [];

    public function prepare(Document $doc)
    {
        foreach ($doc->data as &$item) {
            if ($item instanceof View) {
                $item = $item->render();
            }
        }

        $this->document = $doc;
        $this->addAlerts();
        $this->addNotify();
    }

    protected function addAlerts()
    {
        if (!empty($_SESSION['alerts'])) {
            $alerts = '';
            foreach ($_SESSION['alerts'] as $a) {
                $a['message'] = str_replace(
                    ["\n", '{', '\\', '"'],
                    ['', '&#123;', '\\\\', '\"'],
                    $a['message']
                );
                $alerts .= 'syd.alert("'.$a['message'].'", "'.$a['status'].'");';
            }
            $this->document->addScript('alerts', $alerts);
            $_SESSION['alerts'] = [];
        }
        $this->document->data['alerts'] = '<div id="alerts"></div>';
    }

    protected function addNotify()
    {
        if (isset($_SESSION['notify'])) {
            $this->document->addScript('notify',
                'syd.notify("'.$_SESSION['notify']['message'].'", "'.$_SESSION['notify']['status'].'");');
            unset($_SESSION['notify']);
        }
    }

    protected function fillHead()
    {
        $this->head[] = '<title>'.$this->document->title.'</title>';

        $files = $this->prepareAssets($this->document->css, 'css');
        foreach ($files as $file) {
            $this->head[] = '<link rel="stylesheet" href="'.$file.'" media="screen">';
        }

        $this->head[] = empty($this->document->styles) ? '' :
            '<style>'."\n".implode("\n\n", $this->document->styles)."\n".'</style>';

        app('event')->trigger('head.filled', [&$this->head]);
    }

    protected function fillFooter()
    {
        $files = $this->prepareAssets($this->document->js, 'js');
        foreach ($files as $file) {
            $this->footer[] = '<script src="'.$file.'"></script>';
        }

        $this->footer[] = '<ul id="notify"></ul>';

        if (app('user')->isLoggedIn()) {
            $this->footer[] = $this->getToolbar();
        }

        $this->footer[] = "<script>\n".implode("\n\n", $this->document->scripts)."\n</script>";

        app('event')->trigger('footer.filled', [&$this->footer]);
    }

    protected function getToolbar()
    {
        $key = 'toolbar.'.md5(json_encode($this->document->context_menu));
        return app('cache')->remember($key, function () {
            $menuFlat = [];
            foreach ($this->document->context_menu as $posName => $position) {
                $menuFlat[] = [
                    'level' => 1,
                    'attr'  => ['class' => 'toolbar-'.$posName],
                ];
                usort($position['items'], 'sortByWeight');
                foreach ($position['items'] as $menu) {
                    $menuFlat[] = array_merge_recursive([
                        'level' => 2,
                        'attr'  => ['class' => ['toolbar-menu']],
                    ], $menu);

                    if (!isset($menu['items'])) {
                        continue;
                    }

                    foreach ($menu['items'] as $item) {
                        $menuFlat[] = array_merge_recursive([
                            'level' => 3,
                            'attr'  => ['class' => ['toolbar-item']],
                        ], $item);
                    }
                }
            }

            return \H::treeList($menuFlat, function ($item) {
                if (count($item) < 3 && isset($item['attr'])) {
                    return ''; // first level
                }

                if (isset($item['modal'])) {
                    return '<a href="'.$item['url'].'" data-load="modal" data-size="'.$item['modal'].'">'.
                    t($item['title']).'</a>';
                } elseif (isset($item['url'])) {
                    return '<a href="'.$item['url'].'">'.t($item['title']).'</a>';
                } elseif (isset($item['html'])) {
                    return $item['html'];
                } else {
                    return '<span class="tbs">'.t($item['title']).'</span>';
                }
            }, ['id' => 'toolbar']);
        });
    }

    protected function prepareAssets($assets, $type)
    {
        usort($assets, 'sortByWeight');
        $files = [];

        foreach ($assets as $pack) {
            $files = array_merge($files, $pack['files']);
        }

        $files = array_unique($files);

        app('event')->trigger('assets.prepared', [&$files, $type]);

        return $files;
    }
}
