<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App;

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
        'xml' => 'text/xml',
        'binary' => 'application/octet-stream',
    ];

    /**
     * Response content.
     *
     * @var string
     */
    public $content;

    private $statusCode;
    private $headers;
    private $cookies;
    private $mime;

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
        if (is_null($this->mime)){
            $this->mime = 'html';
        }
        $this->addHeader('Content-type', self::$mimeTypes[$this->mime]);

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

    /**
     * Sets a header by name.
     *
     * @param string       $key    The key
     * @param string|array $values The value or an array of values
     * @return \HttpResponse
     */
    public function addHeader($key, $values) {
        $values = array_values((array) $values);
        if (!isset($this->headers[$key])) {
            $this->headers[$key] = $values;
        } else {
            $this->headers[$key] = array_merge($this->headers[$key], $values);
        }
        return $this;
    }

    /**
     * Removes a header.
     *
     * @param string $key The HTTP header name
     * @return \HttpResponse
     */
    public function removeHeader($key) {
        unset($this->headers[$key]);
        return $this;
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
     * Sets a content type of response
     *
     * @param string $type
     * @return \HttpResponse
     */
    public function withMime($type) {
        $this->mime = in_array($type, self::$mimeTypes) ? $type : 'txt';
        return $this;
    }

    /**
     * Create a new file download response.
     *
     * @param type $filename
     * @return \HttpResponse
     */
    public function download($filename) {
        if (is_null($this->mime)){
            $this->mime = 'binary';
        }
        $this->addHeader('Content-Disposition', 'attachment; filename="'.toSlug($filename, false).'"');
        return $this;
    }

}
