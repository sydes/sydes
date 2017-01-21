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
        $this->document->addJs('extend', '$.extend(syd, '.json_encode($this->document->sydes['js'], JSON_UNESCAPED_UNICODE).');');
        $this->footer[] = '<ul id="notify"></ul>';
        $this->footer[] = '<script>'."\n".implode("\n\n", $this->document->internal_scripts)."\n".'</script>';

        if (app('user')->isEditor()) {
            $this->footer[] = $this->getToolbar();
        }
    }

    protected function getToolbar()
    {
        /*$menu = [];
        foreach ($this->document->sydes['context_menu'] as $key => $data) {
            $menu[$key]['title'] = $data['title'];
            $menu[$key]['link'] = $data['link'];
            foreach ($data['children'] as $child) {
                $modal = '';
                if ($child['modal']) {
                    $size = '';
                    if ($child['modal'] === 'small') {
                        $size = 'data-size="sm"';
                    } elseif ($child['modal'] === 'large') {
                        $size = 'data-size="lg"';
                    }
                    $modal = 'data-toggle="modal" data-target="#modal" '.$size.' ';
                }
                $menu[$key]['children'][] = '<a '.$modal.'href="'.$child['link'].'">'.$child['title'].'</a>';
            }
        }

        return render(DIR_SYSTEM.'/views/toolbar.php', [
            'menu'        => $menu,
            'request_uri' => app('request')->getUri()->getPath(),
        ]);*/

        $this->document->addContextMenu('left', 'brand_link', [
            'weight' => 0,
            'title' => 'Administration',
            'link' => '/admin'
        ]);

        $this->document->addContextMenu('right', 'profile', [
            'weight' => 0,
            'title' => 'ArtyGrand',
            'children' => [
                'profile' => [
                    'title' => 'Profile',
                    'link' => '/admin/profile',
                ],
                'logout' => [
                    'html' => '<strong>Logout form<strong>',
                ]
            ]
        ]);

        $this->document->addContextMenu('right', 'support', [
            'weight' => 10,
            'title' => 'Support',
            'link' => '//sydes.ru/ru/docs/v2/iblocks',
            'modal' => 'lg'
        ]);

        return pre($this->document->sydes['context_menu'], true);
    }

}
