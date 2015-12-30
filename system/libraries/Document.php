<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App;

class Document {

    public $data = [];
    public $title;
    public $base;
    public $meta = [];
    public $notify;
    public $alerts = [];
    public $links = [];
    public $scripts = [];
    public $internal_scripts = [];
    public $styles = [];
    public $internal_styles = [];
    public $context_menu = [];
    public $js = ['l10n' => [], 'settings' => []];
    public $language = 'en';
    public $csrf_token;

    public function __construct() {
        if (isset($_SESSION['alerts'])) {
            $this->alerts = $_SESSION['alerts'];
            unset($_SESSION['alerts']);
        }
    }

    /**
     * Sets a notify message.
     *
     * @param string $message
     * @param string $status Any of bootstrap alert statuses
     * @return $this
     */
    public function notify($message, $status = 'success') {
        $this->notify = [
            'message' => $message,
            'status' => $status
        ];
        return $this;
    }

    /**
     * Adds a alert message.
     *
     * @param string $message
     * @param string $status Any of bootstrap alert statuses
     * @return $this
     */
    public function alert($message, $status = 'success') {
        $this->alerts[] = [
            'message' => $message,
            'status' => $status
        ];
        return $this;
    }

    /**
     * Adds a script by url or raw string
     *
     * @param string       $key    Name of script
     * @param string|array $source Raw string|Absolute or relative paths
     * @return $this
     */
    public function addScript($key, $source) {
        if (is_string($source) && !preg_match('!^(http|/)!', $source)){
            $this->internal_scripts[$key] = $source;
        } else {
            $paths = array_values((array) $source);
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
    public function removeScript($key) {
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
    public function addStyle($key, $source) {
        if (is_string($source) && !preg_match('!^(http|/)!', $source)){
            $this->internal_styles[$key] = $source;
        } else {
            $paths = array_values((array) $source);
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
    public function removeStyle($key) {
        unset($this->styles[$key], $this->internal_styles[$key]);
        return $this;
    }

    /**
     * Adds a link
     *
     * @param string $key   For removing
     * @param array  $attrs ['rel' => '...', 'href' => '...', 'type' => '...']
     * @return $this|null
     */
    public function addLink($key, $attrs) {
        if (!isset($attrs['href'])){
            return;
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
    public function removeLink($key) {
        unset($this->links[$key]);
        return $this;
    }

    /**
     * Adds array of translation strings to js modules
     * 
     * @param array $array
     * @return $this
     */
    public function addJsL10n($array) {
        $this->js['l10n'] = array_merge($this->js['l10n'], $array);
        return $this;
    }

    /**
     * Adds some settings for js modules
     *
     * @param array $array
     * @return $this
     */
    public function addJsSettings($array) {
        $this->js['settings'] = array_merge($this->js['settings'], $array);
        return $this;
    }

    /**
     * Gets title and other meta tags from data array
     *
     * @return $this
     */
    public function findMetaTags() {
        if (isset($this->data['meta_title'])){
            $this->title = $this->data['meta_title'];
            unset($this->data['meta_title']);
        } else {
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

}
