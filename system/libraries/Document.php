<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
class Document {

    public $data = [];
    public $title;
    public $base;
    public $meta = [];
    public $notify;
    public $alerts = [];
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
     */
    public function notify($message, $status = 'success') {
        $this->notify = [
            'message' => $message,
            'status' => $status
        ];
    }

    /**
     * Adds a alert message.
     *
     * @param string $message
     * @param string $status Any of bootstrap alert statuses
     */
    public function alert($message, $status = 'success') {
        $this->alerts[] = [
            'message' => $message,
            'status' => $status
        ];
    }

    /**
     * Adds a script by url or raw string
     *
     * @param string       $key    Name of script
     * @param string|array $source Raw string|Absolute or relative paths
     */
    public function addScript($key, $source) {
        if (is_string($source) && !preg_match('!^(http|/)!', $source)){
            $this->internal_scripts[$key] = $source;
        } else {
            $paths = array_values((array) $source);
            $this->scripts[$key] = $paths;
        }
    }

    /**
     * Removes a script
     *
     * @param string $key
     */
    public function removeScript($key) {
        unset($this->scripts[$key], $this->internal_scripts[$key]);
    }

    /**
     * Adds a style by url or raw string
     *
     * @param string       $key    Name of style
     * @param string|array $source Raw string|Absolute or relative paths
     */
    public function addStyle($key, $source) {
        if (is_string($source) && !preg_match('!^(http|/)!', $source)){
            $this->internal_styles[$key] = $source;
        } else {
            $paths = array_values((array) $source);
            $this->styles[$key] = $paths;
        }
    }

    /**
     * Removes a style
     *
     * @param string $key
     */
    public function removeStyle($key) {
        unset($this->styles[$key], $this->internal_styles[$key]);
    }

    /**
     * Adds array of translation strings to js modules
     * 
     * @param array $array
     */
    public function addJsL10n($array) {
        $this->js['l10n'] = array_merge($this->js['l10n'], $array);
    }

    /**
     * Adds some settings for js modules
     *
     * @param array $array
     */
    public function addJsSettings($array) {
        $this->js['settings'] = array_merge($this->js['settings'], $array);
    }

    /**
     * Gets title and other meta tags from data array
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
    }

}
