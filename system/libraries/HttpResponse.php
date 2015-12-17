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
     * Status codes translation table.
     *
     * @var array
     */
    public static $statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Request Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );

    /**
     * Mime types translation table.
     *
     * @var array
     */
    public static $mimeTypes = array(
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
    );

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
    public $status;

    public function __construct($content = '', $status = 200, $headers = array(), $cookies = array()) {
        $this->content = $content;
        $this->status = $status . ' ' . self::$statusTexts[$status];
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
        header('HTTP/1.0 ' . $this->status);
        // headers
        foreach ($this->headers as $name => $values) {
            foreach ($values as $value) {
                header($name . ': ' . $value, false);
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
