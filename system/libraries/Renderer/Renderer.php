<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App\Renderer;

use App\Document;

class Renderer
{
    /**
     * @var Document
     */
    protected $document;
    protected $head;
    protected $footer = [];

    public function prepare(Document $doc)
    {
        $doc->addScript('jquery-1.11', '//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js');
        $doc->addScript('bootstrap-3.3', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js');
        $doc->addScript('sydes-core', '/system/assets/js/sydes.js');
        $doc->addStyle('bootstrap-3.3', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');

        $this->document = $doc;
        $this->addAlerts();
        $this->addNotify();

        $this->head = [
            '<title>'.$doc->title.'</title>',
            '<base href="http://'.app('base').'/">',
        ];
    }

    protected function addAlerts()
    {
        if (!empty($_SESSION['alerts'])) {
            foreach ($_SESSION['alerts'] as $a) {
                $this->document->addScript('alerts', 'syd.alert('.json_encode($a['message']).', '.json_encode($a['status']).');');
            }
            $_SESSION['alerts'] = [];
        }
        $this->document->data['alerts'] = '<div id="alerts"></div>';
    }

    protected function addNotify()
    {
        if (isset($_SESSION['notify'])) {
            $this->document->addScript('notify', 'syd.notify('.json_encode($_SESSION['notify']['message'])
                .', '.json_encode($_SESSION['notify']['status']).');');
            unset($_SESSION['notify']);
        }
    }

    protected function fillHead() {
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
            'locale' => app('contentLang'),
        ]);
        $this->document->addScript('extend', '$.extend(syd, '.json_encode($this->document->js).');');
        $this->footer[] = '<ul id="notify"></ul>';
        $this->footer[] = '<script>'."\n".implode("\n\n", $this->document->internal_scripts)."\n".'</script>';
    }

}
