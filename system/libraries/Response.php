<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
class Response {

    public $data = [];
    public $notify;
    public $alerts = [];
    public $styles = [];
    public $scripts = [];
    public $context_menu = [];
    public $headers = [];
    public $js = ['l10n' => [], 'settings' => []];
    public $body;
    public $status = 200;
    public $mime = 'html';
    public $cookies = [];

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
     * Sets a header by name.
     *
     * @param string       $key    The key
     * @param string|array $values The value or an array of values
     */
    public function addHeader($key, $values) {
        $values = array_values((array) $values);
        if (!isset($this->headers[$key])) {
            $this->headers[$key] = $values;
        } else {
            $this->headers[$key] = array_merge($this->headers[$key], $values);
        }
    }

    /**
     * Removes a header.
     *
     * @param string $key The HTTP header name
     */
    public function removeHeader($key) {
        unset($this->headers[$key]);
    }

    /**
     * Sets a cookie
     *
     * @param string $name
     * @param string $value
     * @param int    $expire Ttl in seconds
     */
    public function addCookie($name, $value, $expire) {
        $this->cookies[] = [
            'name' => $name,
            'value' => $value,
            'expire' => $expire
        ];
    }

    /**
     * Removes a cookie.
     *
     * @param string $name The cookie name
     */
    public function removeCookie($name) {
        $this->cookies[] = [
            'name' => $name,
            'expire' => -2
        ];
    }

    /**
     * Adds a script
     *
     * @param string|array $path Absolute or relative paths
     */
    public function addScript($path) {
        $paths = array_values((array) $path);
        $this->scripts = array_merge($this->scripts, array_combine($paths, $paths));
    }

    /**
     * Removes a script
     *
     * @param string|array $path Absolute or relative paths
     */
    public function removeScript($path) {
        unset($this->scripts[$path]);
    }

    /**
     * Adds a style
     *
     * @param string|array $path Absolute or relative paths
     */
    public function addStyle($path) {
        $paths = array_values((array) $path);
        $this->styles = array_merge($this->styles, array_combine($paths, $paths));
    }

    /**
     * Removes a style
     *
     * @param string|array $path Absolute or relative paths
     */
    public function removeStyle($path) {
        unset($this->styles[$path]);
    }

    public function addJsL10n($array) {
        $this->js['l10n'] = array_merge($this->js['l10n'], $array);
    }

    public function addJsSettings($array) {
        $this->js['settings'] = array_merge($this->js['settings'], $array);
    }

    public function redirect($url = '') {
        if (!empty($this->alerts)) {
            $_SESSION['alerts'] = $this->alerts;
            unset($this->alerts);
        }

        if (app('request')->is_ajax) {
            $this->body['redirect'] = $url;
        } else {
            $host = $_SERVER['HTTP_HOST'].'/';
            $this->addHeader('Location', app('request')->scheme.'://'.$host.$url);
            $this->status = 301;
        }

        return $this;
    }

    public function reload() {
        $this->body['reload'] = 1;
    }

    public function send() {
        if (!empty($this->notify)) {
            if (app('request')->is_ajax) {
                $this->body['notify'] = $this->notify;
            } else {
                $this->addCookie('notify.message', $this->notify['message'], 3);
                $this->addCookie('notify.status', $this->notify['status'], 3);
            }
        }

        if (is_array($this->body)) {
            $this->mime = 'json';
        }

        $content = is_array($this->body) ? json_encode($this->body) : $this->body;

        $this->addHeader('Content-type', HttpResponse::$mimeTypes[$this->mime]);

        $response = new HttpResponse($content, $this->status, $this->headers, $this->cookies);
        $response->send();
        die;
    }

}
