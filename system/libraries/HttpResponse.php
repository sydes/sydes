<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
class HttpResponse {

    /**
     * Mime types translation table.
     *
     * @var array
     */
    public static $mimeTypes = [
        'css' => 'text/css',
        'csv' => 'application/vnd.ms-excel',
        'doc' => 'application/msword',
        'html' => 'text/html',
        'json' => 'application/json',
        'js' => 'application/x-javascript',
        'txt' => 'text/plain',
        'rss' => 'application/rss+xml',
        'atom' => 'application/atom+xml',
        'zip' => 'application/zip',
        'pdf' => 'application/pdf',
        'xls' => 'application/vnd.ms-excel',
        'gtar' => 'application/x-gtar',
        'gzip' => 'application/x-gzip',
        'tar' => 'application/x-tar',
        'xhtml' => 'application/xhtml+xml',
        'rtf' => 'text/rtf',
        'xsl' => 'text/xml',
        'xml' => 'text/xml'
    ];

    /**
     * Response headers.
     *
     * @var array
     */
    public $headers;

    /**
     * Response cookies.
     *
     * @var array
     */
    public $cookies;

    /**
     * Response content.
     *
     * @var string
     */
    public $content;

    /**
     * Status code for the current web response.
     *
     * @var string
     */
    public $statusCode;

    public function __construct($content = '', $statusCode = 200, $headers = [], $cookies = []) {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->cookies = $cookies;
    }

    /**
     * Sends HTTP headers and content.
     *
     * @return void
     */
    public function send() {
        $this->sendHeaders();
        echo $this->content;
    }

    /**
     * Sends HTTP headers.
     *
     * @return void
     */
    public function sendHeaders() {
        if (headers_sent()) {
            return;
        }
        // status
        http_response_code($this->statusCode);
        // headers
        foreach ($this->headers as $name => $values) {
            foreach ($values as $value) {
                header($name.': '.$value, false);
            }
        }
        // cookies
        foreach ($this->cookies as $cookie) {
            $value = isset($cookie['value']) ? $cookie['value'] : '';
            $expire = isset($cookie['expire']) ? time() + $cookie['expire'] : 0;
            setcookie($cookie['name'], $value, $expire, '/');
        }
    }

}
