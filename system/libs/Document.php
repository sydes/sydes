<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

class Document
{
    public $data;
    public $title = 'SyDES';
    public $base = '';
    public $meta = [];
    public $links = [];
    public $scripts = [];
    public $internal_scripts = [];
    public $styles = [];
    public $internal_styles = [];

    public $context_menu = ['left' => ['weight' => 0, 'items' => []], 'right' => ['weight' => 2, 'items' => []]];
    public $js_syd = ['l10n' => [], 'settings' => []];

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Adds a script by url or raw string
     *
     * @param string       $key    Name of script
     * @param string|array $source Raw string|Absolute or relative paths
     * @return $this
     */
    public function addJs($key, $source)
    {
        if (is_string($source) && !preg_match('!^(http|/)!', $source)) {
            $this->internal_scripts[$key] = $source;
        } else {
            $paths = array_values((array)$source);
            $this->scripts[$key] = $paths;
        }
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
        unset($this->scripts[$key], $this->internal_scripts[$key]);
        return $this;
    }

    /**
     * Adds a style by url or raw string
     *
     * @param string       $key    Name of style
     * @param string|array $source Raw string|Absolute or relative paths
     * @return $this
     */
    public function addCss($key, $source)
    {
        if (is_string($source) && !preg_match('!^(http|/)!', $source)) {
            $this->internal_styles[$key] = $source;
        } else {
            $paths = array_values((array)$source);
            $this->styles[$key] = $paths;
        }
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
        unset($this->styles[$key], $this->internal_styles[$key]);
        return $this;
    }

    /**
     * Adds a style and script
     *
     * @param              $key
     * @param string|array $js
     * @param string|array $css
     * @return $this
     */
    public function addPackage($key, $js = [], $css = [])
    {
        return $this->addJs($key, $js)->addCss($key, $css);
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

    /**
     * Gets title and other meta tags from data array
     *
     * @return $this
     */
    public function findMetaTags()
    {
        if (isset($this->data['meta_title'])) {
            $this->title = $this->data['meta_title'];
            unset($this->data['meta_title']);
        } elseif (isset($this->data['title'])) {
            $this->title = $this->data['title'];
        }

        foreach ($this->data as $key => $value) {
            if (substr($key, 0, 5) != 'meta_') {
                continue;
            }
            $this->meta[substr($key, 5)] = $value;
            unset($this->data[$key]);
        }
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
