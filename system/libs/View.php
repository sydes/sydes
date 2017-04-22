<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

class View
{
    public $module;
    public $view;
    public $data;

    /**
     * View constructor.
     *
     * @param string $view module-name/view-name
     * @param array  $data
     */
    public function __construct($view, $data = [])
    {
        $part = explode('/', $view);

        if (count($part) != 2) {
            throw new \InvalidArgumentException(t('error_view_argument'));
        }

        $this->module = $part[0];
        $this->view = $part[1];
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function render()
    {
        $context = $this->module.'/'.$this->view;
        app('event')->trigger('view.render.started', [&$this->module, &$this->view, &$this->data], $context);

        $original = moduleDir($this->module).'/views/'.$this->view.'.php';

        if ($override = model('Themes')->getActive()->getThemedView('module', $this->module, $this->view)) {
            $file = $override;
        } elseif (is_file($original)) {
            $file = $original;
        } else {
            throw new \RuntimeException(t('error_file_not_found', ['file' => $original]));
        }
        $html = render($file, $this->data);

        app('event')->trigger('view.render.ended', [&$html], $context);

        return $html;
    }
}
