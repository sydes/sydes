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
    protected $head = [];
    protected $footer = [];

    public function render(Document $doc) {
        $this->prepare($doc);
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
    }

}
