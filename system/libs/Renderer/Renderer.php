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

class Renderer
{
    /** @var Document */
    protected $document;
    protected $head;
    protected $footer = [];

    public function render(Document $doc) {
        $this->prepare($doc);
    }

    public function prepare(Document $doc)
    {
        $doc->addJs('jquery', '//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js');
        $doc->addPackage('bootstrap',
            '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
            '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
        $doc->addJs('sydes-core', '/system/assets/js/sydes.js');
        $doc->addPackage('fancybox',
            '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js',
            '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.css');

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
        $this->head = [
            '<title>'.$this->document->title.'</title>',
            //'<base href="'.$this->document->base.'/">',
        ];

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
        //$this->document->addJs('token', "var token = '{$_SESSION['csrf_token']}';"); TODO вставить токен
        $this->document->addJs('extend', '$.extend(syd, '.json_encode($this->document->sydes['js'], JSON_UNESCAPED_UNICODE).');');
        $this->footer[] = '<ul id="notify"></ul>';
        $this->footer[] = '<script>'."\n".implode("\n\n", $this->document->internal_scripts)."\n".'</script>';
    }

}
