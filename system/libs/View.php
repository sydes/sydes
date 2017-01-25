<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

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
    public function __toString()
    {
        $context = $this->module.'/'.$this->view;
        app('event')->trigger('before.render.view', [&$this->module, &$this->view, &$this->data], $context);

        $file_override = DIR_THEME.'/'.app('site')['theme'].'/modules/'.$this->view.'.php';
        $file = moduleDir($this->module).'/views/'.$this->view.'.php';
        if (file_exists($file_override)) {
            $html = render($file_override, $this->data);
        } elseif (file_exists($file)) {
            $html = render($file, $this->data);
        } else {
            throw new \RuntimeException(sprintf(t('error_file_not_found'), $file));
        }

        app('event')->trigger('after.render.view', [&$html], $context);

        return $html;
    }
}
