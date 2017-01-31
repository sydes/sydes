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

class Base
{
    /** @var Document */
    protected $document;
    protected $head = [];
    protected $footer = [];

    public function render(Document $doc) {
        $this->prepare($doc);
        debug_print_backtrace();
        return 'wut';
    }

    public function prepare(Document $doc)
    {
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
                    ["\n", '{',      '\\',   '"'],
                    ['',   '&#123;', '\\\\', '\"'],
                    $a['message']
                );
                $alerts .= 'syd.alert("'.$a['message'].'", "'.$a['status'].'");';
            }
            $this->document->addJs('alerts', $alerts);
            $_SESSION['alerts'] = [];
        }
        $this->document->data['alerts'] = '<div id="alerts"></div>';
    }

    protected function addNotify()
    {
        if (isset($_SESSION['notify'])) {
            $this->document->addJs('notify',
                'syd.notify("'.$_SESSION['notify']['message'].'", "'.$_SESSION['notify']['status'].'");');
            unset($_SESSION['notify']);
        }
    }

    protected function fillHead() {
        $this->head[] = '<title>'.$this->document->title.'</title>';
        if (app('editor')->isLoggedIn()) {
            $this->document->addCss('toolbar', '/system/assets/css/toolbar.css');
        }

        foreach ($this->document->styles as $pack) {
            foreach ($pack as $file) {
                $this->head[] = '<link rel="stylesheet" href="'.$file.'" media="screen">';
            }
        }
        $this->head[] = empty($this->document->internal_styles) ? '' :
            '<style>'."\n".implode("\n\n", $this->document->internal_styles)."\n".'</style>';
    }

    protected function fillFooter() {
        foreach ($this->document->scripts as $pack) {
            foreach ($pack as $file) {
                $this->footer[] = '<script src="'.$file.'"></script>';
            }
        }
        $this->document->addJsSettings([
            'locale' => app('locale'),
        ]);
        $this->document->addJs('token', "var csrf_name = '".app('csrf')->getTokenName()
            ."', csrf_value = '".app('csrf')->getTokenValue()."';");
        $this->document->addJs('extend',
            '$.extend(syd, '.json_encode($this->document->js_syd, JSON_UNESCAPED_UNICODE).');');
        $this->footer[] = '<ul id="notify"></ul>';
        $this->footer[] = "<script>\n".implode("\n\n", $this->document->internal_scripts)."\n</script>";

        if (app('editor')->isLoggedIn()) {
            $this->footer[] = $this->getToolbar();
        }
    }

    protected function getToolbar()
    {
        $key = 'toolbar.'.md5(json_encode($this->document->context_menu));
        $toolbar = app('cache')->remember($key, function () {
            $menuFlat = [];
            foreach ($this->document->context_menu as $posName => $position) {
                $menuFlat[] = [
                    'level' => 1,
                    'attr' => 'class="toolbar-'.$posName.'"',
                ];
                usort($position['items'], 'sortByWeight');
                foreach ($position['items'] as $menu) {
                    $menuFlat[] = array_merge([
                        'level' => 2,
                        'attr' => 'class="toolbar-menu"',
                    ], $menu);

                    if (!isset($menu['items'])) {
                        continue;
                    }

                    foreach ($menu['items'] as $item) {
                        $menuFlat[] = array_merge([
                            'level' => 3,
                            'attr' => 'class="toolbar-item"',
                        ], $item);
                    }
                }
            }

            return \H::treeList($menuFlat, function ($item) {
                if (count($item) < 3 && isset($item['attr'])) {
                    return '';
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

        return $toolbar;
    }
}
