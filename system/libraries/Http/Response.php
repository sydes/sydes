<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App\Http;

class Response
{

    /**
     * Mime types translation table.
     *
     * @var array
     */
    public static $mimeTypes = [
        'binary' => 'application/octet-stream',
        'css'    => 'text/css',
        'csv'    => 'application/vnd.ms-excel',
        'doc'    => 'application/msword',
        'html'   => 'text/html',
        'json'   => 'application/json',
        'js'     => 'application/x-javascript',
        'txt'    => 'text/plain',
        'rss'    => 'application/rss+xml',
        'atom'   => 'application/atom+xml',
        'zip'    => 'application/zip',
        'pdf'    => 'application/pdf',
        'xls'    => 'application/vnd.ms-excel',
        'gtar'   => 'application/x-gtar',
        'gzip'   => 'application/x-gzip',
        'tar'    => 'application/x-tar',
        'xhtml'  => 'application/xhtml+xml',
        'rtf'    => 'text/rtf',
        'xsl'    => 'text/xml',
        'xml'    => 'text/xml',
    ];

    protected $content;
    protected $statusCode;
    protected $headers;
    protected $cookies = [];
    protected $mime;

    /**
     * HttpResponse constructor.
     *
     * @param string $content
     * @param int    $statusCode
     * @param array  $headers
     */
    public function __construct($content = '', $statusCode = 200, $headers = [])
    {
        $this->withContent($content);
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Set the content on the response.
     *
     * @param  mixed $content
     * @return self
     */
    public function withContent($content)
    {
        if (is_array($content)) {
            $content = json_encode($content);
            $this->mime = 'json';
        } elseif ($content instanceof \App\Document) {
            app('event')->trigger('before.render', $content, app('request')->url);
            $content = app('renderer')->render($content);
            app('event')->trigger('after.render', $content);
        }
        $this->content = (string)$content;
        return $this;
    }

    /**
     * Set status code.
     *
     * @param $code
     */
    public function withStatus($code)
    {
        $this->statusCode = $code;
    }

    /**
     * Sends HTTP headers and content.
     */
    public function send()
    {
        if (is_null($this->mime)) {
            $this->mime = 'html';
        }
        $this->addHeader('Content-type', self::$mimeTypes[$this->mime]);

        $this->sendHeaders();
        echo $this->content;
    }

    /**
     * Sets a header by name.
     *
     * @param string       $key    The key
     * @param string|array $values The value or an array of values
     * @return self
     */
    public function addHeader($key, $values)
    {
        $values = array_values((array)$values);
        if (!isset($this->headers[$key])) {
            $this->headers[$key] = $values;
        } else {
            $this->headers[$key] = array_merge($this->headers[$key], $values);
        }
        return $this;
    }

    /**
     * Sends HTTP headers.
     */
    public function sendHeaders()
    {
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
     * Removes a header.
     *
     * @param string $key The HTTP header name
     * @return self
     */
    public function removeHeader($key)
    {
        unset($this->headers[$key]);
        return $this;
    }

    /**
     * Sets a cookie
     *
     * @param string $name
     * @param string $value
     * @param int    $expire Ttl in seconds
     * @return self
     */
    public function addCookie($name, $value, $expire)
    {
        $this->cookies[] = [
            'name'   => $name,
            'value'  => $value,
            'expire' => $expire,
        ];
        return $this;
    }

    /**
     * Removes a cookie.
     *
     * @param string $name The cookie name
     * @return self
     */
    public function removeCookie($name)
    {
        $this->cookies[] = [
            'name'   => $name,
            'expire' => -2,
        ];
        return $this;
    }

    /**
     * Sets a content type of response
     *
     * @param string $type
     * @return self
     */
    public function withMime($type)
    {
        $this->mime = in_array($type, self::$mimeTypes) ? $type : 'txt';
        return $this;
    }

    /**
     * Create a new file download response.
     *
     * @param string $filename
     * @return self
     */
    public function download($filename)
    {
        if (is_null($this->mime)) {
            $this->mime = 'binary';
        }
        $this->addHeader('Content-Disposition', 'attachment; filename="'.toSlug($filename, false).'"');
        return $this;
    }

    /**
     * Prepares the response object to return an HTTP Redirect response.
     *
     * @param string $url
     * @param int    $status
     * @return self
     */
    public function withRedirect($url, $status = 301)
    {
        $this->statusCode = $status;
        $this->addHeader('Location', $url);
        return $this;
    }

}
