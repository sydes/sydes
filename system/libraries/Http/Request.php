<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App\Http;

class Request {

    /**
     * Query string parameters ($_GET).
     *
     * @var array
     */
    public $query;

    /**
     * Request body parameters ($_POST).
     *
     * @var array
     */
    public $request;

    /**
     * Cookies ($_COOKIE).
     *
     * @var array
     */
    public $cookies;

    /**
     * Uploaded files ($_FILES).
     *
     * @var array
     */
    public $files;

    /**
     * Server and execution environment parameters ($_SERVER).
     *
     * @var array
     */
    public $server;

    /**
     * Headers (taken from the $_SERVER).
     *
     * @var array
     */
    public $headers;

    /**
     * List of languages acceptable by the client browser.
     *
     * @var array
     */
    public $languages;

    /**
     * Gets the request's scheme.
     *
     * @var string
     */
    public $scheme = 'http';

    /**
     * The request method.
     *
     * @var string
     */
    public $method;

    /**
     * The domain.
     *
     * @var string
     */
    public $domain;

    /**
     * The requested URI (path and query string).
     *
     * @var string
     */
    public $requestUri;

    /**
     * The URL without query string.
     *
     * @var string
     */
    public $url;

    /**
     * Gets client ip.
     *
     * @var string
     */
    public $ip;

    /**
     * Was the request made by POST?.
     *
     * @var bool
     */
    public $is_post = false;

    /**
     * Returns true if the request is a XMLHttpRequest.
     *
     * @var bool
     */
    public $is_ajax = false;

    /**
     * Checks whether the request is secure or not.
     *
     * @var bool
     */
    public $is_secure = false;

    public function __construct(array $query = array(), array $request = array(), array $cookies = array(),
                                array $files = array(), array $server = array()) {
        if (!isset($server['HTTP_HOST'])) {
            header('HTTP/1.1 400 Bad Request');
            die;
        }

        $this->query = $query;
        $this->request = $request;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->server = $server;
        $this->headers = $this->getHeaders($server);

        $this->ip = $server['REMOTE_ADDR'];
        $this->method = $server['REQUEST_METHOD'];
        $this->domain = $server['HTTP_HOST'];
        $this->requestUri = $server['REQUEST_URI'];

        if ($pos = strpos($server['REQUEST_URI'], '?')) {
            $this->url = substr($server['REQUEST_URI'], 0, $pos);
        } else {
            $this->url = $server['REQUEST_URI'];
        }

        if ($this->method == 'POST') {
            $this->is_post = true;
        }

        if (isset($this->headers['X_REQUESTED_WITH']) &&
                strtolower($this->headers['X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->is_ajax = true;
        }

        if ((!empty($server['HTTPS']) && $server['HTTPS'] !== 'off') ||
                (!empty($server['SERVER_PORT']) && $server['SERVER_PORT'] == 443) ||
                (!empty($server['HTTP_X_FORWARDED_PROTO']) && $server['HTTP_X_FORWARDED_PROTO'] == 'https')) {
            $this->scheme = 'https';
            $this->is_secure = true;
        }
    }

    /**
     * Create a new HTTP request from PHP's super globals.
     *
     * @return static
     */
    public static function capture() {
        return new static($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
    }

    /**
     * Gets a "parameter" value from request.
     *
     * @param string $key     the key
     * @param mixed  $default the default value
     * @return mixed
     */
    public function get($key, $default = null) {
        if (isset($this->query[$key])) {
            return $this->query[$key];
        } elseif (isset($this->request[$key])) {
            return $this->request[$key];
        } else {
            return $default;
        }
    }

    /**
     * Determine if the request contains a non-empty value for an input item.
     *
     * @param string|array $key
     * @return bool
     */
    public function has($key) {
        $keys = is_array($key) ? $key : func_get_args();

        foreach ($keys as $value) {
            if ($this->isEmptyString($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the given input key is an empty string for "has".
     *
     * @param  string $key
     * @return bool
     */
    protected function isEmptyString($key) {
        $value = $this->get($key);
        $boolOrArray = is_bool($value) || is_array($value);
        return !$boolOrArray && trim((string) $value) === '';
    }

    /**
     * Gets a required "parameter" value from request or throws Exception.
     *
     * @param string $key the key
     * @return mixed
     * @throws \RuntimeException
     */
    public function getRequired($key) {
        $value = $this->get($key);
        if (is_null($value) || ($value === '')) {
            throw new \RuntimeException(sprintf(t('error_parameter_required'), $key));
        }
    }

    /**
     * Get a subset of the items from the input data.
     *
     * @param  array|mixed $keys
     * @return array
     */
    public function only($keys) {
        $keys = is_array($keys) ? $keys : func_get_args();
        $results = [];
        $input = $this->all();
        foreach ($keys as $key) {
            $results[$key] = isset($input[$key]) ? $input[$key] : null;
        }
        return $results;
    }

    /**
     * Get all of the input for the request.
     *
     * @return array
     */
    public function all() {
        return array_replace_recursive($this->request, $this->query);
    }

    /**
     * Gets the HTTP headers.
     *
     * @param array $server
     * @return array
     */
    private function getHeaders($server) {
        $headers = [];
        $contentHeaders = ['CONTENT_LENGTH' => 1, 'CONTENT_MD5' => 1, 'CONTENT_TYPE' => 1];
        foreach ($server as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } elseif (isset($contentHeaders[$key])) {
                $headers[$key] = $value;
            }
        }
        return $headers;
    }

    /**
     * Gets a list of languages acceptable by the client browser.
     *
     * @return array Languages ordered in the user browser preferences
     */
    public function getLanguages() {
        if (!is_null($this->languages)) {
            return $this->languages;
        }

        if (empty($this->headers['ACCEPT_LANGUAGE'])){
            return $this->languages = ['en'];
        }

        if (preg_match_all('/([a-z*]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/i', $this->headers['ACCEPT_LANGUAGE'], $list)) {
            $langs = array_combine($list[1], $list[2]);
            foreach ($langs as $lang => $weight) {
                if ($lang == '*') {
                    continue;
                }
                if (false !== strpos($lang, '-')) {
                    $codes = explode('-', $lang);
                    for ($i = 0, $max = count($codes); $i < $max; ++$i) {
                        if ($i === 0) {
                            $lang = strtolower($codes[0]);
                        } else {
                            $lang .= '_'.strtoupper($codes[$i]);
                        }
                    }
                }
                $languages[$lang] = $weight ? $weight : 1;
            }
            arsort($languages, SORT_NUMERIC);
            $this->languages = array_keys($languages);
        }
        return $this->languages;
    }

    /**
     * Returns the preferred language.
     *
     * @param array $locales An array of ordered available locales
     * @return string|null The preferred locale
     */
    public function getPreferredLanguage(array $locales = null) {
        $prefLangs = $this->getLanguages();
        if (empty($locales)) {
            return isset($prefLangs[0]) ? $prefLangs[0] : null;
        }
        if (!$prefLangs) {
            return $locales[0];
        }
        $extPrefLangs = [];
        foreach ($prefLangs as $language) {
            $extPrefLangs[] = $language;
            if (false !== $position = strpos($language, '_')) {
                $superLanguage = substr($language, 0, $position);
                if (!in_array($superLanguage, $prefLangs)) {
                    $extPrefLangs[] = $superLanguage;
                }
            }
        }
        $prefLangs = array_values(array_intersect($extPrefLangs, $locales));
        return isset($prefLangs[0]) ? $prefLangs[0] : $locales[0];
    }

}
