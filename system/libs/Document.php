<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

class Document
{
    public $data;
    public $title = 'SyDES';
    public $base = '';
    public $meta = [];
    public $links = [];
    public $js = [];
    public $scripts = [];
    public $css = [];
    public $styles = [];

    public $context_menu = ['left' => ['weight' => 0, 'items' => []], 'right' => ['weight' => 2, 'items' => []]];
    public $js_syd = ['l10n' => [], 'settings' => []];

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Adds a js file by url
     *
     * @param string       $key
     * @param string|array $files
     * @param int          $weight
     * @return $this
     */
    public function addJs($key, $files, $weight = 300)
    {
        $this->js[$key] = [
            'files'  => array_values((array)$files),
            'weight' => $weight,
        ];

        return $this;
    }

    /**
     * Adds a string with js code
     *
     * @param string $key
     * @param string $code
     * @return $this
     */
    public function addScript($key, $code)
    {
        $this->scripts[$key] = $code;

        return $this;
    }

    /**
     * Removes a script
     *
     * @param string $key
     * @return $this
     */
    public function removeJs($key)
    {
        unset($this->js[$key], $this->scripts[$key]);

        return $this;
    }

    /**
     * Adds a css file by url
     *
     * @param string       $key
     * @param string|array $files
     * @param int          $weight
     * @return $this
     */
    public function addCss($key, $files, $weight = 300)
    {
        $this->css[$key] = [
            'files'  => array_values((array)$files),
            'weight' => $weight,
        ];

        return $this;
    }

    /**
     * Adds a string with css code
     *
     * @param string $key
     * @param string $code
     * @return $this
     */
    public function addStyle($key, $code)
    {
        $this->styles[$key] = $code;

        return $this;
    }

    /**
     * Removes a style
     *
     * @param string $key
     * @return $this
     */
    public function removeCss($key)
    {
        unset($this->css[$key], $this->styles[$key]);

        return $this;
    }

    /**
     * Adds a style and script
     *
     * @param              $key
     * @param string|array $js
     * @param string|array $css
     * @param int          $weight
     * @return $this
     */
    public function addPackage($key, $js = [], $css = [], $weight = 300)
    {
        return $this->addJs($key, $js, $weight)->addCss($key, $css, $weight);
    }

    /**
     * Removes a style and script
     *
     * @param string $key
     * @return $this
     */
    public function removePackage($key)
    {
        return $this->removeJs($key)->removeCss($key);
    }

    /**
     * Adds a link
     *
     * @param string $key   For removing
     * @param array  $attrs ['rel' => '...', 'href' => '...', 'type' => '...']
     * @return $this|null
     */
    public function addLink($key, array $attrs)
    {
        if (!isset($attrs['href'])) {
            throw new \InvalidArgumentException(t('error_document_addlink_href'));
        }
        $this->links[$key] = $attrs;
        return $this;
    }

    /**
     * Removes a link
     *
     * @param string $key
     * @return $this
     */
    public function removeLink($key)
    {
        unset($this->links[$key]);
        return $this;
    }

    /**
     * Adds array of translation strings to js modules
     *
     * @param array $array
     * @return $this
     */
    public function addJsL10n($array)
    {
        $this->js_syd['l10n'] = array_merge($this->js_syd['l10n'], $array);
        return $this;
    }

    /**
     * Adds some settings for js modules
     *
     * @param array $array
     * @return $this
     */
    public function addJsSettings($array)
    {
        $this->js_syd['settings'] = array_merge($this->js_syd['settings'], $array);
        return $this;
    }

    public function addCsrfToken($name, $value)
    {
        $this->js_syd['csrf'] = [
            'name' => $name,
            'value' => $value,
        ];
        return $this;
    }

    /**
     * @param string $position
     * @param string $name
     * @param array  $params
     * @return $this
     */
    public function addContextMenu($position, $name, $params)
    {
        $this->context_menu[$position]['items'][$name] = $params;
        return $this;
    }

    /**
     * @param $position
     * @param $name
     * @param $params
     * @return $this
     */
    public function addContextMenuItem($position, $name, $params)
    {
        $this->context_menu[$position]['items'][$name]['items'][] = $params;
        return $this;
    }

    /**
     * @param string $position
     * @param string $name
     * @return array
     */
    public function getContextMenu($position, $name)
    {
        return $this->context_menu[$position]['items'][$name];
    }

    /**
     * @param string $position
     * @param string $name
     * @return $this
     */
    public function removeContextMenu($position, $name)
    {
        unset($this->context_menu[$position]['items'][$name]);
        return $this;
    }
}
